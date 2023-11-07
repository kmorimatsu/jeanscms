<?php
/*
 * Jeans CMS (GPL license)
 * $Id: admin_sql.php 365 2017-10-25 20:06:22Z kmorimatsu $
 */

class admin_sql extends jeans {
	static public function init(){
		// Load the language file
		$warning=self::translate('_ADMIN_NO_PERMISSION');
		// Only superadmin can use this class
		if (!member::is_admin()) jerror::quit($warning);
		// Register function to be used in query
		sql::pdo()->sqliteCreateFunction('get_table_index',array('admin_sql','get_table_index'),1);
	}
	static public function tag_dbfilelist(&$data,$skin){
		$rows=array();
		foreach (sql::pdo_objects() as $mode=>$pdo) {
			sql::select_pdo($mode);
			$row=sql::query('PRAGMA database_list')->fetch();
			$file=$row['file'];
			$size=filesize($file);
			$file=preg_replace('#^.*[/\\\\]#','',$file);
			$rows[]=array('mode'=>$mode,'file'=>$file,'size'=>$size);
		}
		view::show_using_array($data,$rows,$skin);
	}
	static public function action_post_backup(){
		foreach (sql::pdo_objects() as $mode=>$pdo) {
			sql::select_pdo($mode);
			$row=sql::query('PRAGMA database_list')->fetch();
			if (substr($row['file'],-strlen($_POST['file']))!=$_POST['file']) continue;
			$file=$row['file'];
		}
		if (!isset($file)) return;
		header("Content-Type: application/unknown; name=\"$_POST[file]\"");
		header("Content-Disposition: attachment; filename=$_POST[file]");
		readfile($file);
		exit;
	}
	static public function action_post_vacuum(){
		foreach (sql::pdo_objects() as $mode=>$pdo) {
			sql::select_pdo($mode);
			sql::query('VACUUM;');
		}
		jerror::note('_ADMIN_VACUUM_DONE');
	}
	static private $query='';
	static public function action_post_query(){
		self::$query=$_POST['query_text'];
	}
	static public function get_table_index($tbl_name){
		static $cache;
		if (!isset($cache)) {
			$cache=array();
			$query='SELECT * FROM sqlite_master WHERE type="index"';
			$res=sql::query($query);
			while ($row=$res->fetch()) {
				if (!isset($cache[$row['tbl_name']])) $cache[$row['tbl_name']]='';
				$cache[$row['tbl_name']].=$row['sql']."; \n";
			}
		}
		return isset($cache[$tbl_name])?$cache[$tbl_name]:null;
	}
	static public function tag_queryresult(&$data,$skin){
		$query=self::$query;
		if (1<preg_match_all('/(([^;\'"]*|\'[^\']*\'|"[^"]*")*);/',$query,$m,PREG_SET_ORDER)) {
			sql::begin();
			foreach($m as $q) sql::pdo()->exec($q[0]);
			sql::commit();
			$query='SELECT 0 WHERE 0';
		}
		if (preg_match('/^\s*SELECT\s/i',$query,$m)) self::query_page_check($data,$query);
		$rows=array();
		$keys=array();
		$res=@sql::query($query);
		if (!$res) {
			$e=sql::pdo()->errorInfo();
			$data['query']=self::$query;
			$data['sqlstate']=$e[0];
			$data['errorcode']=$e[1];
			$data['errormessage']=$e[2];
			view::tag_template($data,'error',$skin);
			return;
		}
		$additional_data=array();
		while ($row=$res->fetch()) {
			foreach ($row as $key=>$value) {
				if (substr($key,0,15)=='libs_admin_sql_') {
					$additional_data[substr($key,15)]=$value;
					unset($row[$key]);
					continue;
				}
				$keys[$key]=true;
			}
			$rows[]=$row;
		}
		if (count($rows)==0) view::tag_template($data,'none',$skin);
		else {
			$data['libs']['admin_sql']=$additional_data;
			view::tag_template($data,'head',$skin);
			view::tag_template($data,'tr',$skin);
			foreach ($keys as $key=>$value) {
				$data['key']=$key;
				view::tag_template($data,'th',$skin);
			}
			view::tag_template($data,'/tr',$skin);
			foreach ($rows as $row) {
				view::tag_template($data,'tr',$skin);
				foreach ($keys as $key=>$value) {
					$data['key']=$key;
					$data['value']=isset($row[$key])?$row[$key]:'';
					view::tag_template($data,'td',$skin);
				}
				view::tag_template($data,'_tr',$skin);
			}
			
			view::tag_template($data,'foot',$skin);
		}
	}
	static private function query_page_check(&$data,$query) {
		// Page setting
		static $search_array=array(
			'/[\s]LIMIT[\s]+([0-9]+)[\s]+(OFFSET)[\s]+([0-9]+)[\s]*[;]?[\s]*$/i',
			'/[\s]LIMIT[\s]+([0-9]+)[\s]+(,)[\s]+([0-9]+)[\s]*[;]?[\s]*$/i',
			'/[\s]LIMIT[\s]+([0-9]+)[\s]*[;]?[\s]*$/i');
		$count=@sql::count_query($query);
		$offset=0;
		$limit=$count;
		foreach ($search_array as $search) {
			if (!preg_match($search,$query,$m)) continue;
			if (count($m)==2) {
				// LIMIT xx
				$limit=(int)$m[1];
			} elseif ($m[2]==',') {
				// LIMIT xx,xx
				$limit=(int)$m[3];
				$offset=(int)$m[1];
			} else {
				// LIMIT xx OFFSET xx
				$limit=(int)$m[1];
				$offset=(int)$m[3];
			}
			break;
		}
		$data['libs']['page']=array('items'=>$count,'offset'=>$offset,'limit'=>$limit);
	}
	static $editform=array('',0);
	static public function action_post_editform(){
		self::$editform=array($_POST['tablename'],$_POST['itemid']);
	}
	static public function tag_editform(&$data,$skin) {
		list($tablename,$id)=self::$editform;
		$data['libs']['admin_sql']=array('tablename'=>$tablename);
		if ($id) {
			// Edit item mode
			$query='SELECT * FROM <%tablename%> WHERE id=<%id%> LIMIT 1';
			$query=sql::fill_query($query,array('tablename'=>$tablename));
			$array=array('id'=>$id);
			$item=sql::query($query,$array)->fetch();
		} else {
			// New item mode
			$query='PRAGMA table_info(<%tablename%>)';
			$query=sql::fill_query($query,array('tablename'=>$tablename));
			$item=array();
			$res=sql::query($query);
			while ($row=$res->fetch()) {
				switch($row['name']) {
					case 'time':
						$item[$row['name']]=date('Y-m-d H:i:s', time());
						break;
					default:
						$item[$row['name']]='';
				}
			}
		}
		$rows=array();
		foreach ($item as $key=>$value) {
			switch ($key) {
				case 'id':
					$type='text';
					$extra='hidden';
					break;
				case 'gid': case 'sgid': case 'contextid':
				case 'author': case 'sequence':
					$type='text';
					$extra='numeric';
					break;
				case 'body': case 'more': case 'xml':
					$type='textarea';
					$extra='';
					break;
				default:
					$type='text';
					$extra='';
					break;
			}
			$rows[]=array('key'=>$key, 'name'=>$key.'_text', 'value'=>$value, 
				'type'=>$type, 'extra'=>$extra);
		}
		view::show_using_array($data,$rows,$skin);
	}
	static public function action_post_edit(){
		self::$query=$_POST['query_text'];
		$id=$_POST['id_text'];
		$table=$_POST['table'];
		$delete=!empty($_POST['delete']);
		if (!$table) return;
		$row=array();
		foreach ($_POST as $key=>$value) {
			if ($key=='query_text' || substr($key,-5)!='_text') continue;
			if ($key=='id_text' && !$id) continue;
			elseif ($key=='bindata_text') {
				$query='SELECT bindata FROM <%table%> WHERE id=<%id%>';
				$query=sql::fill_query($query,array('table'=>$table));
				$value=sql::query($query,array('id'=>$id))->fetch();
				if ($value) $value=$value['bindata'];
			}
			$row[substr($key,0,-5)]=$value;
		}
		if ($delete && $id) $query='DELETE FROM <%table%> WHERE id=<%id%>';
		else $query='INSERT OR REPLACE INTO <%table%> (<%key:row%>) VALUES (<%row%>)';
		$query=sql::fill_query($query,array('table'=>$table));
		sql::query($query,array('id'=>$id,'row'=>$row));
		$array=array('id'=>$id,'table'=>$table);
		if (!$id) jerror::note('New item on <%table%> table was created.', $array);
		elseif ($delete) jerror::note('The item (id: <%id%>) on <%table%> table was deleted.', $array);
		else jerror::note('The item (id: <%id%>) on <%table%> table was saved.', $array);
	}
}