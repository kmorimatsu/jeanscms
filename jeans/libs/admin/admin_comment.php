<?php
/*
 * Jeans CMS (GPL license)
 * $Id: admin_comment.php 365 2017-10-25 20:06:22Z kmorimatsu $
 */

class admin_comment extends jeans {
	static private $itemdata=false;
	static public function init(){
		// if self::$itemdata remains as false, edit/add comment isn't accepted.
		if (!empty($_GET['cid'])) {
			$row=sql::query('SELECT itemid, author from jeans_comment WHERE id=<%0%>',$_GET['cid'])->fetch();
			if (!$row) return jerror::fatal('_ADMIN_COMMENT_NO_SUCH_COMMENT');
			if ($row['author']!=member::setting('id') && !member::is_admin()) return jerror::fatal('_ADMIN_NO_PERMISSION');
			$itemid=$row['itemid'];
		} elseif (!empty($_POST['itemid'])) $itemid=(int)$_POST['itemid'];
		elseif (!empty($_GET['itemid'])) $itemid=(int)$_GET['itemid'];
		else return jerror::fatal('_ADMIN_COMMENT_NO_SUCH_ITEM');
		self::$itemdata=sql::query('SELECT gid,id FROM jeans_item WHERE id=<%0%>',$itemid)->fetch();
		if (!self::$itemdata) return jerror::fatal('_ADMIN_COMMENT_NO_SUCH_ITEM');
	}
	static public function enabled(){
		if (!self::$itemdata) return false;
		return comments::if_enabled(self::$itemdata);
	}
	static public function action_post_add(){
		if (!self::enabled()) return jerror::note('_ADMIN_COMMENT_NOT_ACCEPTED');
		// prepare XML
		$xml=new SimpleXMLElement(_XML_BLANC);
		if (member::logged_in()) {
			$xml->user=  member::setting('name');
			$xml->web=   member::setting('web');
			$xml->email= member::setting('email');
		} else {
			if (strlen($_POST['user_text'])==0) return jerror::note('_ADMIN_COMMENT_VALID_USER_NAME_RQUIRED');
			if (constant('_CONF_PROTECT_MEMBER_NAMES')) {
				$query='SELECT COUNT(*) as result FROM jeans_member WHERE name LIKE <%0%>';
				$row=sql::query($query,$_POST['user_text'])->fetch();
				if ($row['result']) return jerror::note('_ADMIN_COMMENT_VALID_USER_NAME_RQUIRED');
			}
			$xml->user=  $_POST['user_text'];
			$xml->web=   $_POST['web_url'];
			$xml->email= $_POST['email'];
		}
		$xml->ip=$_SERVER['REMOTE_ADDR'];
		$xml->time=_NOW;
		// prepare row
		$row=array('author'=>(int)member::setting('id'), 'xml'=>$xml->asXML());
		if (empty($_POST['body_text'])) return jerror::note('_ADMIN_COMMENT_NO_EMPTY_COMMENT');
		else $row['body']=$_POST['body_text'];
		$row['itemid']=self::$itemdata['id'];
		// update SQL table
		$query='INSERT INTO jeans_comment (<%key:row%>) VALUES (<%row%>)';
		sql::query($query,array('row'=>$row));
		unset($_POST['body_text']);
		// "remember me" feature using cookie.
		if (!empty($_POST['remember'])) {
			foreach(array('user_text','web_url','email') as $key ) core::set_cookie("commentform_$key",$_POST[$key]);
		}
	}
	static public function action_post_edit(){
		if (!self::$itemdata) return;
		// use $_GET['commentid'] and $_POST['body_text']
		if (member::is_admin()) $query='UPDATE jeans_comment SET body=<%body%>, flags=<%flags%> WHERE id=<%id%>';
		elseif (member::logged_in()) $query='UPDATE jeans_comment SET body=<%body%>, flags=<%flags%> WHERE id=<%id%> AND author=<%author%>';
		else return jerror::fatal('_ADMIN_NO_PERMISSION');
		// prepare data array
		$post=admin::item_from_post('jeans_comment');
		$array=array('body'=>$post['body'],'id'=>$_GET['cid'],'author'=>member::setting('id'),'flags'=>$post['flags']);
		sql::query($query,$array);
		if (sql::pdo()->errorCode()=='00000') {
			$url=empty($_POST['redirect_url'])?_CONF_SELF:$_POST['redirect_url'];
			core::set_cookie('note_text','_ADMIN_COMMENT_SAVED',0);
			core::redirect_local($url);
		} else {
			// error
		}
	}
}