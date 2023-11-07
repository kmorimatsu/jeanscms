<?php
/*
 * Jeans CMS (GPL license)
 * $Id: install.php 373 2023-07-31 00:08:47Z kmorimatsu $
 */

class install extends jeans{
	static private $language=array();
	static private $default=false;
	static public function init(){
		// List up available language files.
		$dir = dir(dirname(__FILE__).'/install/language');
		while (false !== ($entry = $dir->read())) {
			if (preg_match('/^(.+)\.php$/',$entry,$m)) self::$language[]=$m[1];
		}
		$dir->close();
		// Load language file
		if (isset($_GET['language']) && in_array($_GET['language'],self::$language)) {
			self::include_local(dirname(__FILE__).'/install/language/',$_GET['language'].'.php');
		}
		// Detemine user's default language from header from browser.
		if (preg_match_all('/[a-z\-]+/i',$_SERVER['HTTP_ACCEPT_LANGUAGE'],$matches,PREG_SET_ORDER)) {
			foreach($matches as $m){
				$lang=tables::language($m[0]);
				if (!in_array($lang,self::$language)) continue;
				self::$default=$lang;
				break;
			}
		}
		if (self::$default && !defined('_INSTALL_WELCOME')) {
			self::include_local(dirname(__FILE__).'/install/language/',self::$default.'.php');
		}
	}
	static public function selector(){
		if (!isset($_GET['language'])) $template='language';
		elseif (!in_array($_GET['language'],self::$language)) $template='language';
		elseif (count($_POST)) {
			// Posted values are handled here.
			// Action cannot be used because ticket class is not available.
			self::posted();
			if (!core::error_exists()) $template='done';
			elseif (error::fatal()) $template='failed';
			else $template='install';
		} else $template='install';
		$skin='/install/skin.inc';
		$parent_skin=false;
		$data=false;
		view::parse_skin($skin,$parent_skin,$data,$template);
	}
	static public function tag_setdata(&$data){
		$data['sitename']='My Jeans CMS';
		$data['timezone']=@date_default_timezone_get();
		foreach(self::item_from_post() as $key=>$value) $data[$key]=$value;
	}
	static private function item_from_post(){
		$post=array();
		foreach($_POST as $key=>$value){
			if (substr($key,-5)=='_text') $key=substr($key,0,-5);
			$post[$key]=$value;
		}
		return $post;
	}
	static private function posted(){
		// Check values
		$post=self::item_from_post();
		if (empty($post['sitename'])) error::note('_INSTALL_NO_SITE_NAME');
		if (!@date_default_timezone_set($post['timezone'])) error::note('_INSTALL_INVALIED_TIME_ZONE');
		if (empty($post['email'])) error::note('_INSTALL_NO_EMAIL');
		if (empty($post['loginname'])) error::note('_INSTALL_NO_LOGIN_NAME');
		if (empty($post['name'])) error::note('_INSTALL_NO_NAME');
		if (strlen($post['password1'])<6) error::note('_INSTALL_PASSWORD_TOO_SHORT');
		if ($post['password1']!=$post['password2']) error::note('_INSTALL_PASSWORD_MISMATCH');
		if (core::error_exists()) return;
		$post['password']=hash('sha512',_HASH_SALT.$post['password1']);
		unset($post['password1']);
		unset($post['password2']);
		
		// Add additional information
		$url=substr(_DIR_ROOT,strlen(realpath($_SERVER['SCRIPT_FILENAME']))-strlen($_SERVER['SCRIPT_NAME']));
		$url=strtr($url,'\\','/');
		$url='/'.preg_replace('#(^/+|/+$)#','',$url).'/';
		if ($url=='//') $url='/';
		if (isset($_SERVER['HTTPS'])) {
			$url=self::hsc('https://'.$_SERVER['HTTP_HOST'].$url);
		} else {
			$url=self::hsc('http://'.$_SERVER['HTTP_HOST'].$url);
		}
		$post['url_index']=$url;
		$post['url_skins']=$url.'skins/';
		$post['url_admin']=$url.'jeans/';
		$post['url_plugins']=$url.'jeans/plugins/';
		$post['cookie_prefix']='jeans_'.preg_replace('/[0-9]/','',sha1(__FILE__)).'_';
		
		// Construct main DB
		ob_start();
		view::parse_skin('/install/sqlmain.inc',false,$post);
		$query=ob_get_clean();
		if (!self::query($query,'main')) return;
		
		// Construct member DB
		sql::init('member',_CONF_DB_LOGIN);
		ob_start();
		view::parse_skin('/install/sqlmember.inc',false,$post);
		$query=ob_get_clean();
		if (!self::query($query,'member')) return;

		// Save the default settings
		$query='SELECT d.name as name, d.defvalue as value, c.id as id 
			FROM jeans_config_desc as d 
			LEFT JOIN jeans_config as c 
			ON d.name=c.name AND c.type="global" AND c.owner="jeans" and c.contextid=0 
			WHERE d.configtype="global" AND d.owner="jeans" AND NOT d.type="separator"';
		$res=sql::query($query);
		$query='INSERT OR REPLACE INTO jeans_config(name,value) VALUES (<%name%>,<%value%>)';
		while ($row=$res->fetch()) {
			if (!$row['id']) sql::register_shutdown_query($query,$row);
		}
	}
	static private function query($query,$mode='main'){
		if (preg_match_all("/((?:[^';]*|'[^']*')*);/",$query,$matches,PREG_SET_ORDER)) {
			sql::begin($mode);
			foreach($matches as $m){
				sql::select_pdo($mode);
				$res=sql::query($m[1]);
				if (sql::pdo($mode)->errorCode()=='0000') continue;
				sql::commit($mode);
				$e=sql::pdo($mode)->errorInfo();
				error::fatal('SQLite error: <%0%>',$e[2]);
				error::fatal('<%0%>',$m[1]);
				return false;
			}
			sql::commit($mode);
		}
		return true;
	}
	static public function tag_langlist(&$data,$skin){
		$array=array();
		foreach(self::$language as $lang) {
			$row=array('language'=>$lang);
			if ($lang==self::$default) $row['selected']=true;
			$array[]=$row;
		}
		view::show_using_array($data,$array,$skin);
	}
	static public function if_dbok(){
		$file=dirname(_CONF_DB_LOGIN.'/.htdummy');
		$handle=@fopen($file,'w');
		if ($handle) {
			fclose($handle);
			unlink($file);
			return true;
		} else {
			return false;
		}
	}
}