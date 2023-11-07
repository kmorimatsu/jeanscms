<?php
/*
 * Jeans CMS (GPL license)
 * $Id: admin_comments.php 365 2017-10-25 20:06:22Z kmorimatsu $
 */

class admin_comments extends jeans {
	static private $mode,$id;
	static public function init(){
		// Load the language file
		$warning=self::translate('_ADMIN_NO_PERMISSION');
		if (!empty($_GET['cid'])) {
			// Owner and superadmin can go ahead
			if (!member::is_admin()) {
				$query='SELECT author FROM jeans_comment WHERE id=<%0%>';
				$row=sql::query($query,$_GET['cid'])->fetch();
				if (!$row) jerror::quit($warning);
				if ($row['author']!=member::setting('id')) jerror::quit($warning);
			}
			self::$mode='comment';
			self::$id=$_GET['cid'];
		} elseif (!empty($_GET['itemid'])) {
			// Owner and superadmin can go ahead
			if (!member::is_admin()) {
				$query='SELECT author FROM jeans_item WHERE id=<%0%>';
				$row=sql::query($query,$_GET['itemid'])->fetch();
				if (!$row) jerror::quit($warning);
				if ($row['author']!=member::setting('id')) jerror::quit($warning);
			}
			self::$mode='item';
			self::$id=$_GET['itemid'];
		} elseif (!empty($_GET['mid'])) {
			// Owner and superadmin can go ahead
			if (!member::is_admin()) {
				if (member::setting('id')!=$_GET['mid']) jerror::quit($warning);
			}
			self::$mode='member';
			self::$id=$_GET['mid'];
		} elseif (!empty($_GET['gid'])) {
			// Only superadmin can go ahead
			if (!member::is_admin()) jerror::quit($warning);
			self::$mode='group';
			self::$id=$_GET['gid'];
		}
	}
	static public function tag_list(&$data,$skin,$limit=20){
		switch(self::$mode){
			case 'comment':
				$query='SELECT id,author,itemid,body,xml,flags,"comment" as xtable 
					FROM jeans_comment 
					WHERE id=<%id%>';
				break;
			case 'item':
				$query='SELECT id,author,itemid,body,xml,flags,"comment" as xtable 
					FROM jeans_comment 
					WHERE itemid=<%id%> 
					ORDER by id DESC LIMIT <%limit%> OFFSET <%offset%>';
				break;
			case 'member':
				$query='SELECT id,author,itemid,body,xml,flags,"comment" as xtable 
					FROM jeans_comment 
					WHERE author=<%id%>
					ORDER by id DESC LIMIT <%limit%> OFFSET <%offset%>';
				break;
			case 'group':
				$query='SELECT c.id as id, c.author as author, c.itemid as itemid ,c.body as body, c.xml as xml, c.flags as flags, "comment" as xtable 
					FROM jeans_comment as c, jeans_item as i  
					WHERE c.itemid=i.id AND i.gid=<%id%> 
					ORDER by c.id DESC LIMIT <%limit%> OFFSET <%offset%>';
				break;
			default:
				return;
		}
		$offset=isset($_GET['offset'])?(int)$_GET['offset']:0;
		$array=array('id'=>self::$id,'limit'=>$limit,'offset'=>$offset);
		$items=sql::count_query($query,$array);
		$data['libs']['page']=array('items'=>$items,'offset'=>$offset,'limit'=>$limit);
		$cb=array('admin_comments','cb_tag_list');
		view::show_using_query($data,$query,$array,$skin,$cb);
	}
	static public function cb_tag_list(&$row){
		if (!empty($row['author'])) {
			$row['user']=memberinfo::setting('name',$row['author']);
		}
	}
}