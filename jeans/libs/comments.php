<?php
/*
 * Jeans CMS (GPL license)
 * $Id: comments.php 375 2023-08-03 00:20:18Z kmorimatsu $
 */

class comments extends jeans{
	static public function init(){

	}
	static private function groupsetting(&$data,$key){
		if (isset($data['gid'])) $gid=$data['gid'];
		else $gid=item::setting('gid');
		return group::setting($key,$gid);
	}
	static private function itemsetting(&$data,$key){
		if (isset($data[$key])) return $data[$key];
		if (isset($data['id'])) $itemid=$data['id'];
		else $itemid=item::setting('id');
		return item::setting($key,$itemid);
	}
	static public function if_enabled(&$data){
		if (!self::groupsetting($data,'comments_enabled')) return false;
		if (!member::logged_in() && !self::groupsetting($data,'comments_non_member')) return false;
		if (isset($data['comments_disabled'])) return !$data['comments_disabled'];
		return !self::itemsetting($data,'comments_disabled');
	}
	static public function tag_comments(&$data,$skin){
		$query='SELECT *,"comment" as xtable FROM jeans_comment 
			WHERE itemid=<%itemid%> 
			AND NOT (flags & <%const:sql::FLAG_HIDDEN%>) 
			ORDER BY id ASC';
		$array=array('itemid'=>self::itemsetting($data,'id'));
		view::show_using_query($data,$query,$array,$skin,array('comments','cb_tag_comments'));
	}
	static public function cb_tag_comments(&$row){
		if (empty($row['author'])) {
			$row['email']=strtr($row['email'],array('.'=>'(dot)','@'=>'(at)'));
			if (!empty($row['web'])) $row['link']=$row['web'];
			elseif(!empty($row['email'])) $row['link']='mailto:'.$row['email'];
			else $row['link']='';
		} else {
			$row['link']=view::create_link(array('memberid'=>(int)$row['author']));
			$row['user']=memberinfo::setting('name',$row['author']);
			$row['web']=memberinfo::setting('web',$row['author']);
		}
	}
	static public function tag_body(&$data){
		$body=self::hsc($data['body']);
		$body=trim($body);
		$body=preg_replace_callback('/(^|[ \r\n])([ ]+)/',array('self','cb1_tag_body'),$body);
		$body=preg_replace_callback('/([^\r\n]*)(\r\n|\r|\n|$)/',array('self','cb2_tag_body'),$body);
		self::echo_html($body);
	}
	static private function cb1_tag_body(&$m){
		// '   ' => ' &nbsp;&nbsp;' conversion
		return $m[1].str_repeat('&nbsp;',strlen($m[2]));
	}
	static private function cb2_tag_body($m){
		// web link & nl2br
		if (self::is_valid_url($m[1])) $m[1]=self::fill_html('<a href="<%0%>" rel="nofollow"><%0%></a>',str_replace('&amp;','&',$m[1]),'hsc');
		if (strlen($m[2])) return $m[1].'<br />'.$m[2];
		else return $m[1].$m[2];
	}
}