<?php
/*
 * Jeans CMS (GPL license)
 * $Id: showmedia.php 216 2010-06-27 18:42:54Z kmorimatsu $
 */

class showmedia extends jeans {
	static public function init(){
	}
	static private function popup_data($type){
		switch($type){
			case 'text':
				$key='alt_text';
				break;
			case 'mode':
				$key='imagepopup';
				break;
			case 'file':
				$key='image_path';
				break;
		}
		if (isset($key) && isset($_GET[$key])) return $_GET[$key];
		return '';
	}
	static public function if_popupdb(){
		return self::popup_data('mode')=='db';
	}
	static public function tag_popuptext(){
		self::p(self::popup_data('text'));
	}
	static public function tag_img(&$data,$file=false,$alt=false,$width=false,$height=false){
		if (!$file) $file=self::popup_data('file');
		if (!$alt) $alt=self::popup_data('text');
		if (!$alt) $alt=$file;
		if (self::local_file_exists(_DIR_SKINS,$file)) {
			if (!$height) {
				$size=getimagesize(_DIR_SKINS.$file);
				$width=$size[0];
				$height=$size[1];
			}
			$file=_CONF_URL_SKINS.$file;
		} else return self::p($alt);
		self::_img_tag($file,$alt,$width,$height);
	}
	static public function tag_imgdb(&$data,$file=false,$alt=false,$width=false,$height=false){
		if (!$file) $file=self::popup_data('file');
		if (!$alt) $alt=self::popup_data('text');
		if (!$alt) $alt=$file;
		if (preg_match('/^([0-9]+)\-(.*)$/',$file,$m)) {
			$query='SELECT * FROM jeans_binary WHERE contextid=<%1%> AND name=<%2%> AND type="media" LIMIT 1';
			$row=sql::query($query,$m)->fetch();
			if (!$row) return self::p($alt);
			if (!$height) {
				$xml=new SimpleXMLElement($row['xml']);
				$width=$xml->width;
				$height=$xml->height;
			}
			$file='?action=media.view&file='.$file;
		} else return self::p($alt);
		self::_img_tag($file,$alt,$width,$height);
	}
	static private function _img_tag($file,$alt,$width,$height){
		$html='<img src="<%file%>" alt="<%alt%>"'.
			($height?' height="<%height%>"':'').
			($width?' width="<%width%>"':'').
			'/>';
		self::echo_html($html,array(
			'file'=>$file,
			'alt'=>$alt,
			'width'=>$width,
			'height'=>$height));
	}
	static public function tag_media(&$data,$file=false,$mode='raw',$alt=false){
		if (!$file) $file=$data['file'];
		switch ($mode) {
			case 'raw':
				$html='<%file%>';
				break;
			case 'link':
			default:
				if (!$alt) $file=$data['alt'];
				$html='<a href="<%file%>"><%alt%></a>';
		}
		self::echo_html($html,array(
			'file'=>_CONF_URL_MEDIA.$file,
			'alt'=>$alt));
	}
	
}
