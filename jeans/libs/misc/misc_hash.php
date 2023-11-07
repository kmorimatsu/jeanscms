<?php
/*
 * Jeans CMS (GPL license)
 * $Id: misc_hash.php 288 2010-10-11 07:06:21Z kmorimatsu $
 */

class misc_hash extends jeans {
	static public function init(){
		if (!function_exists('hash')) {
			function hash($mode,$str,$raw_output=false){
				$mode=strtoupper($mode);
				switch($mode){
					case 'MD5':
						return md5($str,$raw_output);
					case 'SHA1':
						return sha1($str,$raw_output);
					case 'SHA512': case 'SHA384':
						if (defined('MHASH_'.$mode) && function_exists('mhash')) {
							$result=mhash(constant('MHASH_'.$mode),$str);
						} else {
							$result=false;
							misc_hash::sha4($str,strlen($str),$result,$mode=='SHA384');
						}
						if ($raw_output) return $result;
						$ret='';
						for($i=0;$i<64;$i++){
							$byte=ord(substr($result,$i,1));
							if ($byte<16) $ret.='0';
							$ret.=dechex($byte);
						}
						return $ret;
					default:
						error::quit('"<%0%>" is not supported by hash() emulation mode.',$mode);
				}
			}
		}
	}
	/**
	 * SHA-512/384 calculation routine.
	 * This code originally came from PolarSSL (http://www.polarssl.org)
	 * but was modified by Jeans CMS team.
	 */
	/*
	 * SHA-512 context structure
	 */
	public $total,$state,$buffer,$is384;
	public function __construct($is384){
		$this->total=array(str_repeat("\x00",8),str_repeat("\x00",8));
		$this->buffer=str_repeat("\x00",128);
		$this->is384=$is384;
		$this->state=array();
		if( $is384 )
		{
			/* SHA-384 */
			$this->state[0] = "\xCB\xBB\x9D\x5D\xC1\x05\x9E\xD8";
			$this->state[1] = "\x62\x9A\x29\x2A\x36\x7C\xD5\x07";
			$this->state[2] = "\x91\x59\x01\x5A\x30\x70\xDD\x17";
			$this->state[3] = "\x15\x2F\xEC\xD8\xF7\x0E\x59\x39";
			$this->state[4] = "\x67\x33\x26\x67\xFF\xC0\x0B\x31";
			$this->state[5] = "\x8E\xB4\x4A\x87\x68\x58\x15\x11";
			$this->state[6] = "\xDB\x0C\x2E\x0D\x64\xF9\x8F\xA7";
			$this->state[7] = "\x47\xB5\x48\x1D\xBE\xFA\x4F\xA4";
		}
		else
		{
			/* SHA-512 */
			$this->state[0] = "\x6A\x09\xE6\x67\xF3\xBC\xC9\x08";
			$this->state[1] = "\xBB\x67\xAE\x85\x84\xCA\xA7\x3B";
			$this->state[2] = "\x3C\x6E\xF3\x72\xFE\x94\xF8\x2B";
			$this->state[3] = "\xA5\x4F\xF5\x3A\x5F\x1D\x36\xF1";
			$this->state[4] = "\x51\x0E\x52\x7F\xAD\xE6\x82\xD1";
			$this->state[5] = "\x9B\x05\x68\x8C\x2B\x3E\x6C\x1F";
			$this->state[6] = "\x1F\x83\xD9\xAB\xFB\x41\xBD\x6B";
			$this->state[7] = "\x5B\xE0\xCD\x19\x13\x7E\x21\x79";
		}
	}
	/*
	 * There is only a static public function below.
	 */
	static public function sha4(&$input,$ilen,&$output,$is384){
		$ctx=false;
		self::sha4_starts( $ctx, $is384 );
		self::sha4_update( $ctx, $input, $ilen );
		self::sha4_finish( $ctx, $output );
	}
	/*
	 * Private functions follow.
	 */
	/*
	 * 64 bit calculation routines.
	 */
	static private function int_to_64($int){
		$result='';
		for($i=0;$i<8;$i++){
			$result=chr($int & 0xff).$result;
			$int=$int>>8;
		}
		return $result;
	}
	static private function int_from_64($i64str){
		$result=0;
		for($i=0;$i<8;$i++){
			$result*=256;
			$result+=ord(substr($i64str,$i,1));
		}
		return $result;
	}
	static private function bitc($a,$mode,$b){
		if (is_int($a)) $a=self::int_to_64($a);
		if (is_int($b)) $b=self::int_to_64($b);
		$result='';
		$c=0;
		for($i=7;0<=$i;$i--){
			$aa=ord(substr($a,$i,1));
			$bb=ord(substr($b,$i,1));
			switch($mode){
				case '&':
					$byte=$aa & $bb;
					break;
				case '|':
					$byte=$aa | $bb;
					break;
				case '^':
					$byte=$aa ^ $bb;
					break;
				case '+':
					$byte=$aa + $bb +$c;
					$c=$byte>>8;
					$byte=$byte & 0xff;
					break;
				default:
					exit(__CLASS__.__LINE__);
			}
			$result=chr($byte).$result;
		}
		return $result;
	}
	/*
	 * Shift and rotate routines.
	 */
	static private function shr($x,$n){
		$n8=$n>>3;
		if (0<$n8) {
			$x=substr(str_repeat("\x00",$n8).$x,0,8);
		}
		$n=$n & 7;
		if (0<$n) {
			$result='';
			$c=0;
			for($i=0;$i<8;$i++){
				$byte=ord(substr($x,$i,1));
				$result.=chr(($byte>>$n) | $c);
				$c=($byte<<(8-$n)) & 0xff;
			}
			$x=$result;
		}
		return $x;
	}
	static private function ror($x,$n){
		$n8=$n>>3;
		if (0<$n8) {
			$x=substr($x.$x,8-$n8,8);
		}
		$n=$n & 7;
		if (0<$n) {
			$result='';
			$byte=ord(substr($x,-1));
			$byte=$byte<<(8-$n);
			$c=$byte & 0xff;
			for($i=0;$i<8;$i++){
				$byte=ord(substr($x,$i,1));
				$result.=chr(($byte>>$n) | $c);
				$c=($byte<<(8-$n)) & 0xff;
			}
			$x=$result;
		}
		return $x;
	}
	static private function shl($x,$n){
		$n8=$n>>3;
		if (0<$n8) {
			$x=substr($x.str_repeat("\x00",$n8),-8);
		}
		$n=$n & 7;
		if (0<$n) {
			$result='';
			$c=0;
			for($i=7;0<=$i;$i--){
				$byte=ord(substr($x,$i,1))<<$n;
				$result=chr(($byte & 0xff) | $c).$result;
				$c=$byte>>8;
			}
			$x=$result;
		}
		return $x;
	}
	/*
	 * Get/put 64bit data from/into buffer.
	 */
	static private function get_uint64_be(&$n,$b,$i){
		$n=substr($b,$i,8);
	}
	static private function put_uint64_be($n,&$b,$i){
		$b=substr($b,0,$i).$n.substr($b,$i+8);
	}
	/*
	 * Round constants
	 */
	static private $k=array(
		"\x42\x8A\x2F\x98\xD7\x28\xAE\x22","\x71\x37\x44\x91\x23\xEF\x65\xCD",
		"\xB5\xC0\xFB\xCF\xEC\x4D\x3B\x2F","\xE9\xB5\xDB\xA5\x81\x89\xDB\xBC",
		"\x39\x56\xC2\x5B\xF3\x48\xB5\x38","\x59\xF1\x11\xF1\xB6\x05\xD0\x19",
		"\x92\x3F\x82\xA4\xAF\x19\x4F\x9B","\xAB\x1C\x5E\xD5\xDA\x6D\x81\x18",
		"\xD8\x07\xAA\x98\xA3\x03\x02\x42","\x12\x83\x5B\x01\x45\x70\x6F\xBE",
		"\x24\x31\x85\xBE\x4E\xE4\xB2\x8C","\x55\x0C\x7D\xC3\xD5\xFF\xB4\xE2",
		"\x72\xBE\x5D\x74\xF2\x7B\x89\x6F","\x80\xDE\xB1\xFE\x3B\x16\x96\xB1",
		"\x9B\xDC\x06\xA7\x25\xC7\x12\x35","\xC1\x9B\xF1\x74\xCF\x69\x26\x94",
		"\xE4\x9B\x69\xC1\x9E\xF1\x4A\xD2","\xEF\xBE\x47\x86\x38\x4F\x25\xE3",
		"\x0F\xC1\x9D\xC6\x8B\x8C\xD5\xB5","\x24\x0C\xA1\xCC\x77\xAC\x9C\x65",
		"\x2D\xE9\x2C\x6F\x59\x2B\x02\x75","\x4A\x74\x84\xAA\x6E\xA6\xE4\x83",
		"\x5C\xB0\xA9\xDC\xBD\x41\xFB\xD4","\x76\xF9\x88\xDA\x83\x11\x53\xB5",
		"\x98\x3E\x51\x52\xEE\x66\xDF\xAB","\xA8\x31\xC6\x6D\x2D\xB4\x32\x10",
		"\xB0\x03\x27\xC8\x98\xFB\x21\x3F","\xBF\x59\x7F\xC7\xBE\xEF\x0E\xE4",
		"\xC6\xE0\x0B\xF3\x3D\xA8\x8F\xC2","\xD5\xA7\x91\x47\x93\x0A\xA7\x25",
		"\x06\xCA\x63\x51\xE0\x03\x82\x6F","\x14\x29\x29\x67\x0A\x0E\x6E\x70",
		"\x27\xB7\x0A\x85\x46\xD2\x2F\xFC","\x2E\x1B\x21\x38\x5C\x26\xC9\x26",
		"\x4D\x2C\x6D\xFC\x5A\xC4\x2A\xED","\x53\x38\x0D\x13\x9D\x95\xB3\xDF",
		"\x65\x0A\x73\x54\x8B\xAF\x63\xDE","\x76\x6A\x0A\xBB\x3C\x77\xB2\xA8",
		"\x81\xC2\xC9\x2E\x47\xED\xAE\xE6","\x92\x72\x2C\x85\x14\x82\x35\x3B",
		"\xA2\xBF\xE8\xA1\x4C\xF1\x03\x64","\xA8\x1A\x66\x4B\xBC\x42\x30\x01",
		"\xC2\x4B\x8B\x70\xD0\xF8\x97\x91","\xC7\x6C\x51\xA3\x06\x54\xBE\x30",
		"\xD1\x92\xE8\x19\xD6\xEF\x52\x18","\xD6\x99\x06\x24\x55\x65\xA9\x10",
		"\xF4\x0E\x35\x85\x57\x71\x20\x2A","\x10\x6A\xA0\x70\x32\xBB\xD1\xB8",
		"\x19\xA4\xC1\x16\xB8\xD2\xD0\xC8","\x1E\x37\x6C\x08\x51\x41\xAB\x53",
		"\x27\x48\x77\x4C\xDF\x8E\xEB\x99","\x34\xB0\xBC\xB5\xE1\x9B\x48\xA8",
		"\x39\x1C\x0C\xB3\xC5\xC9\x5A\x63","\x4E\xD8\xAA\x4A\xE3\x41\x8A\xCB",
		"\x5B\x9C\xCA\x4F\x77\x63\xE3\x73","\x68\x2E\x6F\xF3\xD6\xB2\xB8\xA3",
		"\x74\x8F\x82\xEE\x5D\xEF\xB2\xFC","\x78\xA5\x63\x6F\x43\x17\x2F\x60",
		"\x84\xC8\x78\x14\xA1\xF0\xAB\x72","\x8C\xC7\x02\x08\x1A\x64\x39\xEC",
		"\x90\xBE\xFF\xFA\x23\x63\x1E\x28","\xA4\x50\x6C\xEB\xDE\x82\xBD\xE9",
		"\xBE\xF9\xA3\xF7\xB2\xC6\x79\x15","\xC6\x71\x78\xF2\xE3\x72\x53\x2B",
		"\xCA\x27\x3E\xCE\xEA\x26\x61\x9C","\xD1\x86\xB8\xC7\x21\xC0\xC2\x07",
		"\xEA\xDA\x7D\xD6\xCD\xE0\xEB\x1E","\xF5\x7D\x4F\x7F\xEE\x6E\xD1\x78",
		"\x06\xF0\x67\xAA\x72\x17\x6F\xBA","\x0A\x63\x7D\xC5\xA2\xC8\x98\xA6",
		"\x11\x3F\x98\x04\xBE\xF9\x0D\xAE","\x1B\x71\x0B\x35\x13\x1C\x47\x1B",
		"\x28\xDB\x77\xF5\x23\x04\x7D\x84","\x32\xCA\xAB\x7B\x40\xC7\x24\x93",
		"\x3C\x9E\xBE\x0A\x15\xC9\xBE\xBC","\x43\x1D\x67\xC4\x9C\x10\x0D\x4C",
		"\x4C\xC5\xD4\xBE\xCB\x3E\x42\xB6","\x59\x7F\x29\x9C\xFC\x65\x7E\x2A",
		"\x5F\xCB\x6F\xAB\x3A\xD6\xFA\xEC","\x6C\x44\x19\x8C\x4A\x47\x58\x17"
	);
	/*
	 * SHA-512 context setup
	 * See also constructor.
	 */
	static private $sha4_padding;
	static private function sha4_starts( &$ctx, $is384 ){
		$ctx=new self($is384);
		if (!isset(self::$sha4_padding)) {
			self::$sha4_padding="\x80".str_repeat("\x00",127);
		}
	}
	/*
	 * Bit calculation routines follow
	 */
	static private function s0($x){
		$result=self::ror($x,1);
		$result=self::bitc($result,'^',self::ror($x,8));
		$result=self::bitc($result,'^',self::shr($x,7));
		return $result;
	}
	static private function s1($x){
		$result=self::ror($x,19);
		$result=self::bitc($result,'^',self::ror($x,61));
		$result=self::bitc($result,'^',self::shr($x,6));
		return $result;
	}
	static private function s2($x){
		$result=self::ror($x,28);
		$result=self::bitc($result,'^',self::ror($x,34));
		$result=self::bitc($result,'^',self::ror($x,39));
		return $result;
	}
	static private function s3($x){
		$result=self::ror($x,14);
		$result=self::bitc($result,'^',self::ror($x,18));
		$result=self::bitc($result,'^',self::ror($x,41));
		return $result;
	}
	static private function f0($x,$y,$z){
		$v1=self::bitc($x,'&',$y);
		$v2=self::bitc($x,'|',$y);
		$v2=self::bitc($z,'&',$v2);
		return self::bitc($v1,'|',$v2);
	}
	static private function f1($x,$y,$z){
		$v1=self::bitc($y,'^',$z);
		$v1=self::bitc($x,'&',$v1);
		return self::bitc($z,'^',$v1);
	}
	static private function p0($a,$b,$c,&$d,$e,$f,$g,&$h,$x,$k){
		$temp1=self::bitc($h,'+',self::s3($e));
		$temp1=self::bitc($temp1,'+',self::f1($e,$f,$g));
		$temp1=self::bitc($temp1,'+',$k);
		$temp1=self::bitc($temp1,'+',$x);
		$temp2=self::bitc(self::s2($a),'+',self::f0($a,$b,$c));
		$d=self::bitc($d,'+',$temp1);
		$h=self::bitc($temp1,'+',$temp2);
	}
	/*
	 * SHA512/SHA384 process
	 */
	static private function sha4_process(&$ctx, $data){
		$W=array_fill(0,80,false);

		for( $i = 0; $i < 16; $i++ ){
			self::get_uint64_be( $W[$i], $data, $i << 3 );
		}

		for( ; $i < 80; $i++ ){
			$temp=self::bitc(self::s1($W[$i-2]),'+',$W[$i-7]);
			$temp=self::bitc($temp,'+',self::s0($W[$i-15]));
			$W[$i]=self::bitc($temp,'+',$W[$i - 16]);
		}

		$A = $ctx->state[0];
		$B = $ctx->state[1];
		$C = $ctx->state[2];
		$D = $ctx->state[3];
		$E = $ctx->state[4];
		$F = $ctx->state[5];
		$G = $ctx->state[6];
		$H = $ctx->state[7];
		$i = 0;

		do{
			self::p0( $A, $B, $C, $D, $E, $F, $G, $H, $W[$i], self::$k[$i] ); $i++;
			self::p0( $H, $A, $B, $C, $D, $E, $F, $G, $W[$i], self::$k[$i] ); $i++;
			self::p0( $G, $H, $A, $B, $C, $D, $E, $F, $W[$i], self::$k[$i] ); $i++;
			self::p0( $F, $G, $H, $A, $B, $C, $D, $E, $W[$i], self::$k[$i] ); $i++;
			self::p0( $E, $F, $G, $H, $A, $B, $C, $D, $W[$i], self::$k[$i] ); $i++;
			self::p0( $D, $E, $F, $G, $H, $A, $B, $C, $W[$i], self::$k[$i] ); $i++;
			self::p0( $C, $D, $E, $F, $G, $H, $A, $B, $W[$i], self::$k[$i] ); $i++;
			self::p0( $B, $C, $D, $E, $F, $G, $H, $A, $W[$i], self::$k[$i] ); $i++;
		} while( $i < 80 );

		$ctx->state[0] = self::bitc($ctx->state[0],'+', $A);
		$ctx->state[1] = self::bitc($ctx->state[1],'+', $B);
		$ctx->state[2] = self::bitc($ctx->state[2],'+', $C);
		$ctx->state[3] = self::bitc($ctx->state[3],'+', $D);
		$ctx->state[4] = self::bitc($ctx->state[4],'+', $E);
		$ctx->state[5] = self::bitc($ctx->state[5],'+', $F);
		$ctx->state[6] = self::bitc($ctx->state[6],'+', $G);
		$ctx->state[7] = self::bitc($ctx->state[7],'+', $H);
	}
	/*
	 * SHA-512 process buffer
	 */
	static private function memcpy(&$to,$to_pos,&$from,$from_pos,$len){
		$to=substr($to,0,$to_pos).
			substr($from,$from_pos,$len).
			substr($to,$to_pos+$len);
	}
	static private function sha4_update( &$ctx, &$input, $ilen ){
		if( $ilen <= 0 )
			return;
		
		$input_pos=0;
		$temp=array_fill(0,128,0);
	
		$left = ord(substr($ctx->total[0],-1)) & 0x7F;
		$fill = (int)( 128 - $left );
	
		$ctx->total[0]=self::bitc($ctx->total[0],'+',(int)$ilen);
	
		if( self::int_from_64($ctx->total[0]) < $ilen )
			$ctx->total[1]=self::bitc($ctx->total[1],'+',1);
	
		if( $left && $ilen >= $fill )
		{
			self::memcpy($ctx->buffer,$left,$input,$input_pos,$fill);
			self::sha4_process( $ctx, $ctx->buffer );
			$input_pos+=$fill;
			$ilen  -= $fill;
			$left = 0;
		}
	
		while( $ilen >= 128 )
		{
			$temp='';
			self::memcpy($temp,0,$input,$input_pos,128);
			self::sha4_process( $ctx, $temp );
			$input_pos +=128;
			$ilen  -= 128;
		}
	
		if( $ilen > 0 )
		{
			self::memcpy($ctx->buffer,$left,$input,$input_pos,$ilen);
		}
	}
	/*
	 * SHA-512 final digest
	 */
	static private function sha4_finish( &$ctx, &$output){
		$msglen=str_repeat("\x00",16);
	
		$high=self::bitc(self::shr($ctx->total[0],61),'|',self::shl($ctx->total[1],3));
		$low=self::shl($ctx->total[0],3);

		self::put_uint64_be( $high, $msglen, 0 );
		self::put_uint64_be( $low,  $msglen, 8 );

		$last = ord(substr($ctx->total[0],-1)) & 0x7F;
		$padn = ( $last < 112 ) ? ( 112 - $last ) : ( 240 - $last );
	
		self::sha4_update( $ctx, self::$sha4_padding, $padn );
		self::sha4_update( $ctx, $msglen, 16 );
	
		self::put_uint64_be( $ctx->state[0], $output,  0 );
		self::put_uint64_be( $ctx->state[1], $output,  8 );
		self::put_uint64_be( $ctx->state[2], $output, 16 );
		self::put_uint64_be( $ctx->state[3], $output, 24 );
		self::put_uint64_be( $ctx->state[4], $output, 32 );
		self::put_uint64_be( $ctx->state[5], $output, 40 );
	
		if( !$ctx->is384 )
		{
			self::put_uint64_be( $ctx->state[6], $output, 48 );
			self::put_uint64_be( $ctx->state[7], $output, 56 );
		}
	}
}
