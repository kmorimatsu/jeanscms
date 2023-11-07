<?php
/*
 * Jeans CMS (GPL license)
 * $Id: ticket.php 365 2017-10-25 20:06:22Z kmorimatsu $
 */

class ticket extends jeans{
	static public function init(){
		// DB-login may be not initialized.
		sql::init('member',_CONF_DB_LOGIN);
		// Delete more than 60 min old tickets
		$query='DELETE FROM jeans_ticket WHERE time < <%time%>';
		$array=array('time'=>gmdate('Y-m-d H:i:s', time()-3600));
		sql::select_pdo('member');
		sql::query($query,$array);
		return new ticket;
	}
	static public function check(){
		self::check_referer();
		if (!isset($_POST['ticket'])) return false;
		$action=isset($_POST['action'])?
			$_POST['action'] : (isset($_GET['action'])?$_GET['action']:'');
		$query='SELECT COUNT(*) as result FROM jeans_ticket'.
			' WHERE memberid=<%memberid%>'.
			' AND ticket=<%ticket%>'.
			($action?' AND action=<%action%>':'');
		$array=array(
			'memberid'=>member::setting('id'),
			'ticket'=>$_POST['ticket'],
			'action'=>$action);
		sql::select_pdo('member');
		$row=sql::query($query,$array)->fetch();
		return (bool)$row['result'];
	}
	static private function check_referer(){
		if (!isset($_SERVER['HTTP_REFERER'])) return;
		$referer=$_SERVER['HTTP_REFERER'];
		$hosts=array($_SERVER['HTTP_HOST']);
		if (defined('_CONF_ACCEPTED_HOSTS_AS_REFERER')) {
			$hosts=array_merge($hosts,
				preg_split('/[\s]+/',_CONF_ACCEPTED_HOSTS_AS_REFERER,-1,PREG_SPLIT_NO_EMPTY));
		}
		foreach ($hosts as $host) {
			if ($host && preg_match("#^(http|https)://$host/#",$referer)) return;
		}
		jerror::quit(self::translate('_ADMIN_INVALID_REFERER_FOR_POST'));
	}
	static private $tickets=array();
	static public function buy_ticket($action=''){
		if (!isset(self::$tickets[$action])) {
			self::$tickets[$action]=self::random_key();
		}
		return self::$tickets[$action];
	}
	static public function tag_hidden(&$data,$action=''){
		$ticket=self::buy_ticket($action);
		self::echo_html('<input type="hidden" name="ticket" value="<%0%>" />',$ticket);
		$data['libs']['ticket']=array('ticket'=>$ticket,'aciton'=>$action);
	}
	static public function tag_ticket(&$data,$action=''){
		self::p(self::buy_ticket($action));
		$data['libs']['ticket']=array('ticket'=>$ticket,'aciton'=>$action);
	}
	static public function shutdown(){
		sql::select_pdo('member');
		sql::query('BEGIN');
		foreach (self::$tickets as $action=>$ticket) {
			$query='INSERT INTO jeans_ticket (<%key:values%>) VALUES (<%values%>)';
			$array=array('values'=>array(
					'memberid'=>member::setting('id'),
					'action'=>$action,
					'time'=>_NOW,
					'ticket'=>$ticket
				));
			sql::select_pdo('member');
			sql::query($query,$array);
		}
		sql::select_pdo('member');
		sql::query('COMMIT');
	}
}