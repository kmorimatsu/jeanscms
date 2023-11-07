<?php
/*
 * Jeans CMS (GPL license)
 * $Id: admin_temp.php 365 2017-10-25 20:06:22Z kmorimatsu $
 */

class admin_temp extends jeans{
	/* Usage:
	 * $temp=admin_temp::create();
	 * Use "$temp->filename()" as the temprary file name.
	 * The temporary file will be automatically deleted when the object is destructed,
	 * so it's not required to delete by yourself.
	 * Do not destruct the object during the temporary file is beeing used.
	 */
	/**
	 * @return admin_temp_class
	 */
	static public function create(){
		return new admin_temp_class;
	}
}
class admin_temp_class{
	private $filename;
	public function __construct(){
		$this->filename=tempnam(self::tempdir(),'.htemp');
	}
	public function __destruct(){
		unlink($this->filename);
	}
	public function filename(){
		return $this->filename;
	}
	static private function tempdir(){
		static $dir;
		if (isset($dir)) return $dir;
		if (function_exists('sys_get_temp_dir')) {
			$dir=sys_get_temp_dir();
			if (is_writable($dir)) return $dir;
		}
		if (defined('_DIR_SKINS') && file_exists(_DIR_SKINS.'media')) {
			$dir=_DIR_SKINS.'media';
			if (is_writable($dir)) return $dir;
		}
		if (defined('_DIR_SKINS') && file_exists(_DIR_SKINS)) {
			$dir=_DIR_SKINS;
			if (is_writable($dir)) return $dir;
		}
		if (defined('_CONF_DB_MAIN')) {
			$dir=dirname(_CONF_DB_MAIN);
			if (is_writable($dir)) return $dir;
		}
		jerror::quit('Cannot create temporary file');
	}
}