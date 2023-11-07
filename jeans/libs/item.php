<?php
/*
 * Jeans CMS (GPL license)
 * $Id: item.php 303 2010-10-24 04:26:27Z kmorimatsu $
 */

class item extends jeans{
	static private $itemid=0;
	static public function set_id($id){
		$row=self::item_query($id);
		if (!isset($row['id'])) return false;
		self::$itemid=$id;
		return true;
	}
	static public function init(){
		if (isset($_GET['itemid'])) self::$itemid=(int)$_GET['itemid'];
	}
	static private function category(){
		return isset($_GET['sgid'])?$_GET['sgid']:false;
	}
	static private function item_query($itemid){
		// TODO: support multiple item selection
		static $cache=array();
		if (!isset($cache[$itemid])) {
			$query=blog::query_select('i.id=<%itemid%> LIMIT 1');
			$array=array('itemid'=>$itemid);
			$cache[$itemid]=sql::query($query,$array)->fetch();
			sql::convert_xml($cache[$itemid],'item');
			$cat_array=array('sgid'=>(int)$cache[$itemid]['sgid']);
			$item_array=array('itemid'=>(int)$cache[$itemid]['id']);
			if (self::category()) $item_array=array_merge($item_array,$cat_array);
			$cache[$itemid]['link']=view::create_link($item_array);
			$cache[$itemid]['clink']=view::create_link($cat_array);
			$cache[$itemid]['alink']=view::create_link(array('memberid'=>(int)$cache[$itemid]['author']));
		}
		return $cache[$itemid];
	}
	static public function setting($key=false,$itemid=false){
		if ($itemid===false) $itemid=self::$itemid;
		$row=self::item_query($itemid);
		if ($key===false) return $row;
		if (isset($row[$key])) return $row[$key];
		else return false;
	}
	static public function tag_item(&$data,$template,$itemid=false){
		// TODO: support showing multiple items
		if (!$itemid) $itemid=self::$itemid;
		$row=self::item_query($itemid);
		view::show_using_array($data,array($row),$template,array('blog','_blog_cb'),array('blog','_blog_cb2'));
	}
	static public function tag_data(&$data,$key){
		$row=self::item_query(self::$itemid);
		data::tag_data($row,$key);
	}
	static public function tag_shorten(&$data,$length,$toadd,$key){
		$row=self::item_query(self::$itemid);
		data::tag_shorten($row,$length,$toadd,$key);
	}
	static public function tag_hsc(&$data,$key){
		$row=self::item_query(self::$itemid);
		data::tag_hsc($row,$key);
	}
	static public function tag_raw(&$data,$key){
		$row=self::item_query(self::$itemid);
		data::tag_raw($row,$key);
	}
	static public function tag_parse(&$data,$key){
		$row=self::item_query(self::$itemid);
		data::tag_parse($row,$key);
	}
	static private function older(){
		static $row;
		if (!isset($row)) {
			$query='SELECT i.id FROM jeans_item as i, jeans_group as c 
				WHERE i.time<=<%time%> 
				AND NOT i.id=<%id%> 
				AND i.gid=<%blogid%> 
				AND i.sgid=c.id 
				'.(self::category()?'AND i.sgid=<%sgid%> ':'').'
				AND NOT (i.flags & <%const:sql::FLAG_HIDDEN%>) 
				AND NOT (c.flags & <%const:sql::FLAG_HIDDEN%>) 
				ORDER BY time DESC LIMIT 1';
			$array=self::setting();
			$row=sql::query($query,$array)->fetch();
			if ($row) $row=self::item_query($row['id']);
		}
		return $row;
	}
	static public function newer(){
		static $row;
		if (!isset($row)) {
			$query='SELECT i.id FROM jeans_item as i, jeans_group as c 
				WHERE i.time>=<%time%> 
				AND i.time<=<%const:_NOW%> 
				AND NOT i.id=<%id%> 
				AND i.gid=<%blogid%> 
				AND i.sgid=c.id 
				'.(self::category()?'AND i.sgid=<%sgid%> ':'').'
				AND NOT (i.flags & <%const:sql::FLAG_HIDDEN%>) 
				AND NOT (c.flags & <%const:sql::FLAG_HIDDEN%>) 
				ORDER BY time ASC LIMIT 1';
			$array=self::setting();
			$row=sql::query($query,$array)->fetch();
			if ($row) $row=self::item_query($row['id']);
		}
		return $row;
	}
	static public function tag_older(&$data,$key){
		$row=self::older();
		if (!$row) {
			$b_or_c=self::category()?'sgid':'gid';
			$row=array('link'=>view::create_link(array($b_or_c=>self::setting($b_or_c))));
		}
		if (!isset($row[$key])) return;
		self::p(strip_tags($row[$key]));
	}
	static public function tag_newer(&$data,$key){
		$row=self::newer();
		if (!$row) {
			$b_or_c=self::category()?'sgid':'gid';
			$row=array('link'=>view::create_link(array($b_or_c=>self::setting($b_or_c))));
		}
		if (!isset($row[$key])) return;
		self::p(strip_tags($row[$key]));
	}
	static public function if_olderis(&$data,$value=false,$key='id'){
		$row=self::older();
		if (!$row) return false;
		if ($value===false) return true;
		if (!isset($row[$key])) return false;
		return $value==$row[$key];
	}
	static public function if_neweris(&$data,$value=false,$key='id'){
		$row=self::newer();
		if (!$row) return false;
		if ($value===false) return true;
		if (!isset($row[$key])) return false;
		return $value==$row[$key];
	}
}