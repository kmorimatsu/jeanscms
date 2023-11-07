<?php
/*
 * Jeans CMS (GPL license)
 * $Id: date.php 216 2010-06-27 18:42:54Z kmorimatsu $
 */


class date extends jeans {
	static private $locale,$xcase,$time;
	static public function init(){
		self::$locale=array(
			'c'=>self::translate('_JEANS_STRFTIME_CL'),
			'x'=>_JEANS_STRFTIME_XL,
			'a'=>explode('|',_JEANS_STRFTIME_AL),
			'A'=>explode('|',_JEANS_STRFTIME_AH),
			'b'=>explode('|',_JEANS_STRFTIME_BL),
			'B'=>explode('|',_JEANS_STRFTIME_BH),
			'p'=>explode('|',_JEANS_STRFTIME_PL),
			'P'=>explode('|',_JEANS_STRFTIME_PH));
		self::$xcase=array();
		$al='abcdefghijklmnopqrstuvwxyz';
		$ah=strtoupper($al);
		for($i=0;$i<26;$i++){
			self::$xcase[$al{$i}]=$ah{$i};
			self::$xcase[$ah{$i}]=$al{$i};
		}
	}
	static public function strftime($format,$time=false){
		self::$time=$time;
		return preg_replace_callback('/%([_0#\^\-]?)([a-zA-Z%])/',array('self','cb_strftime'),$format);
	}
	static private function cb_strftime($m){
		$result=self::cb_strftime_sub($m[2]);
		switch($m[1]){
			case '_': return preg_replace(array('/(^|[^0-9])0([0-9])/','/(^|[^0-9])00([0-9])/'),array('$1 $2','$1  $2'),$result);
			case '-': return preg_replace(array('/(^|[^0-9])0([0-9])/','/(^|[^0-9])00([0-9])/'),array('$1$2','$1$2'),$result);
			case '0': return preg_replace(array('/(^|[^0-9]) ([0-9])/','/(^|[^0-9])  ([0-9])/'),array('$1\\0$2','$1\\00$\2'),$result);
			case '#': return strtoupper($result);
			case '^': return strtr($result,$xcase);
			default:  return $result;
		}
	}
	static private function cb_strftime_sub(&$format){
		switch($format){
			case 'a':                                     // Sun-Sat
				return self::$locale['a'][date('w',self::$time)];
			case 'A':                                     // Sunday-Saturday
				return self::$locale['A'][date('w',self::$time)];
			case 'd': return date('d',self::$time); // 01-31
			case 'e': return date('j',self::$time); // 1-31
			case 'j': return date('z',self::$time); // 001-366
			case 'u': return date('N',self::$time); //1-7
			case 'w': return date('w',self::$time); // 0-6
			case 'U':             // 1-53
				if (date('w',mktime(0, 0, 0, 1, 1, date('Y',self::$time)))==0) {
					if (date('w',self::$time)==0) return 'W';
					else return date('W',self::$time)-1;
				} else {
					if (date('w',self::$time)==0) return date('W',self::$time)+1;
					else return 'W';
				}
			case 'V': return date('W',self::$time); // 01-53
			case 'W': return date('W',self::$time); // 1-53
			case 'b':                                     // Jan-Dec
				return self::$locale['b'][date('n',self::$time)-1];
			case 'B':                                     // January-December
				return self::$locale['B'][date('n',self::$time)-1];
			case 'h': return date('M',self::$time); // Jan-Dec
			case 'm': return date('m',self::$time); // 01-12
			case 'C': return intval((date('Y',self::$time)-1)/100); // 19 or 20
			case 'g': return date('y',self::$time); // 00-99
			case 'G': return date('Y',self::$time); // 1970-2023
			case 'y': return date('y',self::$time); // 00-99
			case 'Y': return date('Y',self::$time); // 1970-2023
			case 'H': return date('H',self::$time); // 00-23
			case 'I': return date('h',self::$time); // 01-12
			case 'l': return preg_replace('/^0/',' ',date('h',self::$time)); // _1-12
			case 'M': return date('i',self::$time); // 00-59
			case 'p':                                     // AM or PM
				return date('A',self::$time)=='AM' ? self::$locale['p'][0]:self::$locale['p'][1];
			case 'P':                                     // am or pm
				return date('A',self::$time)=='AM' ? self::$locale['P'][0]:self::$locale['P'][1];
			case 'r': return date('h:i:s A',self::$time); // '%I:%M:%S %p'
			case 'R': return date('H:i',self::$time); // '%H:%M'
			case 'S': return date('s',self::$time); // 00-59
			case 'T': return date('H:i:s',self::$time); // '%H:%M:%S'
			case 'X': return date('H:i:s',self::$time);
			case 'z': return date('T',self::$time); // EST etc
			case 'Z': return date('O',self::$time); // -0500 etc
			case 'c':                                     // depends on locale
				return self::strftime(self::$locale['c'],self::$time);
			case 'D': return date('m/d/y',self::$time); // '%m/%d/%y'
			case 'F': return date('Y-m-d',self::$time); // '%y-%m-%d'
			case 's': return date('U',self::$time); // timestamp
			case 'x':                                     // depends on locale
				return self::strftime(self::$locale['x'],self::$time);
			case 'n': return "\n";
			case 't': return "\t";
			case '%': return '%';
			default : return $format;
		}
	}
	static public function tag_date(&$data,$format,$key='time'){
		$args=func_get_args();
		$time=data::get_data($args,1,'time');
		if (!is_numeric($time)) $time=strtotime($time.' GMT');
		self::p(date($format,$time));
	}
	static public function tag_strftime(&$data,$format,$key='time'){
		$args=func_get_args();
		$time=data::get_data($args,1,'time');
		if (!is_numeric($time)) $time=strtotime($time.' GMT');
		self::echo_html(self::strftime($format,$time));
	}
}