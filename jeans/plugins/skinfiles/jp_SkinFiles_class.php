<?php
/*
 * Jeans CMS (GPL license)
 * $Id: jp_SkinFiles_class.php 365 2017-10-25 20:06:22Z kmorimatsu $
 */

class jp_skinfiles_class extends jeans {
	static private $extends=array('jeans');
	static private $dir_path='',$file_path='',$file='';
	static private $real_dir_path='', $real_file_path='';
	static public function init(){
		// Only superadmin can use this class
		if (!member::is_admin()) jerror::quit('_ADMIN_NO_PERMISSION');
		// Get the path and resolve skin extension
		// Note that the dir_path must end with '/' except for root path.
		if (isset($_GET['dir_path'])) self::$dir_path=$_GET['dir_path'];
		if (preg_match('#^([^/]+)/#',self::$dir_path,$m)) {
			$skin=$m[1];
			$text=self::local_file_contents(_DIR_SKINS,"$skin/skin.inc");
			if ($text===false) $text=self::local_file_contents(_DIR_SKINS,"$skin/adminskin.inc");
			if ($text && preg_match('/<%view.extends\(([a-zA-Z0-9,]+)\)%>/',$text,$m)) {
				$extends=explode(',',$m[1]);
				array_unshift($extends,'jeans');
				self::$extends=$extends;
			}
		}
		// Create media sub directory when the owner is accessing
		$mid=member::setting('id');
		if (preg_match("#^media/$mid/$#",self::$dir_path)) {
			if (!self::local_file_exists(_DIR_SKINS,'media/')) {
				$perms=fileperms(_DIR_SKINS);
				@mkdir(_DIR_SKINS.'media/');
			}
			if (!self::local_file_exists(_DIR_SKINS,self::$dir_path)) {
				$perms=fileperms(_DIR_SKINS.'media/');
				@mkdir(_DIR_SKINS.self::$dir_path,$perms);
			}
		}
		// Get the file and resolve real path
		self::$file=$file=isset($_GET['file']) ? $_GET['file']:'';
		self::$file_path=self::$dir_path.$file;
		self::$real_dir_path=self::to_realpath(self::$dir_path);
		self::$real_file_path=self::to_realpath(self::$file_path);
		if (self::$real_dir_path===false) {
			jerror::note('_JP_SKINFILES_DIR_NOT_FOUND',self::$dir_path);
			self::$real_dir_path=self::$dir_path='';
			self::$real_file_path=self::$file_path=self::$file='';
		} elseif (self::$real_file_path===false) {
			jerror::note('_JP_SKINFILES_FILE_NOT_FOUND',self::$file_path);
			self::$real_dir_path=self::$dir_path='';
			self::$real_file_path=self::$file_path=self::$file='';
		}
		if (self::$dir_path=='') self::$extends=array();
	}
	static private function to_realpath($virtual_path){
		if (self::local_file_exists(_DIR_SKINS,$virtual_path)) return $virtual_path;
		$pos=strpos($virtual_path,'/');
		if ($pos===false) return self::local_file_exists(_DIR_SKINS,$virtual_path) ? $virtual_path:false;
		$path=substr($virtual_path,$pos);
		if ($path==='/') return false;
		foreach(self::$extends as $skin){
			$real_path=$skin.$path;
			if (self::local_file_exists(_DIR_SKINS,$real_path)) return $real_path;
		}
		return false;
	}
	static public function tag_breadcrumbs(&$data,$skin=false){
		$array=array();
		$dir_path='';
		foreach(explode('/',self::$dir_path) as $dir){
			if (!$dir) continue;
			$dir_path.="$dir/";
			$row=array();
			$row['type']='dir';
			$row['name']=$dir;
			$row['link']='?padmin=jp_skinfiles&dir_path='.$dir_path;
			$array[]=$row;
		}
		if (strlen(self::$file)) {
			$row=array();
			$row['type']='file';
			$row['name']=self::$file;
			$row['link']='?padmin=jp_skinfiles&dir_path='.$dir_path.'&file='.self::$file;
			$array[]=$row;
		}
		view::show_using_array($data,$array,$skin);
	}
	static public function tag_dir(&$data,$skin=false){
		$image_exists=false;
		$files=array();
		if (preg_match('#^[^/]+/(.*)$#',self::$dir_path,$m)) {
			foreach(self::$extends as $parent_skin){
				$files=array_merge($files,self::file_list("$parent_skin/$m[1]",true));
			}
		}
		$files=array_merge($files,self::file_list(self::$real_dir_path,self::if_virtualdir()));
		$array=array();
		foreach($files as $row) {
			$array[$row['name']]=$row;
			$image_exists |= ($row['type']=='image');
		}
		$data['jp']['skinfiles']['image_exists']=$image_exists;
		// paging information
		$items=count($array);
		if (isset($_GET['offset'])) {
			$offset=(int)$_GET['offset'];
			$limit=isset($_GET['limit']) ? (int)$_GET['limit']:10;
		} else {
			$offset=0;
			$limit=$items;
		}
		$data['libs']['page']=array('items'=>$items,'offset'=>$offset,'limit'=>$limit);
		$array=array_slice($array,$offset,$limit,true);
		// show them
		view::show_using_array($data,$array,$skin);
	}
	static private function file_list($dir,$virtual=false){
		$order=isset($_GET['order']) ? $_GET['order']:'az';
		$files=$dirs=array();
		if (!self::local_file_exists(_DIR_SKINS,$dir)) return array();
		$d=dir(_DIR_SKINS.$dir);
		while(($file=$d->read())!==false){
			if ($file=='.' || $file=='..') continue;
			$path=_DIR_SKINS.$dir.$file;
			$text_ext=self::is_textfile($file);
			$width=$height=0;
			if (is_dir($path)) {
				$type='dir';
			} elseif(self::is_imagefile($file)) {
				$type='image';
				$size=@getimagesize(_DIR_SKINS.$dir.$file);
				if ($size) list($width,$height)=$size;
			} else {
				switch($text_ext){
					case 'php':
						$type='php';
						break;
					case 'htm': case 'html':
						$type='html';
						break;
					case false:
						$type='other';
						break;
					default:
						$type='text';
				}
			}
			$size=filesize($path);
			if (1048576<=$size) $size=round($size/1048576,1).' MB';
			elseif (1024<=$size) $size=round($size/1024,1).' KB';
			else $size=$size.' Bytes';
			switch($type){
				case 'dir':
					$size='';
					break;
				case 'image':
					$size="$width x $height ($size)";
				default:
					break;
			}
			$array=array(
				'name'=>$file,
				'time'=>filemtime($path),
				'size'=>$size,
				'virtual'=>$virtual,
				'writable'=>is_writable($path),
				'type'=>$type,
				'width'=>$width,
				'height'=>$height);
			if ($order=='on'||$order=='no') $key=gmdate('Y-m-d H:i:s', $array['time']).$file;
			else $key=$file;
			if ($type=='dir') $dirs[$key]=$array;
			else $files[$key]=$array;
		}
		if ($order=='no'||$order=='za') {
			krsort($files);
			krsort($dirs);
		} else {
			ksort($files);
			ksort($dirs);
		}
		return array_merge($dirs,$files);
	}
	static private function is_textfile($name){
		static $search;
		if (!isset($search)) {
			$extensions=strtolower(jp_SkinFiles::option()->text_ext);
			$search=preg_split('/[,\s]+/',$extensions,-1,PREG_SPLIT_NO_EMPTY);
		}
		$name=strtolower($name);
		if (!preg_match('/\.([a-z0-9]+)$/',$name,$m)) return false;
		if (!in_array($m[1],$search)) return false;
		return $m[1];
	}
	static private function is_imagefile($name){
		static $search;
		if (!isset($search)) {
			$extensions=strtolower(jp_SkinFiles::option()->image_ext);
			$search=preg_split('/[,\s]+/',$extensions,-1,PREG_SPLIT_NO_EMPTY);
		}
		$name=strtolower($name);
		if (!preg_match('/\.([a-z0-9]+)$/',$name,$m)) return false;
		if (!in_array($m[1],$search)) return false;
		return $m[1];
	}
	static public function tag_setting(&$data,$key){
		switch($key){
			case 'dir':
				return self::p(self::$dir_path);
			case 'file':
				return self::p(self::$file);
			case 'real_dir':
				return self::p(self::$real_dir_path);
			case 'real_dir_for_file':
				if (self::$file!=='') return self::p(dirname(self::$real_file_path).'/');
				else return self::p(self::$real_file_path);
			case 'real_file':
				return self::p(self::$real_file_path);
			case 'basename':
				if (preg_match('#([^/]+)/?$#',self::$real_file_path,$m)) self::p($m[1]);
		}
	}
	static public function tag_showtext(){
		if (!self::is_textfile(self::$file)) return;
		$text=self::local_file_contents(_DIR_SKINS,self::$real_file_path);
		self::p($text,'hsc');
	}
	static public function tag_skinname(){
		$m=self::if_skinname();
		if ($m) self::p($m[1]);
	}
	static public function tag_extends(&$data,$skin=false){
		$array=array();
		foreach(self::$extends as $each) $array[]=array('name'=>$each);
		view::show_using_array($data,$array,$skin);
	}
	static public function tag_link(&$data,$key,$value=false){
		$array=$_GET;
		switch($key){
			case 'orderbyname':
				if (!isset($_GET['order']) || $_GET['order']!='za') $array['order']='za';
				else $array['order']='az';
				break;
			case 'orderbydate':
				if (!isset($_GET['order']) || $_GET['order']!='no') $array['order']='no';
				else $array['order']='on';
				break;
			default:
				$array[$key]=$value;
		}
		self::p(view::create_link($array));
	}
	static public function if_skinname(){
		preg_match('#^([^/]+)/#',self::$dir_path,$m);
		return $m;
	}
	static public function if_file(){
		return self::$file!=='' && !is_dir(_DIR_SKINS.self::$real_file_path);
	}
	static public function if_text(){
		return self::is_textfile(self::$file);
	}
	static public function if_image(){
		return self::is_imagefile(self::$file);
	}
	static public function if_virtualdir(){
		return self::$dir_path!=self::$real_dir_path;
	}
	static public function if_virtualfile(){
		return self::$file_path!=self::$real_file_path;
	}
	static public function if_mediadir(){
		return preg_match('#^media/[0-9]+/#',self::$dir_path);
	}
	static public function if_thumbnail(){
		return !empty($_GET['thumbnail']);
	}
	static public function action_get_thumbnail(){
		// Check the local file.
		$full_path=self::local_file_exists(_DIR_SKINS,self::$real_file_path);
		if (!$full_path) jerror::quit('File not found');
		$time=filemtime(_DIR_SKINS.self::$real_file_path);
		// Check if modified (the browser would store a cache).
		media::check_if_changed($time);
		// Check if the file is an image.
		if (!preg_match('/\.([^\.]+)$/',self::$file,$m)) jerror::quit('Not an image.');
		$mime=tables::mime($m[1]);
		if (substr($mime,0,6)!='image/') jerror::quit('Not an image.');
		// Get the image info
		list($width,$height,$new_width,$new_height)=self::image_size(self::$real_file_path);
		// Return the image to browser (use tumbnail if possible.
		if ($width!=$new_width || $height!=$new_height) {
			$gd=admin_thumbnail::gd($full_path,$mime);
			if ($gd) $file=$gd[2]['file'];
		}
		if (!isset($file)) $file=self::local_file_contents(_DIR_SKINS,self::$real_file_path);
		media::send_media($file,$mime,$time);
	}
	static public function tag_thumbnail(&$data){
		$file=$data['name'];
		// Check if the file is an image.
		if (!preg_match('/\.([^\.]+)$/',$file,$m)) jerror::quit('Not an image.');
		$mime=tables::mime($m[1]);
		if (substr($mime,0,6)!='image/') jerror::quit('Not an image.');
		// Get the image info
		list($width,$height,$new_width,$new_height)=self::image_size(self::to_realpath(self::$real_dir_path.$file));
		// Show the img tag
		$html='<img src="<%url%>" width="<%width%>" height="<%height%>" alt="<%alt%>" />';
		$array=array(
			'url'=>_CONF_SELF.'?action=jp.skinfiles.class.thumbnail&dir_path='.self::$dir_path.'&file='.$file,
			'width'=>$new_width,
			'height'=>$new_height,
			'alt'=>$file);
		self::echo_html($html,$array);		
	}
	static private function image_size($file_path){
		$full_path=self::local_file_exists(_DIR_SKINS,$file_path);
		if (!$full_path) return false;
		$info=@getimagesize(_DIR_SKINS.$file_path);
		$width=$info[0];
		$height=$info[1];
		// Decide the new size.
		if ($width>=$height && $width>_CONF_THUMBNAIL_ADMIN_SIZE) {
			$new_width=intval(_CONF_THUMBNAIL_ADMIN_SIZE);
			$new_height=intval($height * _CONF_THUMBNAIL_ADMIN_SIZE / $width);
		} elseif ($height>$width && $height>_CONF_THUMBNAIL_ADMIN_SIZE) {
			$new_width=intval($width * _CONF_THUMBNAIL_ADMIN_SIZE / $height);
			$new_height=intval(_CONF_THUMBNAIL_ADMIN_SIZE);
		} else {
			$new_width=$width;
			$new_height=$height;
		}
		return array($width,$height,$new_width,$new_height);
	}
	static public function action_get_download(){
		$filename=self::$file;
		preg_match('/([^\.]*)$/',$filename,$m);
		$mime=tables::mime($m[1]);
		header("Content-Type: $mime; name=\"$filename\"");
		header("Content-disposition: attachment; filename=$filename");
		$full_path=self::local_file_exists(_DIR_SKINS,self::$real_file_path);
		header('Content-length: '.filesize($full_path));
		readfile($full_path);
		exit;
	}
	static public function action_post_newdir(){
		if (empty($_POST['newdir'])) return;
		if (self::local_file_exists(_DIR_SKINS,self::$real_dir_path.$_POST['newdir'])) {
			jerror::note('_JP_SKINFILES_DIR_ALREADY_EXISTS');
			return;
		}
		$perms=fileperms(_DIR_SKINS.self::$real_dir_path);
		if (@mkdir(_DIR_SKINS.self::$real_dir_path.$_POST['newdir'],$perms)) {
			jerror::note('_JP_SKINFILES_NEW_DIR_WAS_CREATED');
		} else {
			jerror::note('_JP_SKINFILES_CREATING_NEW_DIR_FAILED');
		}
	}
	static public function action_post_newfile(){
		if (empty($_POST['newfile'])) return;
		if (self::local_file_exists(_DIR_SKINS,self::$real_dir_path.$_POST['newfile'])) {
			jerror::note('_JP_SKINFILES_FILE_ALREADY_EXISTS');
			return;
		}
		$res=@fopen(_DIR_SKINS.self::$real_dir_path.$_POST['newfile'],'x');
		if ($res) {
			fclose($res);
			jerror::note('_JP_SKINFILES_NEW_FILE_WAS_CREATED');
		} else {
			jerror::note('_JP_SKINFILES_CREATING_NEW_FILE_FAILED');
		}
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
		if (_CONF_MAX_UPLOAD_SIZE < $size) {
			@unlink($file['tmp_name']);
			return jerror::note(self::translate('_ADMIN_MEDIA_FILE_TOO_LARGE').'(max: '._CONF_MAX_UPLOAD_SIZE.')');
		}
		if (!preg_match('/^(.+\.)([a-zA-Z0-9]+)$/',trim($file['name']),$m)) {
			@unlink($file['tmp_name']);
			return jerror::note('_ADMIN_MEDIA_INVALID_FILENAME');
		}
		$m[2]=strtolower($m[2]);
		$m[1]=self::utf8($m[1]);
		if (strlen($m[1])==0) {
			@unlink($file['tmp_name']);
			return jerror::note('_ADMIN_MEDIA_INVALID_FILENAME');
		}
		$filename=$m[1].$m[2];
		if (self::if_mediadir() && jp_skinfiles::option()->date_prefix) {
			$filename=date('Y-m-d-').$filename;
		}
		// Check if destination is OK
		if (self::local_file_exists(_DIR_SKINS,self::$real_dir_path.$filename)) {
			jerror::note('_JP_SKINFILES_FILE_ALREADY_EXISTS');
			return;
		}
		// Everything is file.  Let's move file to skins/ directory
		if (@move_uploaded_file ($file['tmp_name'],_DIR_SKINS.self::$real_dir_path.$filename)) {
			jerror::note('_JP_SKINFILES_FILE_IS_UPLOADED');
		} else {
			@unlink($file['tmp_name']);
			jerror::note('_JP_SKINFILES_FILE_UPLOAD_WAS_FALIED');
		}
	}
	static public function action_get_mediamanager(){
		$mid=member::setting('id');
		core::redirect_local('?padmin=jp_skinfiles&dir_path=media/'.$mid.'/&order=no&thumbnail=1&offset=0');
	}
	static private function more_params(){
		$params='';
		if (strlen($_POST['order'])) $params.="&order=".self::quote_html($_POST['order']);
		if (strlen($_POST['thumbnail'])) $params.="&thumbnail=".intval($_POST['thumbnail']);
		if (strlen($_POST['offset'])) $params.="&offset=".intval($_POST['offset']);
		return $params;
	}
	static public function action_post_rename(){
		$new_path=dirname(self::$real_file_path).'/'.$_POST['new_path'];
		if (self::local_file_exists(_DIR_SKINS,$new_path)) {
			if (is_dir(_DIR_SKINS.$new_path)) jerror::note('_JP_SKINFILES_DIR_ALREADY_EXISTS');
			else jerror::note('_JP_SKINFILES_FILE_ALREADY_EXISTS');
			return;
		}
		if (@rename(_DIR_SKINS.self::$real_file_path,_DIR_SKINS.$new_path)) {
			core::set_cookie('note_text','_JP_SKINFILES_RENAMED',0);
			core::redirect_local('?padmin=jp_skinfiles&dir_path='.self::$dir_path.self::more_params());
		} else {
			jerror::note('_JP_SKINFILES_RENAME_WAS_FAILED');
		}
	}
	static public function action_post_delete(){
		if (!self::local_file_exists(_DIR_SKINS,self::$real_file_path)) {
			jerror::note('_JP_SKINFILES_FILE_NOT_FOUND');
			return;
		}
		$path=_DIR_SKINS.self::$real_file_path;
		if (is_dir($path) ? @rmdir($path):@unlink($path)) {
			core::set_cookie('note_text','_JP_SKINFILES_DELETED',0);
			core::redirect_local('?padmin=jp_skinfiles&dir_path='.self::$dir_path.self::more_params());
		} else {
			jerror::note('_JP_SKINFILES_DELETION_WAS_FAILED');
			if (is_dir($path)) jerror::note('_JP_SKINFILES_CONFIRM_IF_EMPTY');
		}
	}
	static public function action_post_edit(){
		if (!self::local_file_exists(_DIR_SKINS,self::$real_file_path)) {
			jerror::note('_JP_SKINFILES_FILE_NOT_FOUND');
			return;
		}
		if (file_put_contents(_DIR_SKINS.self::$real_file_path,$_POST['save_text'])) {
			core::set_cookie('note_text','_JP_SKINFILES_SAVED',0);
			core::redirect_local('?padmin=jp_skinfiles&dir_path='.self::$dir_path);
		} else {
			jerror::note('_JP_SKINFILES_SAVING_WAS_FAILED');
		}
	}
}