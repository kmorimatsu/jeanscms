<?php
/*
 * Jeans CMS (GPL license)
 * $Id: misc_gd.php 311 2010-11-17 04:23:09Z sakamocchi $
 */

class misc_gd extends jeans {
	static public function init(){
	}
	
	/*
	 * Following method makes GD image format (version 1)
	 * from the bitmap image binary (either Microsoft or IBM format).
	 * 
	 * @access	Public	Media:imagecreatefrombmp
	 * @param	char	fullpath to bitmap
	 * 
	 * Maintained by Takashi Sakamoto (o-takashi@sakamocchi.jp)
	 * 
	 * Refer the Bitmaps (Windows) page on The Microsoft Windows graphics device interface (GDI)
	 * about the format of bitmap image.
	 * http://msdn.microsoft.com/en-us/library/dd183377%28v=VS.85%29.aspx
	 * 
	 * The reference about OS/2 (IBM) could not be found, so the code for this format was constructed
	 * by "try and error" way.
	 * 
	 * Note; bitmap: little endian; gd: big endian
	 */
	static public function imagecreatefrombmp($path) {
		// Check if GD is available
		if (!function_exists('imagecreatefromgd'))
			return false;
		
		// General arrays used to check the format type	
		static $header_types = array(
			12	=> 'BITMAPCOREHEADER',	// BITMAPCOREHEADER for Windows and OS/2
			40	=> 'BITMAPINFOHEADER',	// BITMAPINFOHEADER for Windows
			64	=> 'BITMAPINFOHEADER2',	// BITMAPINFOHEADER2 for OS/2
			108	=> 'BITMAPV4HEADER',	// BITMAPV4HEADER for Windows
			124	=> 'BITMAPV5HEADER',	// BITMAPV5HEADER for Windows
		);
		
		static $bitcnts = array(0, 1, 4, 8, 16, 24, 32);
		
		static $compress_types = array(
			0	=> 'BI_RGB',
			1	=> 'BI_RLE8',
			2	=> 'BI_RLE4',
			3	=> 'BI_BITFIELDS',
			4	=> 'BI_JPEG',
			5	=> 'BI_PNG'
		);
		
		if(($binary = fopen($path, "rb")) === FALSE) {
			fclose($binary);
			return FALSE;
		}
		
		// Get BITMAPFILEHEADER information and confirm if it's really bitmap image
		list($first, $second) = array_merge(unpack("C2", fread($binary, 2)));
		$signature = chr($first) . chr($second);
		if ($signature === "BM") {
			$vendor='MICROSOFT';
		} else if (in_array($signature, array("BA", "CI", "CP", "IC", "PT"))) {
			$vendor='IBM';
		} else {
			fclose($binary);
			return FALSE;
		}
		
		// Get the offset from the position of image information
		fseek($binary, 10, SEEK_SET);
		list($offbits) = array_merge(unpack("V", fread($binary, 4)));
		
		// Get the size of pixel data and determine the format from header.
		// Note that BITMAPINFOHEADER2 only contains variable header size, the others have fixed ones.
		list($size) = array_merge(unpack("V", fread($binary, 4)));
		if (array_key_exists($size, $header_types)) {
			$header_type=$header_types[$size];
		} else if ($vendor==='IBM') {
			$header_type='BITMAPINFOHEADER2';
		} else {
			fclose($binary);
			return FALSE;
		}
		
		// Extract information (the format depend on the type).
		if (in_array($header_type, array('BITMAPV5HEADER', 'BITMAPV4HEADER', 'BITMAPINFOHEADER2', 'BITMAPINFOHEADER'))) {
			list($width) = array_merge(unpack("V", fread($binary, 4)));
			list($height) = array_merge(unpack("V", fread($binary, 4)));
			extract(unpack("vplanes/vbitcnt/Vcompress/Vsizeimage/Vxppm/Vyppm/Vclrused/Vclrimportant", fread($binary, 28)));
		} else if($header_type==='BITMAPCOREHEADER') {
			extract(unpack("vwidth/vheight/vplanes/vbitcnt", fread($binary, 8)));
			$compress = 0;
			$skip = 0;
		} else {
			fclose($binary);
			return FALSE;
		}
		
		// Confirm if we can go ahead.
		if (!in_array($bitcnt, $bitcnts)
		 || !array_key_exists($compress, $compress_types)
		 || ($bitcnt === 0 && !in_array($compress_types[$compress], array('BI_JPEG', 'BI_PNG')))) {
			fclose($binary);
			return FALSE;
		}
		
		$compress_type = $compress_types[$compress];
		
		if ($bitcnt<=8 && $clrused===0) {
			$clrused = pow(2, $bitcnt);
		}
		
		/*
		 * Determine the bit field.
		 * The bid field exists when all the following conditions are true:
		 * 1. Conpression type is 3.
		 * 2. $bitcnt is either 16 or 32
		 * 3. The type of header is either INFO, V4, V5, or INFO2 (when INFO2, the header length must be 40).
		 */
		$bitfields = '';
		if ($compress_type==='BI_BITFIELDS' && $bitcnt >= 16) {
			if(in_array($header_type, array('BITMAPV5HEADER', 'BITMAPV4HEADER')) || ($header_type==='BITMAPINFOHEADER2' && $size === 40)) {
				fseek($binary, 54, SEEK_SET);
				$bitfields = array_merge(unpack("Cred/Cgreen/Cblue/Calpha", fread($binary, 16)));
			} else if ($header_type==='BITMAPINFOHEADER') {
				fseek($binary, 54, SEEK_SET);
				$bitfields = array_merge(unpack("Cred/Cgreen/Cblue", fread($binary, 12)));
				$bitfields['alpha'] = FALSE;
			}
		}
		
		/*
		 * Determine the color pallette and construction of the pallette of GD format version 1
		 * The color pallete exists if either of following condition is true.
		 * 1. $bitcnt is either 1, 4, or 8.
		 * 2. The header type is INFO, INFO2, V4, or V5, and $usedclr is more than 1.
		 */
		$gd_palette ='';
		if ($clrused !== 0) {
			// RGBTRIPLE or RGBQUAD
			$factor = ($header_type==='BITMAPCOREHEADER') ? 3: 4;
			
			fseek($binary, $offbits-$clrused*$factor, SEEK_SET);
			$palette = fread($binary, $clrused*$factor);
			
			$j = 0;
			while($j < $clrused*$factor) {
				if($factor === 3) {
					$b	= $palette[$j++];
					$g	= $palette[$j++];
					$r	= $palette[$j++];
					$gd_palette .= "$r$g$b";
				} else {
					$b	= $palette[$j++];
					$g	= $palette[$j++];
					$r	= $palette[$j++];
					$rsvd	= $palette[$j++];
					$gd_palette .= "$r$g$b$rsvd";
				}
			}
			// The pallette must be 4 bytes * 256 for GD image format version 1.
			$gd_palette .= str_repeat("\x00\x00\x00\x00", 256 - $clrused);
		}
		
		/*
		 * Read the pixel data and put them to GD1 file format pixel data.
		 * When 0<$height, from lower row to upper row.
		 * When $height<0, from upper row to lower row (not recommended).
		 */
		
		// TODO: At least, BI_RLE8,BI_RLE4,BI_BITFIELDS must be available in the future.
		if ($compress_type!=='BI_RGB' || in_array($bitcnt, array(0, 16, 32))) {
			fclose($binary);
			return FALSE;
		}
		
		$gd_image = "";
		// The number of data per row.
		$line_avail_byte = (($bitcnt * $width + 7) >> 3);
		// Each row contains 8the data multiplied of 4 bytes.
		$line_size_byte = (($bitcnt * $width + 31) >> 5) * 4;
		
		/*
		 * Generally, bitmap format is:
		 * stored from lower row to upper row when 0<$height,
		 * but stored from upper row to lower row when $height<0 (not recommended).
		 */
		$factor = ($height > 0) ? 1: -1;
		
		if ($factor > 0) {
			$start = $offbits + $line_size_byte * ($height-1);
			$factor = -1;
		} else {
			$start = $offbits + $line_size_byte * $height;
			$factor = 1;
		}
		
		for ($i=0; $i<abs($height); $i++) {
			fseek ($binary, $start + $line_size_byte * $factor * $i, SEEK_SET);
			$line = fread($binary, $line_avail_byte);
			
			$gd_image_line = '';
			if ($bitcnt === 32) {
				// BITFIELD_TYPE
				// TODO: Not implemented.  Must use RGB88 or bit field definition.
				// RGB838 array(0xFF0000, 0x00007C00, 0x000003E0, 0x0000001F);
			} else if($bitcnt === 16) {
				// BITFIELD_TYPE
				// TODO: Not implemented.  Must use RGB555, RGB565 or bit field definition.
				// RGB555 array(0x00007C00, 0x000003E0, 0x0000001F);
				// RGB565 array(0x0000F800, 0x000007E0, 0x0000001F);
			} else if($bitcnt === 24) {
				// COLOR_TYPE
				$j = 0;
				while($j < $line_avail_byte) {
					$b = $line[$j++];
					$g = $line[$j++];
					$r = $line[$j++];
					$gd_image_line .= "\x00$r$g$b";
				}
			} else if($bitcnt == 8) {
				// PALETTE_TYPE
				$gd_image_line .= $line;
			} else if($bitcnt == 4) {
				// PALETTE_TYPE
				$j = 0;
				while($j < $line_avail_byte) {
					$byte = ord($line[$j++]);
					$p1 = chr($byte >> 4);
					$p2 = chr($byte & 0x0F);
					$gd_image_line .= "$p1$p2";
				}
				$gd_image_line .= substr($gd_image_line, 0, $width);
			} else if($bitcnt == 1) {
				// PALETTE_TYPE
				$j = 0;
				while($j < $line_avail_byte) {
					$byte = ord($line[$j++]);
					$p1 = chr((int) (($byte & 0x80) != 0));
					$p2 = chr((int) (($byte & 0x40) != 0));
					$p3 = chr((int) (($byte & 0x20) != 0));
					$p4 = chr((int) (($byte & 0x10) != 0));
					$p5 = chr((int) (($byte & 0x08) != 0));
					$p6 = chr((int) (($byte & 0x04) != 0));
					$p7 = chr((int) (($byte & 0x02) != 0));
					$p8 = chr((int) (($byte & 0x01) != 0));
					$gd_image_line .= "$p1$p2$p3$p4$p5$p6$p7$p8";
				}
				$gd_image_line .= substr($gd_image_line, 0, $width);
			}
			$gd_image .= $gd_image_line;
		}
		fclose($binary);
		
		if($gd_image === '') {
			return FALSE;
		}
		
		/*
		 *  Definition of GD file format version 1.
		 *  Determine which TrueColor or non-TrueColor (due to $gd_palette).
		 *  TODO: Transparent color can be set when 16/32 bit bitmap is used.
		 */
		if($gd_palette === '') {
			$gd_header = "\xFF\xFE";
			$gd_header .= pack("n", $width);
			$gd_header .= pack("n", abs($height));
			$gd_header .= "\x01";
			$gd_header .= pack("N", -1);
			$gd_binary = $gd_header . $gd_image;
		} else {
			$gd_header = "\xFF\xFF";
			$gd_header .= pack("n", $width);
			$gd_header .= pack("n", abs($height));
			$gd_header .= "\x00";
			$gd_header .= pack("n", $clrused);
			$gd_header .= pack("N", -1);
			$gd_binary = $gd_header . $gd_palette . $gd_image;
		}
		
		$temp=admin_temp::create();
		$temfile=$temp->filename();
		
		if (($handle = fopen($temfile, 'w')) === FALSE) {
			fclose($handle);
			return FALSE;
		}
		
		fwrite($handle, $gd_binary);
		fclose($handle);
		
		return imagecreatefromgd($temfile);
	}
}