<?php
/*
 * Jeans CMS (GPL license)
 * $Id: debug.php 216 2010-06-27 18:42:54Z kmorimatsu $
 */

class debug{
	static private $stime,$head,$pre,$post,$foot;
	static public function init($head=false,$pre=false,$post=false,$foot=false){
		static $obj;
		if ($head===false) return;
		$obj=new self;
		self::$stime=self::getmicrotime();
		self::$head=$head;
		self::$pre=$pre;
		self::$post=$post;
		self::$foot=$foot;
	}
	static public function shutdown(){}
	static private function getmicrotime(){
		list($usec, $sec) = explode(' ',microtime()); 
		return ((float)$sec + (float)$usec); 
	}
	static private $text=array();
	static public function add($text){
		$time=self::getmicrotime()-self::$stime;
		$time=substr($time,0,8);
		$data='';
		$db=debug_backtrace();
		$prevfile='';
		for($i=0;$i<5;$i++){
			if (!isset($db[$i]['file']) || !isset($db[$i]['line'])) continue;
			$dbfile=preg_replace('/^.*[\/\\\\]([a-z0-9_]+\.php)$/i','$1',$db[$i]['file']);
			$dbline=$db[$i]['line'];
			$data.=$prevfile==$dbfile?",$dbline":" $dbfile:$dbline";
			$prevfile=$dbfile;
		}
		self::$text[]="$text ($data) ($time)";
	}
	public function __destruct(){
		core::shutdown();
		echo self::$head;
		foreach(self::$text as $value){
			core::echo_html(self::$pre.'<%0%>'.self::$post,$value);
		}
		echo self::$foot;
	}
	static public function show_sql_result($obj,$head="<br />\n<table border>\n",$pre='<tr>',$data='<td><%data%></td>',$post="</tr>\n",$foot="</table><br />\n"){
		$columns=array();
		$rows=array();
		while($row=$obj->fetch()){
			foreach ($row as $key=>$value) $columns[$key]=true;
			$rows[]=$row;
		}
		echo $head;
		foreach ($columns as $key=>$value) echo str_replace('<%data%>',$key,$data);
		foreach ($rows as $row) {
			echo $pre;
			foreach ($columns as $key=>$value) echo @str_replace('<%data%>',$row[$key],$data);
			echo $post;
		}
		echo $foot;
	}
	static public function query($query,$data=null,$mode=PDO::FETCH_ASSOC){
		core::echo_html("<br />\n<%0%><br />",$query);
		print_r($data);
		self::show_sql_result(sql::query($query,$data,$mode));
	}
	static public function comment($data){
		core::echo_html('<!--');
		print_r($data);
		core::echo_html('-->');
	}
}

debug::init("<hr>","<br />\r\n","<br /><!-- -->\r\n","</hr>");