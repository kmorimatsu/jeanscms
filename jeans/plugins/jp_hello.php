<?php
/*
 * Jeans CMS (GPL license)
 * $Id: jp_hello.php 344 2014-10-17 01:13:28Z kmorimatsu $
 */

class jp_hello extends plugin{
	static public function name(){
		return 'Hello World Plugin';
	}
	static public function author(){
		return 'Your name here';
	}
	static public function url(){
		return 'http://jeanscms.sourceforge.jp/';
	}
	static public function desc(){
		return 'This is Hello World Plugin.';
	}
	static public function version(){
		return '1.0';
	}
	static public function tag_hello(){
		self::p('Hello!');
	}
	static public function tag_world(){
		self::p('Hello World!');
	}
}