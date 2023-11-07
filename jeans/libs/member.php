<?php
/*
 * Jeans CMS (GPL license)
 * $Id: member.php 365 2017-10-25 20:06:22Z kmorimatsu $
 */

class member extends jeans{
	static public function init(){
		sql::init('member',_CONF_DB_LOGIN);
	}
	static public function setting($key=false) {
		static $row;
		if (is_array($key)) {
			if ($key===array('id'=>0) || !isset($row)) {
				// Reset or initialize
				$row=$key;
				return;
			}
		}
		if ($key===false) return $row;
		if (!isset($row[$key])) return false;
		return $row[$key];
	}
	static public function tag_setting(&$data,$key){
		self::p(self::setting($key));
	}
	const MEMBER_CAN_LOGIN=1;
	const MEMBER_IS_ADMIN=128;
	static public function auth($mode){
		return $mode & self::setting('authority');
	}
	static public function logged_in(){
		return (bool)self::setting('id');
	}
	static public function is_admin(){
		if (!self::setting('id')) return false;
		return self::auth(self::MEMBER_IS_ADMIN);
	}
	static public function if_loggedin(){
		return self::logged_in();
	}
	static public function if_isadmin(){
		return self::is_admin();
	}
	static public function login(){
		// Check if logged in
		if (isset($_POST['login']) && isset($_POST['password_text'])) {
			$name=$_POST['login'];
			$password=$_POST['password_text'];
			$row=self::try_login($name,$password);
			if ($row) {
				$name=$row['loginname'];
				$key=self::random_key();
				self::set_cookie($name,$key);
				$query='UPDATE jeans_login SET cookie=<%cookie%> WHERE id=<%id%>';
				$array=array('cookie'=>self::hash_cookie($key),'id'=>$row['id']);
				sql::select_pdo('member');
				sql::query($query,$array);
			}
		} elseif (isset($_COOKIE['login']) && isset($_COOKIE['loginkey'])) {
			$name=$_COOKIE['login'];
			$key=$_COOKIE['loginkey'];
			$row=self::cookie_login($name,$key);
			if ($row) self::set_cookie($name,$key);
			else self::unset_cookie();
		}
		// Check authority
		if ($row && !($row['authority']&self::MEMBER_CAN_LOGIN)) {
			$row=false;
			jerror::note('_JEANS_LOGIN_NOT_ALLOWED');
		}
		// Check multiple failure (blute fource attack?)
		$ip16=preg_replace('/\.[0-9]+\.[0-9]+$/','',$_SERVER['REMOTE_ADDR']);
		$stop=self::anti_blute_fource($name,$ip16);
		if ($stop) {
			jerror::note('Account is invalid for <%0%> seconds.',$stop);
			$row=false;
		}
		
		if ($row) {
			// sucess
			$row=array_merge($row,sql::query('SELECT * FROM jeans_member WHERE id=<%id%>',$row)->fetch());
			sql::convert_xml($row,'member');
			self::setting($row);
			if (isset($_POST['login'])) {
				core::log('Login Success',__CLASS__,$name);
				core::event('login_success',array());
			}
		} else {
			// fail
			self::setting(array('id'=>0));
			jerror::note('_JEANS_LOGIN_FAILED');
			if (isset($_POST['login'])) {
				core::event('login_failed',array('user'=>$_POST['login']));
				$text=self::fill_html('Login Failed <%0%>',$ip16);
				if ($stop<86400) core::log($text,__CLASS__,$name);
			}
		}
	}
	static private function anti_blute_fource($name,$ip16) {
		$query='SELECT time FROM jeans_log 
			WHERE desc=<%ip16%> AND type=<%name%> AND time >= <%time%> 
			ORDER BY time ASC LIMIT 17';
		$array=array(
			'ip16'=>"Login Failed $ip16",
			'name'=>$name,
			'time'=>gmdate('Y-m-d H:i:s', time()-86400));
		$stop=1;
		$res=sql::query($query,$array);
		$time='1970-01-01 00:00:00';
		while ($row=$res->fetch()) {
			$stop=$stop*2;
			$time=$row['time'];
		}
		$stop=$stop-1;
		if (86400<$stop) $stop=86400;
		if (gmdate('Y-m-d H:i:s', time()-$stop) < $time) return $stop;
		else return false;
	}
	static public function try_login($name,$password){
		// Restrict the length of password to 40. This is for avoiding hash colision by very long password.
		$password=substr($password,0,40);
		$query='SELECT id, loginname, authority, 1 as justloggedin FROM jeans_login 
			WHERE (loginname=<%loginname%> OR email=<%loginname%>) AND password=<%password%> LIMIT 1';
		$array=array('loginname'=>$name,'password'=>hash('sha512',_HASH_SALT.$password));
		sql::select_pdo('member');
		$row=sql::query($query,$array)->fetch();
		if ($row) return $row;
		$array=array('loginname'=>$name,'password'=>hash('sha512',$password));
		sql::select_pdo('member');
		$row=sql::query($query,$array)->fetch();
		if ($row) {
			// The hash salt has been set.
			// Let's update the hashed password in server.
			$query='UPDATE jeans_login SET password=<%new%> WHERE id=<%id%> AND password=<%old%>';
			$array=array(
				'old'=>hash('sha512',$password),
				'new'=>hash('sha512',_HASH_SALT.$password),
				'id'=>$row['id']);
			sql::select_pdo('member');
			sql::query($query,$array);
		}
		return $row;
	}
	static public function action_get_logout(){
		// Member must login for logging out.
		// Not log out when just logged in.
		// (User sometime tries to log in from the log-out page with ?action=member.logout).
		if (!self::setting('id')) return;
		if (self::setting('justloggedin')) return;
		core::event('logout',array('info'=>self::setting()));
		core::log('Logout Success',__CLASS__,self::setting('loginname'));
		self::set_cookie('','');
		$query='UPDATE jeans_login SET cookie="" WHERE id=<%0%>';
		sql::select_pdo('member');
		sql::query($query,self::setting('id'));
		// Reset setting for logging out in this connection
		self::setting(array('id'=>0));
		//TODO: redirect to URL without ?action=member.logout
	}
	static private function set_cookie($name,$key){
		if (isset($_POST['login']) && isset($_POST['password_text'])) {
			$shared=isset($_POST['shared']) && (bool)$_POST['shared'];
		} else {
			$shared=isset($_COOKIE['loginshared']) && (bool)$_COOKIE['loginshared'];
		}
		if ($shared) {
			core::set_cookie('login',$name,0);
			core::set_cookie('loginkey',$key,0);
			core::set_cookie('loginshared','true',0);
		} else {
			core::set_cookie('login',$name);
			core::set_cookie('loginkey',$key);
			core::set_cookie('loginshared','',-1);
		}
	}
	static private function unset_cookie(){
		core::set_cookie('login','',-1);
		core::set_cookie('loginkey','',-1);
		core::set_cookie('loginshared','',-1);
	}
	static private function cookie_login($name,$key){
		$query='SELECT id,loginname,authority FROM jeans_login WHERE loginname=<%loginname%> AND cookie=<%cookie%>';
		$array=array('loginname'=>$name,'cookie'=>self::hash_cookie($key));
		sql::select_pdo('member');
		$row=sql::query($query,$array)->fetch();
		return $row;
	}
	static private function hash_cookie($key){
		// secure cookie key settings (either 0, 8, 16, 24, or 32; default=24)
		// If IPv6 is used, settings 8, 16, 24, and 32 are the same (all IP is used).
		switch(_CONF_SECURE_COOKIE_KEY){
			case  8: 
				$addr=preg_replace('/\.[0-9]+\.[0-9]+\.[0-9]+$/','',$_SERVER['REMOTE_ADDR']);
				break;
			case 16:
				$addr=preg_replace('/\.[0-9]+\.[0-9]+$/','',$_SERVER['REMOTE_ADDR']);
				break;
			case 24:
				$addr=preg_replace('/\.[0-9]+$/','',$_SERVER['REMOTE_ADDR']);
				break;
			case 32:
				$addr=$_SERVER['REMOTE_ADDR'];
				break;
			default:
				$addr='';
		}
		return hash('sha512',_HASH_SALT.$addr.$key);
	}
}