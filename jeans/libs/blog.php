<?php
/*
 * Jeans CMS (GPL license)
 * $Id: blog.php 365 2017-10-25 20:06:22Z kmorimatsu $
 */

class blog extends group {
	static private $error=false;
	static public function init(){
		sql::pdo()->sqliteCreateFunction('libs_blog_search',array('blog','sql_libs_blog_search'));
		sql::pdo()->sqliteCreateFunction('libs_blog_localyear',array('blog','sql_libs_blog_localyear'));
		sql::pdo()->sqliteCreateFunction('libs_blog_localmonth',array('blog','sql_libs_blog_localmonth'));
		sql::pdo()->sqliteCreateFunction('libs_blog_localday',array('blog','sql_libs_blog_localday'));
	}
	static public function set_id($id){
		$id=parent::setting('id',$id);
		if ($id && (parent::setting('flags',$id) & sql::FLAG_INVALID)) $id=0;
		parent::set_id($id);
	}
	static public function set_sgid($id){
		$id=parent::sgsetting('id',$id);
		if ($id && (parent::sgsetting('flags',$id) & sql::FLAG_INVALID)) $id=0;
		parent::set_sgid($id);
	}
	/**
	 * blog::selector() is called in index.php
	 * Here template is determined from user's input
	 * and call view::parse_skin(). 
	 */
	static public function selector($blogid=false){
		if (!$blogid) $blogid=parent::setting('id');
		$template='index';
		if (class_exists('error',false)) {
			// Note that the class 'error' exists when $_COOKIE['note_text'] is set.
			if (jerror::fatal() || isset($_GET['error'])) $template='error';
		}
		$category=isset($_GET['sgid'])?$_GET['sgid']:0;
		if ($template=='error') {
			self::set_id($blogid);
		} elseif (isset($_GET['itemid'])) {
			$template='item';
			$category=parent::sgsetting('id');
			if (item::setting('flags') & sql::FLAG_INVALID) {
				$template='error';
				jerror::fatal('_JEANS_ERROR_ITEM_NOT_EXIST');
			}
		} elseif (isset($_GET['archive'])) {
			$template='archive';
		} elseif (isset($_GET['archivelist'])) {
			$template='archivelist';
			self::set_id($_GET['archivelist']);
		} elseif (isset($_GET['query_text'])) {
			$template='search';
		} elseif (isset($_GET['memberid'])) {
			$template='member';
		} elseif (isset($_GET['imagepopup'])) {
			$template='imagepopup';
			self::set_id($blogid);
		} else {
			self::set_id($blogid);
		}
		if (!parent::setting('id')) {
			$template='error';
			parent::set_id(_CONF_DEFAULT_GROUP);
			jerror::fatal('_JEANS_ERROR_BLOG_NOT_EXIST');
		}
		if ($category && $template!='error') {
			self::set_sgid($category);
			if (!parent::sgsetting('id')) {
				$template='error';
				jerror::fatal('_JEANS_ERROR_CATEGORY_NOT_EXIST');
			}
		}
		$skin=parent::setting('group_skin');
		$parent_skin=false;
		$data=false;
		self::skintype($template);
		view::parse_skin($skin,$parent_skin,$data,$template);
	}
	static public function skintype($set=false){
		static $type;
		if (is_string($set) && !isset($type)) $type=$set;
		return $type;
	}
	static public function if_skintype(&$data,$type){
		return $type==self::skintype();
	}
	/**
	 * blog::tag_blog() is the method to implement
	 * most important skin var, <%blog%>
	 * <%blog.narrowby%> is used to narrow the result of SQL query
	 * before using <%blog%>.
	 * In blog::tag_blog(), code calls view::view_using_query(),
	 * one of the most important function of Jeans CMS.
	 */
	static public function query_select($where){
		return 'SELECT 
			i.id as id, 
			i.time as time, 
			i.author as author, 
			i.title as title, 
			i.body as body, 
			i.more as more, 
			i.gid as gid, 
			i.sgid as sgid, 
			i.gid as blogid, 
			i.sgid as catid,
			i.keywords as keywords, 
			i.xml as xml, 
			"item" as xtable, 
			m.name as aname,
			c.name as cname, 
			c.desc as cdesc,
			count(o.id) as comments 
			FROM jeans_item as i, jeans_group as c, jeans_member as m  
			LEFT JOIN jeans_comment as o ON o.itemid=i.id 
			WHERE i.sgid=c.id
			AND i.author=m.id 
			AND '.$where;
	}
	static private $narrowby=array('category'=>false,'archive'=>false,'search'=>false,'showhidden'=>false,'0offset'=>false);
	static public function tag_narrowby() {
		$args=func_get_args();
		array_shift($args); //&$data
		foreach($args as $key) self::$narrowby[$key]=true;
	}
	static private function narrowby_list(){
		$narrowby=self::$narrowby;
		foreach(self::$narrowby as $key=>$value) self::$narrowby[$key]=false;
		return $narrowby;
	}
	static public function tag_blog(&$data,$skin,$limit=10,$show_child_group_items=true,$blogid=false){
		// Narrowing methods follow
		$narrowby=self::narrowby_list();
		if (!$blogid) $blogid=parent::setting('id',$blogid);
		if ($narrowby['0offset']) {
			$offset=0;
		} else {
			$offset=isset($_GET['offset'])?(int)$_GET['offset']:0;
		}
		if ($narrowby['category']) {
			$subgroup=self::category('id');
			if ($subgroup && $show_child_group_items) $subgroups=parent::all_subgroups($blogid,$subgroup);
			else $subgroups=array($subgroup);
		} else $subgroup=$subgroups=false;
		if ($narrowby['archive'] && isset($_GET['archive'])) {
			if (self::if_monthlyarchive()) {
				$timestamp=strtotime($_GET['archive'].'-01 00:00:00');
				$archivestart=gmdate('Y-m-d H:i:s', $timestamp);
				$timestamp=strtotime(date('Y-m-01 00:00:00',$timestamp+2678400));
				$archiveend=gmdate('Y-m-d H:i:s', $timestamp);
			} elseif (self::if_dailyarchive()) {
				$timestamp=strtotime($_GET['archive'].' 00:00:00');
				$archivestart=gmdate('Y-m-d H:i:s', $timestamp);
				$archiveend=gmdate('Y-m-d H:i:s', $timestamp+86400);
			} else $archivestart=$archiveend=false;
		} else $archivestart=$archiveend=false;
		if ($narrowby['search'] && isset($_GET['query_text'])) {
			$search=true;
			self::sql_search_compile_query($_GET['query_text']);
		} else $search=false;
		$hide=!$narrowby['showhidden'];
		// Prepare query and array-data
		$query=self::query_select(
			'i.gid=<%blogid%> 
			AND i.time <= <%now%> '.
			($subgroup ? 'AND i.sgid IN (<%subgroups%>) ':'').
			($archivestart ? 'AND <%archivestart%> <= i.time ':'').
			($archiveend ? 'AND i.time < <%archiveend%> ':'').
			($search ? 'AND libs_blog_search(i.title,i.body,i.more,i.keywords) ':'').
			($hide ? 'AND NOT (i.flags & <%const:sql::FLAG_HIDDEN%>) AND NOT (c.flags & <%const:sql::FLAG_HIDDEN%>) ':'').
			'GROUP BY i.id 
			ORDER BY time DESC 
			LIMIT <%limit%> OFFSET <%offset%>');
		$array=array(
			'blogid'=>$blogid,
			'limit'=>$limit,
			'subgroups'=>$subgroups,
			'archivestart'=>$archivestart,
			'archiveend'=>$archiveend,
			'now'=>_NOW,
			'offset'=>$offset);
		$items=sql::count_query($query,$array);
		$data['libs']['page']=array('items'=>$items,'offset'=>$offset,'limit'=>$limit);
		view::show_using_query($data,$query,$array,$skin,array('blog','_blog_cb'),array('blog','_blog_cb2'));
	}
	static public function _blog_cb(&$row){
		$cat_array=array('sgid'=>(int)$row['sgid']);
		$item_array=array('itemid'=>(int)$row['id']);
		if (self::category('id')) $item_array=array_merge($item_array,$cat_array);
		$row['link']=view::create_link($item_array);
		$row['clink']=view::create_link($cat_array);
		$row['alink']=view::create_link(array('memberid'=>(int)$row['author']));
		core::event('pre_item',array('row'=>&$row),'view');
	}
	static public function _blog_cb2(&$row){
		core::event('post_item',array('row'=>&$row),'view');
	}
	/**
	 * Methods to search contents by keyword follow.
	 * The query (given as $_GET['query_text']) is first compiled
	 * to a regular expression.
	 * blog::sql_libs_blog_search() is an user-defined SQLite function,
	 * that is used in SQL query defined in blog::tag_blog().
	 */
	static private $query_regex=array();
	static private function sql_search_compile_query($query){
		$query=preg_split('/(?:\x20|\xe3\x80\x80)/',$query,-1,PREG_SPLIT_NO_EMPTY);
		self::$query_regex=array();
		foreach ($query as $word) {
			$words=explode('|',$word);
			foreach ($words as $key=>$word) $words[$key]=preg_quote($word);
			self::$query_regex[]='/(?:'.implode('|',$words).')/i';
		}
	}
	static public function sql_libs_blog_search($title,$body,$more,$keywords){
		foreach(self::$query_regex as $regex){
			if (preg_match($regex,(string)$keywords)) continue;
			if (preg_match($regex,(string)$title)) continue;
			if (preg_match($regex,(string)$body)) continue;
			if (preg_match($regex,(string)$more)) continue;
			return false;
		}
		return true;
	}
	
	static public function tag_link(&$data,$mode,$p1=false,$p2=false){
		switch($mode){
			case 'blog':
			case 'group':
				$blogid=parent::setting('id',$p1);
				self::p(view::create_link(array('gid'=>(int)$blogid)));
				return;
			case 'archivelist':
				$blogid=parent::setting('id',$p1);
				self::p(view::create_link(array('archivelist'=>(int)$blogid)));
				return;
			case 'category':
			case 'subgroup':
				$subgroup=$p1;
				$blogid=parent::sgsetting('gid',$subgroup);
				self::p(view::create_link(array('gid'=>(int)$blogid,'sgid'=>(int)$subgroup)));
				return;
			case 'item':
				self::p(view::create_link(array('itemid'=>(int)$p1)));
				return;
			case 'member':
				self::p(view::create_link(array('memberid'=>(int)$p1)));
				return;
		}
	}
	static public function sql_libs_blog_localyear($time) {
		return date('Y', strtotime($time.' GMT'));
	}
	static public function sql_libs_blog_localmonth($time) {
		return date('m', strtotime($time.' GMT'));
	}
	static public function sql_libs_blog_localday($time) {
		return date('d', strtotime($time.' GMT'));
	}
	static public function tag_archivelist(&$data,$skin,$blogid=false,$mode='month',$show_child_group_items=true){
		$narrowby=self::narrowby_list();
		$blogid=parent::setting('id',$blogid);
		$subgroup=self::category('id');
		if ($subgroup && $narrowby['category']) {
			if ($show_child_group_items) $subgroups=parent::all_subgroups($blogid,$subgroup);
			else $subgroups=array($subgroup);
		} else $subgroup=$subgroups=false;
		$query = 'SELECT i.gid as gid, i.time as time,
				libs_blog_localyear(i.time) AS year,
				libs_blog_localmonth(i.time) AS month,
				libs_blog_localday(i.time) AS day
				FROM jeans_item as i, jeans_group as c
				WHERE i.gid=<%blogid%>
				AND i.sgid=c.id
				AND time <= <%now%>
				AND NOT (i.flags & <%const:sql::FLAG_HIDDEN%>)
				AND NOT (c.flags & <%const:sql::FLAG_HIDDEN%>)'.
				($subgroup ? 'AND i.sgid IN (<%subgroups%>) ':'').
				' GROUP BY year'.($mode=='year'?'':', month').($mode=='day'?', day':'').
				' ORDER BY i.time DESC';
		// Init callback
		self::_archivelist_cb($subgroup);
		// Show the list
		$array=array('blogid'=>$blogid,'subgroups'=>$subgroups,'now'=>_NOW);
		view::show_using_query($data,$query,$array,$skin,array('blog','_archivelist_cb'));
	}
	static public function _archivelist_cb(&$row){
		static $sgid=false;
		if (!is_array($row)) return $sgid=$row;
		if ($sgid) {
			$row['link']=view::create_link(array('sgid'=>$sgid,'archive'=>$row['year'].'-'.$row['month']));
			$row['dlink']=view::create_link(array('sgid'=>$sgid,'archive'=>$row['year'].'-'.$row['month'].$row['day']));
		} else {
			$row['link']=view::create_link(array('gid'=>$row['gid'],'archive'=>$row['year'].'-'.$row['month']));
			$row['dlink']=view::create_link(array('gid'=>$row['gid'],'archive'=>$row['year'].'-'.$row['month'].$row['day']));
		}
	}
	static public function tag_archivedaylist(&$data,$skin,$blogid=false){
		self::tag_archivelist($data,$skin,$blogid,'day');
	}
	static public function tag_archivelink(&$data,$mode){
		$archive=$_GET['archive'];
		if (strlen($archive)==7) {// Month mode
			$timestamp=strtotime($archive.'-01 00:00:00');
			if ($mode=='newer') $timestamp+=2678400; // Newer: plus 31 days
			else $timestamp-=86400; // Older: minus 1 day
			$archive=date('Y-m',$timestamp);
			self::p(view::create_link(array('gid'=>$_GET['gid'],'archive'=>$archive)));
		} else {// Day mode
			$timestamp=strtotime($archive.' 00:00:00');
			if ($mode=='newer') $timestamp+=86400; // Newer: plus 1 day
			else $timestamp-=86400; // Older: minus 1 day
			$archive=date('Y-m-d',$timestamp);
			self::p(view::create_link(array('gid'=>$_GET['gid'],'archive'=>$archive)));
		}
	}
	static public function if_monthlyarchive(){
		if (!isset($_GET['archive'])) return false;
		return preg_match('/^[0-9]{4}\-[0-9]{2}$/',$_GET['archive']);
	}
	static public function if_dailyarchive(){
		if (!isset($_GET['archive'])) return false;
		return preg_match('/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$/',$_GET['archive']);
	}
	static public function tag_archivedate(){
		$args=func_get_args();
		array_shift($args); // &$data
		$format=implode(',',$args);
		if (self::if_monthlyarchive()) $timestamp=strtotime($_GET['archive'].'-01 00:00:00');
		elseif (self::if_dailyarchive()) $timestamp=strtotime($_GET['archive'].' 00:00:00');
		else $timestamp=0;
		self::p(strftime($format,$timestamp));
	}
	static public function tag_categorylist(&$data,$skin,$blogid=false){
		$blogid=parent::setting('id',$blogid);
		$query = 'SELECT g.*,COUNT(i.id) as items FROM jeans_group as g 
			LEFT JOIN jeans_item as i ON i.sgid=g.id AND NOT (i.flags & <%const:sql::FLAG_HIDDEN%>)
			WHERE g.gid=<%blogid%> AND NOT (g.flags & <%const:sql::FLAG_HIDDEN%>) 
			GROUP BY g.id 
			ORDER BY name ASC';
		$array=array('blogid'=>$blogid);
		view::show_using_query($data,$query,$array,$skin,array('blog','_categorylist_cb'));
	}
	static public function _categorylist_cb(&$row){
		$row['link']=view::create_link(array('sgid'=>(int)$row['id']));
	}
	static private function category($key=false){
		static $cache;
		if (!isset($_GET['sgid'])) return false;
		if (!isset($cache)) {
			$query = 'SELECT * FROM jeans_group WHERE id=<%subgroup%> LIMIT 1';
			$array=array('subgroup'=>(int)$_GET['sgid']);
			$cache=sql::query($query,$array)->fetch();
		}
		if (!isset($cache[$key])) return;
		return $cache[$key];
	}
	static public function tag_category(&$data,$key='id'){
		$value=self::category($key);
		if ($value===false) return;
		self::p(strip_tags($value));
	}
	static public function if_categoryis(&$data,$value=false,$key='id'){
		$compare=self::category($key);
		if ($value===false) return (bool)$compare;
		if ($key=='id' && !is_numeric($value)) return (bool)$compare;
		return $value==$compare;
	}
	static public function tag_bloglist(&$data,$skin){
		$query = 'SELECT * FROM jeans_group WHERE gid=0 AND sgid=0 AND NOT (flags & <%const:sql::FLAG_HIDDEN%>) ORDER BY name DESC';
		view::show_using_query($data,$query,array(),$skin,array('blog','_bloglist_cb'));
	}
	static public function _bloglist_cb(&$row){
		$row['link']=view::create_link(array('gid'=>(int)$row['id']));
	}
}