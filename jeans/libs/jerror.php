<?php
/*
 * Jeans CMS (GPL license)
 * $Id: jerror.php 367 2017-10-25 23:49:57Z kmorimatsu $
 */


class jerror extends jeans {
	static private function debug_mode(){
		if (!defined('_CONF_DEBUG_MODE')) return false;
		if ($_SERVER['REMOTE_ADDR']=='127.0.0.1') return true;
		if (_CONF_DEBUG_MODE==='admin') return member::is_admin();
		return true;
	}
	static public function quit($text,$data=array()){
		self::show($text,$data,'quit');
		exit;
	}
	static public function show($text,$data=array(),$log='show'){
		if (preg_match('/^_([A-Z][A-Z0-9]*)_([A-Z0-9]*)(?:_[A-Z0-9]*)*$/',$text,$m)) $text=self::translate($text);
		// Leave log
		$row=sql::query("SELECT count(*) as result FROM sqlite_master WHERE name='jeans_log'")->fetch();
		if ($row['result']==1) core::log(self::fill_html($text,$data),__CLASS__,$log);
		// Redirect, if possible
		if (!self::debug_mode()) {
			if (error_reporting() & E_WARNING) {
				$text=self::fill_html($text,$data);
				if (headers_sent() || isset($_GET['error'])) {
					self::echo_html('<!--\'"--><%0%>',$text);
				} else {
					core::set_cookie('note_text',$text);
					core::redirect_local('?error');
				}
			}
			return;
		}
		if (!is_array($data)) $data=array($data);
		self::echo_html('<!--\'"-->'.$text,$data);
		$db=debug_backtrace();
		self::echo_html('<!--');
		ob_start();
		print_r($db);
		self::p(ob_get_clean());
		self::echo_html('-->');
		for ($i=0;$i<count($db);$i++) {
			if ($db[$i]['class']=='error') continue;
			$db=$db[$i-1];
			break;
		}
		$db['file']=@preg_replace('/^.*[\/\\\\]([a-z0-9_]+\.php)$/i','$1',$db['file']);
		self::echo_html("<br />\r\nError occured at line <%line%> in <%file%>",$db);
	}
	static private $note=array();
	static public function note($text,$data=false){
		if ($data===false) {
			self::$note[]=self::quote_html($text);
		} else {
			if (preg_match('/^_([A-Z][A-Z0-9]*)_([A-Z0-9]*)(?:_[A-Z0-9]*)*$/',$text)) $text=self::translate($text);
			self::$note[]=self::fill_html($text,$data);
		}
	}
	static public function fatal($text=false,$data=false){
		static $fatal=false;
		if ($text===false) return $fatal;
		$fatal=true;
		self::note($text,$data);
	}
	static public function get_note(){
		foreach (self::$note as $key=>$error) {
			if (preg_match('/^_([A-Z][A-Z0-9]*)_([A-Z0-9]*)(?:_[A-Z0-9]*)*$/',$error)) self::$note[$key]=self::translate($error);
		}
		return self::$note;
	}
	static public function tag_note(&$data,$skin){
		$array=array();
		foreach (self::get_note() as $error) {
			$array[]=array('error'=>$error);
		}
		view::show_using_array($data,$array,$skin);
	}
	// From view.php
	static public function compile_error($source,$compiled){
		if (self::debug_mode()) {
			$array=array(&$source,&$compiled);
			foreach($array as &$temp) {
				$temp=preg_split('/(\r\n|\r|\n)/',$temp);
				foreach($temp as $key=>$line) $temp[$key]=substr('  '.($key+1).':',-4).$line;
				$temp=implode("\n",$temp);
			}
			self::echo_html('<hr /><pre><%0%><hr /><%1%></pre><hr />',array($source,$compiled),'hsc');
		}
		return function(){ return false; };
	}
	// From core.php
	static public function set_error($mode,$error,$array=array()){

	}
}