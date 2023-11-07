<?php
/*
 * Jeans CMS (GPL license)
 * $Id: admin_media.php 365 2017-10-25 20:06:22Z kmorimatsu $
 */

class admin_media extends jeans {
	static private $max_size,$accepted;
	static public function init(){
		// Authority check for the action.
		$warning=self::translate('_ADMIN_NO_PERMISSION');
		$mid=isset($_GET['mid']) ? intval($_GET['mid']) : member::setting('id');
		if (!_CONF_ALLOW_FILE_UPLOAD || !member::logged_in()) jerror::quit($warning);
		if (member::is_admin()) {
			self::$max_size=_CONF_MAX_UPLOAD_SIZE;
		} elseif ($mid==member::setting('id')) {
			$max_size=_CONF_MAX_UPLOAD_SIZE;
			$max_total=_CONF_MAX_UPLOAD_TOTAL;
			$res=sql::query('SELECT binsize FROM jeans_binary WHERE type="media" AND contextid=<%0%>',member::setting('id'));
			while ($row=$res->fetch()) $max_total=$max_total-$row['binsize'];
			if ($max_total < $max_size) $max_size=$max_total;
			self::$max_size=$max_size;
		} else jerror::quit($warning);
		self::member_id($mid);
		self::$accepted=preg_split('/[^a-zA-Z0-9]+/',strtolower(_CONF_ALLOW_FILE_TYPES),-1,PREG_SPLIT_NO_EMPTY);
		// Thumbnail
		if (!defined('_CONF_THUMBNAIL_VIEW_SIZE')) define('_CONF_THUMBNAIL_ADMIN_SIZE',80);
	}
	static private function member_id($set=false){
		static $mid;
		if ($set && !isset($mid)) $mid=$set;
		return $mid;
	}
	static public function action_post_upload(){
		// Get information of uploaded file
		if (!isset($_FILES['binfile'])) return jerror::note('_ADMIN_MEDIA_FILE_NOT_FOUND');
		$file=&$_FILES['binfile'];
		switch ($file['error']) {
			case UPLOAD_ERR_OK:
				break;
			case UPLOAD_ERR_INI_SIZE: case UPLOAD_ERR_FORM_SIZE:
				@unlink($file['tmp_name']);
				return jerror::note('_ADMIN_MEDIA_FILE_TOO_LARGE');
			default:
				@unlink($file['tmp_name']);
				return jerror::note('_ADMIN_MEDIA_UNKNOWN_ERROR');
		}
		// Check if upload is accepted to this file.
		$size=filesize($file['tmp_name']);
		if (self::$max_size < $size) {
			@unlink($file['tmp_name']);
			return jerror::note(self::translate('_ADMIN_MEDIA_FILE_TOO_LARGE').'(max: '.self::$max_size.')');
		}
		if (!preg_match('/^(.+\.)([a-zA-Z0-9]+)$/',trim($file['name']),$m)) {
			@unlink($file['tmp_name']);
			return jerror::note('_ADMIN_MEDIA_INVALID_FILENAME');
		}
		$m[2]=strtolower($m[2]);
		if (!in_array($m[2],self::$accepted)) {
			@unlink($file['tmp_name']);
			return jerror::note('_ADMIN_MEDIA_INVALID_FILE_TYPE');
		}
		$m[1]=self::utf8($m[1]);
		if (strlen($m[1])==0) {
			@unlink($file['tmp_name']);
			return jerror::note('_ADMIN_MEDIA_INVALID_FILENAME');
		}
		// Get image information and thumbnail if possible
		$file_name=$m[1].$m[2];
		$file_name=preg_replace(_PREG_NON_SAFE_CHAR,'_',$file_name);
		$size=_CONF_THUMBNAIL_SIZE;
		$mime=tables::mime($file_name);
		$xml=new SimpleXMLElement(_XML_BLANC);
		if (preg_match('#^image/#i',$mime)) {
			core::event('pre_create_thumbnail',array(
					'path'=>$file['tmp_name'],
					'name'=>$file_name,
					'size'=>&$size,
					'mime'=>$mime,
					'owner'=>'jeans'));
			$gd=admin_thumbnail::gd($file['tmp_name'],$mime);
			if ($gd) {
				$thumbnail=$gd[2];
			} else {
				$gd=@getimagesize($file['tmp_name']);
				$thumbnail=false;
			}
			if ($size) {
				$xml->width=$gd[0];
				$xml->height=$gd[1];
			}
		} else $thumbnail=false;
		// Save image to DB
		if (_CONF_PREFIX_UPLOADED_FILES) $file_name=substr(_NOW,0,10).'-'.$file_name;
		$query='INSERT INTO jeans_binary (<%key:row%>) VALUES (<%row%>)';
		$row=array('type'=>'media','name'=>$file_name,'contextid'=>self::member_id(),'time'=>_NOW,
			'bindata'=>file_get_contents($file['tmp_name']),'binsize'=>$size,'mime'=>$mime,'xml'=>$xml->asXML());
		$res=sql::query($query,array('row'=>$row));
		unlink($file['tmp_name']);
		// Save thumbnail if available
		if ($thumbnail) {
			$xml->width=$thumbnail['width'];
			$xml->height=$thumbnail['height'];
			$row=array('type'=>'thumbnail','name'=>$file_name,'contextid'=>self::member_id(),'time'=>_NOW,
				'bindata'=>$thumbnail['file'],'binsize'=>strlen($thumbnail['file']),'mime'=>$thumbnail['mime'],'xml'=>$xml->asXML());
			sql::query($query,array('row'=>$row));
		}
		if ($res->rowCount()) {
			jerror::note('_ADMIN_MEDIA_UPLOAD_DONE');
			$array=array('name'=>$file_name,'size'=>$size,'mime'=>$mime,'time'=>_NOW);
			foreach($xml as $key=>$value) $array[$key]=$value;
			$array['type']=isset($array['width']) ? 'image':'media';
			// Set information of the uploaded file
			self::uploaded_file($array);
		} else {
			jerror::note('_ADMIN_MEDIA_UPLOAD_FAILED');
		}
	}
	static public function uploaded_file($info=false){
		static $cache;
		if (!isset($cache)) {
			if (is_array($info)) $cahe=$info;
			return false;
		}
		if ($info===false) return $cache;
		return isset($cache[$info])?$cache[$info]:false;
	}
	static public function tag_list(&$data,$skin=false,$limit=10){
		$offset=isset($_GET['offset'])?(int)$_GET['offset']:0;
		$query='SELECT m.id as id, m.name as name, m.mime as mime, m.binsize as size, m.time as time, 
			ExtractValue(m.xml,"width") as width, ExtractValue(m.xml,"height") as height, 
			ExtractValue(t.xml,"width") as twidth, ExtractValue(t.xml,"height") as theight 
			FROM jeans_binary as m LEFT JOIN jeans_binary as t 
			ON m.name=t.name AND m.contextid=t.contextid AND t.type="thumbnail" 
			WHERE m.type="media" AND m.contextid=<%id%>
			GROUP BY m.name ORDER BY m.time DESC 
			LIMIT <%limit%> OFFSET <%offset%>';
		$array=array('id'=>self::member_id(),'limit'=>$limit,'offset'=>$offset);
		$items=sql::count_query($query,$array);
		$data['libs']['page']=array('items'=>$items,'offset'=>$offset,'limit'=>$limit);
		view::show_using_query($data,$query,$array,$skin,array('admin_media','cb_tag_list'));
	}
	static public function cb_tag_list(&$row){
		$row['file']=self::member_id().'-'.$row['name'];
		if (substr($row['mime'],0,6)=='image/') {
			$row['type']='image';
			if (empty($row['twidth'])) {
				$width=$row['width'];
				$height=$row['height'];
			} else {
				$width=$row['twidth'];
				$height=$row['theight'];
			}
			if ($width<_CONF_THUMBNAIL_ADMIN_SIZE && $height<_CONF_THUMBNAIL_ADMIN_SIZE) {
				$row['twidth']=$width;
				$row['theight']=$height;
			} else {
				if ($width<$height) {
					$row['twidth']=intval($width * _CONF_THUMBNAIL_ADMIN_SIZE / $height);
					$row['theight']=intval(_CONF_THUMBNAIL_ADMIN_SIZE);
				} else {
					$row['theight']=intval($height * _CONF_THUMBNAIL_ADMIN_SIZE / $width);
					$row['twidth']=intval(_CONF_THUMBNAIL_ADMIN_SIZE);
				}
			}
			$row['thumbnail']='?action=media.thumbnail&file='.$row['file'];
		} else {
			$row['type']='media';
		}
	}
}