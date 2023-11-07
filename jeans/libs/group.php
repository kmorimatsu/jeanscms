<?php
/*
 * Jeans CMS (GPL license)
 * $Id: group.php 365 2017-10-25 20:06:22Z kmorimatsu $
 */

class group extends jeans {
	static private $groupid,$subgroupid=0;
	static public function init(){
		// Note: FLAG_INVALID is not checked in this class (it may be used in admin area).
		if (isset($_GET['gid'])) self::$groupid=(int)$_GET['gid'];
		if (isset($_GET['sgid'])) {
			$row=self::sgsetting(false,$_GET['sgid']);
			if ($row) {
				self::$subgroupid=$row['id'];
				if ($row['gid']) self::$groupid=(int)$row['gid'];
				elseif ($row['sgid']==0) self::$groupid=(int)$row['id'];
				else jerror::fatal('_JEANS_ERROR_SUBGROUP_NOT_EXIST');
			} else jerror::fatal('_JEANS_ERROR_SUBGROUP_NOT_EXIST');
		} elseif (isset($_GET['itemid'])) {
			$query='SELECT gid,sgid FROM jeans_item WHERE id=<%itemid%>';
			$array=array('itemid'=>(int)$_GET['itemid']);
			$row=sql::query($query,$array)->fetch();
			if ($row) {
				self::$groupid=(int)$row['gid'];	
				self::$subgroupid=(int)$row['sgid'];	
			} else jerror::fatal('_JEANS_ERROR_ITEM_NOT_EXIST');
		}
		if (!isset(self::$groupid)) self::$groupid=(int)_CONF_DEFAULT_GROUP;
	}
	static public function set_id($id){
		if ($id && !self::setting('id',$id)) return false;
		self::$groupid=$id;
		return true;
	}
	static public function set_sgid($id){
		if ($id && !self::sgsetting('id',$id)) return false;
		self::$subgroupid=$id;
		return true;
	}
	static public function setting($key=false,$groupid=false){
		static $cache;
		if (!isset($cache)) {
			$cache=$blogs=array();
			$query='SELECT * FROM jeans_group WHERE gid=0 AND sgid=0';
			$res=sql::query($query);
			while ($row=$res->fetch()) {
				sql::convert_xml($row,'group');
				$blogs[]=$row['id'];
				$cache[$row['id']]=$row;
			}
		}
		if (!$groupid) $groupid=self::$groupid;
		if (!isset($cache[$groupid])) return false;
		if ($key===false) return $cache[$groupid];
		if (!isset($cache[$groupid][$key])) return false;
		return $cache[$groupid][$key];
	}
	static public function tag_setting(&$data,$key,$mode='notag',$group=false){
		$text=self::setting($key,$group);
		if ($mode=='raw') self::echo_html($text);
		else self::p($text,$mode);
	}
	static public function sgsetting($key=false,$sgid=false){
		static $cache=array();
		if (!$sgid) $sgid=self::$subgroupid;
		if (!$sgid) return false;
		if (!isset($cache[$sgid])) {
			$query='SELECT * FROM jeans_group WHERE id=<%sgid%> LIMIT 1';
			$data=array('sgid'=>$sgid);
			$cache[$sgid]=sql::query($query,$data)->fetch();
			if (!$cache[$sgid]) $cache[$sgid]=array();
			sql::convert_xml($cache[$sgid],'subgroup');
		}
		if ($key===false) return $cache[$sgid];
		if (!isset($cache[$sgid][$key])) return false;
		return $cache[$sgid][$key];
	}
	static public function tag_sgsetting(&$data,$key,$mode='notag',$sgid=false){
		$text=self::sgsetting($key,$sgid);
		if ($mode=='raw') self::echo_html($text);
		else self::p($text,$mode);
	}
	static public function all_subgroups($gid,$sgid){
		$query='SELECT * FROM jeans_group '.
			'WHERE (gid=<%gid%> AND NOT sgid=<%gid%>) '.
			'OR id=<%sgid%>';
		$array=array('gid'=>$gid, 'sgid'=>$sgid);
		$res=sql::query($query,$array);
		$groups=array();
		while ($row=$res->fetch()) {
			$row['childs']=array();
			$groups[$row['id']]=$row;
		}
		foreach ($groups as $key=>$row) {
			$groups[$row['sgid']]['childs'][]=&$groups[$key];
		}
		return self::all_subgroups_sub($groups[$sgid]);
	}
	static private function all_subgroups_sub($groups,&$found=null){
		$gid=$groups['id'];
		if (is_array($found)) {
			// Avoid infinite loop
			if (in_array($gid,$found)) return array();
		}
		$result=array($gid);
		foreach ($groups['childs'] as $child) {
			$result[]=$child['id'];
			$result=array_merge($result,self::all_subgroups_sub($child,$result));
		}
		return $result;
	}
	static public function tag_list(&$data,$skin,$showhidden=false){
		// TODO: hidden flag for group
		$query='SELECT * FROM jeans_group WHERE gid=0 AND sgid=0'.
			($showhidden?'':' AND NOT(flags & <%const:sql::FLAG_HIDDEN%>)');
		$selected=isset($data['gid'])?$data['gid']:0;
		self::cb_tag_list($selected);// Initialize
		view::show_using_query($data,$query,array(),$skin,array('group','cb_tag_list'));
	}
	static public function cb_tag_list(&$row){
		static $selected=0;
		if (is_array($row)) {
			if ($row['id']==$selected) $row['selected']=true;
		} else $selected=$row;
	}
	static public function tag_tree(&$data,$skin,$restrict_group=false,$show_hidden=false,$gid=false){
		$gid=self::setting('id',$gid);
		$query='SELECT * FROM jeans_group WHERE 1'.
			($restrict_group ? ' AND id=<%gid%> OR (gid=<%gid%> AND NOT sgid=0)':'').
			($show_hidden ? '':' AND NOT(flags & <%const:sql::FLAG_HIDDEN%>)');
		$res=sql::query($query,array('gid'=>$gid));
		$rows=array();
		while ($row=$res->fetch()) {
			$row['childs']=array();
			if (isset($data['sgid'])) {
				if ($data['sgid']==$row['id']) $row['selected']=true;
			}
			$rows[$row['id']]=$row;
		}
		foreach ($rows as $id=>$row) {
			if ($row['sgid']) {
				$rows[$row['sgid']]['childs'][$row['name'].' '.md5($row['id'])]=&$rows[$id];
				ksort($rows[$row['sgid']]['childs']);
			}
		}
		$array=array();
		foreach ($rows as $row) {
			if ($row['gid']==0 && $row['sgid']==0) $array[$row['name'].' '.md5($row['id'])]=$row;
		}
		ksort($array);
		$data['treedepth']=0;
		view::show_using_array($data,$array,$skin);
	}
	static public function tag_treesub(&$data,$skin=false){
		if ($skin===false) $skin=$data['skin'];
		if (100<$data['treedepth']) return; //avoid infinite loop
		foreach ($data['childs'] as $child){
			$temp_data=$data;
			$temp_data['treedepth']++;
			$temp_data['childs']=array();
			unset($temp_data['selected']);
			foreach($child as $key=>$value) $temp_data[$key]=$value;
			view::tag_template($temp_data,'body',$skin);
		}
	}
	static public function tag_treetab(&$data,$template,$skin=false){
		if ($skin===false) $skin=$data['skin'];
		for ($i=0;$i<$data['treedepth'];$i++) view::tag_template($data,$template,$skin);
	}
}