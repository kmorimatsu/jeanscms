<?php
/*
 * Jeans CMS (GPL license)
 * $Id: admin_thumbnail.php 313 2010-11-21 21:21:52Z kmorimatsu $
 */

class admin_thumbnail extends jeans {
	static public function init(){
	}
	static public function save_skinfile_thumbnail($file,$info){
		// Trying to show the thumbnail in skins/media directory.
		// $info=array('name'=>$m[2],'id'=>$m[1],'owner'=>$owner); (see media::show_media)
		if (!self::local_file_exists(_DIR_SKINS.'media/',$file)) return false;
		$size=_CONF_THUMBNAIL_SIZE;
		$mime=tables::mime($file);
		if (!preg_match('#^image/#i',$mime)) return false;
		core::event('pre_create_thumbnail',array(
				'path'=>_DIR_SKINS.'media/'.$file,
				'name'=>$file,
				'size'=>&$size,
				'mime'=>$mime,
				'owner'=>$info['owner']));
		$image=self::gd(_DIR_SKINS.'media/'.$file,$mime,$size);
		$time=filemtime(_DIR_SKINS.'media/'.$file);
		if ($image) {
			$thumbdata=$image[2];
			$xml=new SimpleXMLElement(_XML_BLANC);
			$xml->width=$thumbdata['width'];
			$xml->height=$thumbdata['height'];
			$row=array(
				'type'=>'thumbnail',
				'owner'=>$info['owner'],
				'bindata'=>&$thumbdata['file'],
				'binsize'=>strlen($thumbdata['file']),
				'mime'=>$thumbdata['mime'],
				'name'=>$info['name'],
				'time'=>gmdate('Y-m-d H:i:s', $time),
				'contextid'=>$info['id'],
				'xml'=>$xml->asXML());
			$query='INSERT OR REPLACE INTO jeans_binary (<%key:row%>) VALUES (<%row%>)';
			$array=array('row'=>$row);
			sql::query($query,$array);
		} elseif (preg_match('/\.([^\.]+)$/',$_GET['file_path'],$m2)) {
			$row=array(
				'bindata'=>self::local_file_contents(_DIR_SKINS.'media/',$_GET['file_path']),
				'mime'=>tables::mime($m2[1]),
				'name'=>$info['name'],
				'time'=>$time);
		} else self::not_found();
		return $row;
	}
	static public function gd($file,$mime=false,$size=false){
		// Usage: admin_thumbnail::gd($file)
		//        admin_thumbnail::gd($file,$mime)
		//        admin_thumbnail::gd($file,$mime,$size)
		//        admin_thumbnail::gd($image_string,'string')
		
		// Get mime from filename if not specified
		if ($mime===false) $mime=tables::mime($file);
		// Check if GD is available.
		switch ($mime) {
			case 'image/gif':
				$func='imagecreatefromgif';
				break;
			case 'image/jpeg':
				$func='imagecreatefromjpeg';
				break;
			case 'image/png':
				$func='imagecreatefrompng';
				break;
			case 'image/bmp':
			case 'image/x-ms-bmp':
				$func='misc_gd::imagecreatefrombmp';
				break;
			case 'image/x-xbitmap':
				$func='imagecreatefromxbm';
				break;
			case 'image/x-xpixmap':
				$func='imagecreatefromxpm';
				break;
			case 'string':
				$func='imagecreatefromstring';
				break;
			default:
				return false;
		}
		if (!is_callable($func)) return false;
		$image=call_user_func_array($func,array(&$file));
		if (!$image) return false;
		// Determine the size.
		if (!$size) $size=_CONF_THUMBNAIL_SIZE;
		$width=imagesx($image);
		$height=imagesy($image);
		// Decide the new size.
		if ($width>$height) {
			$new_width=intval($size);
			$new_height=intval($height * $size / $width);
		} else {
			$new_width=intval($width * $size / $height);
			$new_height=intval($size);
		}
		// Create new image.
		$new_image = ImageCreateTrueColor($new_width, $new_height);
		ImageCopyResampled($new_image,$image,0,0,0,0,$new_width,$new_height,$width,$height);
		$types=imagetypes();
		if ($types & IMG_PNG) {
			$thumbnail=array(
				'mime'=>'image/png',
				'width'=>$new_width,
				'height'=>$new_height,
				'file'=>self::gd_image('imagepng',$new_image));	
		} elseif ($types & IMG_JPG) {
			$thumbnail=array(
				'mime'=>'image/jpeg',
				'width'=>$new_width,
				'height'=>$new_height,
				'file'=>self::gd_image('imagejpeg',$new_image));
		} elseif ($types & IMG_GIF) {
			$thumbnail=array(
				'mime'=>'image/gif',
				'width'=>$new_width,
				'height'=>$new_height,
				'file'=>self::gd_image('imagegif',$new_image));
		} else $thumbnail=false;
		// Return original size and thumbnail.
		return array($width,$height,$thumbnail);
	}
	static private function gd_image($func,&$res){
		ob_start();
		call_user_func_array($func,array($res));
		return ob_get_clean();
	}
}