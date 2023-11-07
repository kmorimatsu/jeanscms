<?php
/*
 * Jeans CMS (GPL license)
 * $Id: media.php 314 2010-11-21 21:44:08Z kmorimatsu $
 */

class media extends jeans {
	static private $if_modified_since=false;
	static public function init(){
		if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
			self::$if_modified_since=strtolower($_SERVER['HTTP_IF_MODIFIED_SINCE']);
		} elseif (function_exists('apache_request_headers')) {
			foreach(apache_request_headers() as $key=>$value) {
				if (strtolower($key)!='if-modified-since') continue;
				self::$if_modified_since=strtolower($value);
				break;
			}
		}
		if (!defined('_CONF_THUMBNAIL_SIZE')) define('_CONF_THUMBNAIL_SIZE',240);
	}
	static public function action_get_skin(){
		if (isset($_GET['file_path'])) {
			$file=$_GET['file_path'];
			if (self::local_file_exists(_DIR_SKINS,$file)) {
				core::redirect(_CONF_URL_SKINS.$file);
			}
		}
		header('HTTP/1.0 404 Not Found');
		exit('404 Not Found');
	}
	static public function action_get_media(){
		$mode=isset($_GET['mode']) ? $_GET['mode'] : 'view';
		switch($mode){
			case 'thumbnail':
				return self::show_media('view',true);
			case 'download':
				return self::show_media('download');
			case 'view':
			default:
				return self::show_media('view');
		}
	}
	static private function try_redirect($mode){
		if (self::$if_modified_since) return;
		$url='?action=media&mode='.$mode.'&file='.@$_GET['file'];
		if (isset($_GET['owner'])) $url.='&owner='.$_GET['owner'];
		core::redirect_local(_CONF_URL_INDEX.$url);
		exit;
	}
	static public function action_get_view(){
		self::try_redirect('view');
		return self::show_media('view');
	}
	static public function action_get_thumbnail(){
		self::try_redirect('thumbnail');
		return self::show_media('view',true);
	}
	static public function action_get_download(){
		self::try_redirect('dowload');
		return self::show_media('download');
	}
	static private function show_media($mode,$thumbnail=false){
		if (isset($_GET['file']) && preg_match('/^([0-9]+)\-(.+)$/',$_GET['file'],$m)) {
			// Media in jeans_binary table
			$is_file=false;
		} elseif ($thumbnail && isset($_GET['file_path']) && self::local_file_exists(_DIR_SKINS.'media/',$_GET['file_path'])) {
			// Media in skins directory
			if (preg_match('#^([0-9]+)/(.+)$#',$_GET['file_path'],$m)) {
				// Media in user's media directory
			} else {
				$m=array(0,$file);
			}
			$is_file=filemtime(_DIR_SKINS.'media/'.$_GET['file_path']);
		} else self::not_found();
		if (!$m[1]) self::not_found();
		if ($thumbnail) {
			// If thumbnail exists, use it.  Otherwise, use original one.
			$query='SELECT bindata,mime,name,time FROM jeans_binary 
				WHERE (type="media" OR type="thumbnail") AND name=<%name%> AND contextid=<%id%> AND owner=<%owner%> 
				AND NOT (flags & <%const:sql::FLAG_INVALID%>)
				ORDER BY type DESC LIMIT 1';
		} else {
			// Seek the original media.
			$query='SELECT bindata,mime,name,time FROM jeans_binary 
				WHERE type="media" AND name=<%name%> AND contextid=<%id%> AND owner=<%owner%> 
				AND NOT (flags & <%const:sql::FLAG_INVALID%>) ';
		}
		$owner=isset($_GET['owner']) ?$_GET['owner']:'jeans';
		$array=array('name'=>$m[2],'id'=>$m[1],'owner'=>$owner);
		$row=sql::query($query,$array)->fetch();
		// Check if thumbnail for skinfile corresponde to current file
		if ($row && $is_file) {
			if (strtotime($row['time'].' GMT')!=$is_file) $row=false;
		}
		if (!$row) {
			// Media not found.
			if ($thumbnail && $is_file) {
				$row=admin_thumbnail::save_skinfile_thumbnail($_GET['file_path'],$array);
			}
			if (!$row) self::not_found();
		}
		// Media found.
		$time=strtotime($row['time'].' GMT');
		// Check if modified from previous one.
		self::check_if_changed($time);
		// Return the raw media.
		self::send_media($row['bindata'],$row['mime'],$time,$row['name'],$mode);
	}
	static private function not_found(){
		header("HTTP/1.0 404 Not Found");
		exit('404 Not Found.');
	}
	/*
	 * Following method is public.
	 * You can use it from any class.
	 * $time is Unix timestamp (GMT).
	 */
	static public function check_if_changed($time){
		if (!self::$if_modified_since) return;
		$lastmodified=array(
			gmstrftime("%a, %d %b %Y %H:%M:%S GMT", $time),
			gmstrftime("%A, %d-%b-%y %H:%M:%S GMT", $time),
			preg_replace('/ 0([0-9]) /',' $1 ',gmstrftime("%a %b %d %H:%M:%S %Y", $time)));
		foreach ($lastmodified as $value) {
			if (strtolower($value)!=self::$if_modified_since) continue;
			header ("HTTP/1.1 304 Not Modified");
			header ("Date: ".$lastmodified[0]);
			exit;
		}
	}
	/*
	 * Following method are public.
	 * You can use it from any class.
	 */
	static public function send_media(&$image,$mime,$time=false,$name=false,$mode='view'){
		// Prepare header
		if (!$time) $time=time();
		if ($name===false) header("Content-Type: $mime");
		else header("Content-Type: $mime; name=\"$name\"");
		header ("Content-Length: ".strlen($image));
		header ("Last-Modified: ".gmstrftime("%a, %d %b %Y %H:%M:%S GMT", $time));
		if ($mode=='download') header("Content-disposition: attachment; filename=$name");
		// An exception of using 'echo' statement here.
		// To output binary file, 'echo' is needed.
		echo $image;
		exit;
	}
	static public function gd($file,$mime=false,$size=false){
		// Note that this method will be deleted in future release (probably in beta version).
		return admin_thumbnail::gd($file,$mime,$size);
	}
	
}
