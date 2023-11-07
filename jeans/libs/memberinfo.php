<?php
/*
 * Jeans CMS (GPL license)
 * $Id: memberinfo.php 216 2010-06-27 18:42:54Z kmorimatsu $
 */

class memberinfo extends jeans {
	static private $memberid=0;
	static public function init(){
		if (isset($_GET['memberid'])) self::$memberid=(int)$_GET['memberid'];
		elseif (isset($_GET['mid'])) self::$memberid=(int)$_GET['mid'];
	}
	static public function set_id($id){
		if (!self::setting('id',$id)) return false;
		self::$memberid=$id;
		return true;
	}
	static public function setting($key,$memberid=false){
		static $cache;
		if (!isset($cache)) {
			$cache=array();
			$res=sql::query('SELECT * FROM jeans_member');
			while ($row=$res->fetch()) {
				$cache[$row['id']]=sql::convert_xml($row);
			}
		}
		if (!$memberid) $memberid=self::$memberid;
		if (!isset($cache[$memberid])) return false;
		if (!isset($cache[$memberid][$key])) return false;
		return $cache[$memberid][$key];
	}
	static public function tag_memberinfo(&$data,$key,$memberid=false){
		self::p(self::setting($key,$memberid));
	}
}