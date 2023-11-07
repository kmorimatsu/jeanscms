<?php
/*
 * Jeans CMS (GPL license)
 * $Id: tables.php 312 2010-11-21 07:09:47Z kmorimatsu $
 */


class tables extends jeans {
	static public function language($shortname){
		switch(strtolower($shortname)){
			case 'af':     return 'afrikaans';
			case 'sq':     return 'albanian';
			case 'ar-dz':  return 'arabic-dz';
			case 'ar-bh':  return 'arabic-bh';
			case 'ar-eg':  return 'arabic-eg';
			case 'ar-iq':  return 'arabic-iq';
			case 'ar-jo':  return 'arabic-jo';
			case 'ar-kw':  return 'arabic-kw';
			case 'ar-lb':  return 'arabic-lb';
			case 'ar-ly':  return 'arabic-ly';
			case 'ar-ma':  return 'arabic-ma';
			case 'ar-om':  return 'arabic-om';
			case 'ar-qa':  return 'arabic-qa';
			case 'ar-sa':  return 'arabic-sa';
			case 'ar-sy':  return 'arabic-sy';
			case 'ar-tn':  return 'arabic-tn';
			case 'ar-ae':  return 'arabic-ae';
			case 'ar-ye':  return 'arabic-ye';
			case 'ar':     return 'arabic';
			case 'hy':     return 'armenian';
			case 'as':     return 'assamese';
			case 'az':     return 'azeri';
			case 'az':     return 'azeri';
			case 'eu':     return 'basque';
			case 'be':     return 'belarusian';
			case 'bn':     return 'bengali';
			case 'bg':     return 'bulgarian';
			case 'ca':     return 'catalan';
			case 'zh-cn':  return 'chinese-cn';
			case 'zh-hk':  return 'chinese-hk';
			case 'zh-mo':  return 'chinese-mo';
			case 'zh-sg':  return 'chinese-sg';
			case 'zh-tw':  return 'chinese-tw';
			case 'zh':     return 'chinese';
			case 'hr':     return 'croatian';
			case 'cs':     return 'chech';
			case 'da':     return 'danish';
			case 'div':    return 'divehi';
			case 'nl-be':  return 'dutch-be';
			case 'nl':     return 'dutch';
			case 'en-au':  return 'english-au';
			case 'en-bz':  return 'english-bz';
			case 'en-ca':  return 'english-ca';
			case 'en-ie':  return 'english-ie';
			case 'en-jm':  return 'english-jm';
			case 'en-nz':  return 'english-nz';
			case 'en-ph':  return 'english-ph';
			case 'en-za':  return 'english-za';
			case 'en-tt':  return 'english-tt';
			case 'en-gb':  return 'english-gb';
			case 'en-us':  return 'english-us';
			case 'en-zw':  return 'english-zw';
			case 'en':     return 'english';
			case 'et':     return 'estonian';
			case 'fo':     return 'faeroese';
			case 'fa':     return 'farsi';
			case 'fi':     return 'finnish';
			case 'fr-be':  return 'french-be';
			case 'fr-ca':  return 'french-ca';
			case 'fr':     return 'french';
			case 'fr-lu':  return 'french-lu';
			case 'fr-mc':  return 'french-mc';
			case 'fr-ch':  return 'french-ch';
			case 'mk':     return 'fyro';
			case 'gd':     return 'gaelic';
			case 'ka':     return 'georgian';
			case 'de-at':  return 'german-at';
			case 'de':     return 'german';
			case 'de-li':  return 'german-li';
			case 'de-lu':  return 'german-lu';
			case 'de-ch':  return 'german-ch';
			case 'el':     return 'greek';
			case 'gu':     return 'gujarati';
			case 'he':     return 'hebrew';
			case 'hi':     return 'hindi';
			case 'hu':     return 'hungarian';
			case 'is':     return 'icelandic';
			case 'id':     return 'indonesian';
			case 'it':     return 'italian';
			case 'it-ch':  return 'italian-ch';
			case 'ja':     return 'japanese';
			case 'kn':     return 'kannada';
			case 'kk':     return 'kazakh';
			case 'kok':    return 'konkani';
			case 'ko':     return 'korean';
			case 'kz':     return 'kyrgyz';
			case 'lv':     return 'latvian';
			case 'lt':     return 'lithuanian';
			case 'ms':     return 'malay';
			case 'ms':     return 'malay';
			case 'ml':     return 'malayalam';
			case 'mt':     return 'maltese';
			case 'mr':     return 'marathi';
			case 'mn':     return 'mongolian';
			case 'ne':     return 'nepali';
			case 'nb-no':  return 'norwegian-nb';
			case 'no':     return 'norwegian';
			case 'nn-no':  return 'norwegian-nn';
			case 'or':     return 'oriya';
			case 'pl':     return 'polish';
			case 'pt-br':  return 'portuguese-br';
			case 'pt':     return 'portuguese';
			case 'pa':     return 'punjabi';
			case 'rm':     return 'rhaeto';
			case 'ro-md':  return 'romanian-md';
			case 'ro':     return 'romanian';
			case 'ru-md':  return 'russian-md';
			case 'ru':     return 'russian';
			case 'sa':     return 'sanskrit';
			case 'sr':     return 'serbian';
			case 'sr':     return 'serbian';
			case 'sk':     return 'slovak';
			case 'ls':     return 'slovenian';
			case 'sb':     return 'sorbian';
			case 'es-ar':  return 'spanish-ar';
			case 'es-bo':  return 'spanish-bo';
			case 'es-cl':  return 'spanish-cl';
			case 'es-co':  return 'spanish-co';
			case 'es-cr':  return 'spanish-cr';
			case 'es-do':  return 'spanish-do';
			case 'es-ec':  return 'spanish-ec';
			case 'es-sv':  return 'spanish-sv';
			case 'es-gt':  return 'spanish-gt';
			case 'es-hn':  return 'spanish-hn';
			case 'es':     return 'spanish';
			case 'es-mx':  return 'spanish-mx';
			case 'es-ni':  return 'spanish-ni';
			case 'es-pa':  return 'spanish-pa';
			case 'es-py':  return 'spanish-py';
			case 'es-pe':  return 'spanish-pe';
			case 'es-pr':  return 'spanish-pr';
			case 'es':     return 'spanish';
			case 'es-us':  return 'spanish-us';
			case 'es-uy':  return 'spanish-uy';
			case 'es-ve':  return 'spanish-ve';
			case 'sx':     return 'sutu';
			case 'sw':     return 'swahili';
			case 'sv-fi':  return 'swedish-fi';
			case 'sv':     return 'swedish';
			case 'syr':    return 'syriac';
			case 'ta':     return 'tamil';
			case 'tt':     return 'tatar';
			case 'te':     return 'telugu';
			case 'th':     return 'thai';
			case 'ts':     return 'tsonga';
			case 'tn':     return 'tswana';
			case 'tr':     return 'turkish';
			case 'uk':     return 'ukrainian';
			case 'ur':     return 'urdu';
			case 'uz':     return 'uzbek';
			case 'uz':     return 'uzbek';
			case 'vi':     return 'vietnamese';
			case 'xh':     return 'xhosa';
			case 'yi':     return 'yiddish';
			default:       return false;
		}
	}
	static public function mime($extension){
		static $cache=array (
			  'ez' => 'application/andrew-inset',
			  'atom' => 'application/atom+xml',
			  'hqx' => 'application/mac-binhex40',
			  'cpt' => 'application/mac-compactpro',
			  'mathml' => 'application/mathml+xml',
			  'doc' => 'application/msword',
			  'bin' => 'application/octet-stream',
			  'dms' => 'application/octet-stream',
			  'lha' => 'application/octet-stream',
			  'lzh' => 'application/octet-stream',
			  'exe' => 'application/octet-stream',
			  'class' => 'application/octet-stream',
			  'so' => 'application/octet-stream',
			  'dll' => 'application/octet-stream',
			  'dmg' => 'application/octet-stream',
			  'oda' => 'application/oda',
			  'ogg' => 'application/ogg',
			  'pdf' => 'application/pdf',
			  'ai' => 'application/postscript',
			  'eps' => 'application/postscript',
			  'ps' => 'application/postscript',
			  'rdf' => 'application/rdf+xml',
			  'smi' => 'application/smil',
			  'smil' => 'application/smil',
			  'gram' => 'application/srgs',
			  'grxml' => 'application/srgs+xml',
			  'mif' => 'application/vnd.mif',
			  'xul' => 'application/vnd.mozilla.xul+xml',
			  'xls' => 'application/vnd.ms-excel',
			  'ppt' => 'application/vnd.ms-powerpoint',
			  'wbxml' => 'application/vnd.wap.wbxml',
			  'wmlc' => 'application/vnd.wap.wmlc',
			  'wmlsc' => 'application/vnd.wap.wmlscriptc',
			  'vxml' => 'application/voicexml+xml',
			  'bcpio' => 'application/x-bcpio',
			  'vcd' => 'application/x-cdlink',
			  'pgn' => 'application/x-chess-pgn',
			  'cpio' => 'application/x-cpio',
			  'csh' => 'application/x-csh',
			  'dcr' => 'application/x-director',
			  'dir' => 'application/x-director',
			  'dxr' => 'application/x-director',
			  'dvi' => 'application/x-dvi',
			  'spl' => 'application/x-futuresplash',
			  'gtar' => 'application/x-gtar',
			  'hdf' => 'application/x-hdf',
			  'js' => 'application/x-javascript',
			  'skp' => 'application/x-koan',
			  'skd' => 'application/x-koan',
			  'skt' => 'application/x-koan',
			  'skm' => 'application/x-koan',
			  'latex' => 'application/x-latex',
			  'nc' => 'application/x-netcdf',
			  'cdf' => 'application/x-netcdf',
			  'sh' => 'application/x-sh',
			  'shar' => 'application/x-shar',
			  'swf' => 'application/x-shockwave-flash',
			  'sit' => 'application/x-stuffit',
			  'sv4cpio' => 'application/x-sv4cpio',
			  'sv4crc' => 'application/x-sv4crc',
			  'tar' => 'application/x-tar',
			  'tcl' => 'application/x-tcl',
			  'tex' => 'application/x-tex',
			  'texinfo' => 'application/x-texinfo',
			  'texi' => 'application/x-texinfo',
			  't' => 'application/x-troff',
			  'tr' => 'application/x-troff',
			  'roff' => 'application/x-troff',
			  'man' => 'application/x-troff-man',
			  'me' => 'application/x-troff-me',
			  'ms' => 'application/x-troff-ms',
			  'ustar' => 'application/x-ustar',
			  'src' => 'application/x-wais-source',
			  'xhtml' => 'application/xhtml+xml',
			  'xht' => 'application/xhtml+xml',
			  'xslt' => 'application/xslt+xml',
			  'xml' => 'application/xml',
			  'xsl' => 'application/xml',
			  'dtd' => 'application/xml-dtd',
			  'zip' => 'application/zip',
			  'au' => 'audio/basic',
			  'snd' => 'audio/basic',
			  'mid' => 'audio/midi',
			  'midi' => 'audio/midi',
			  'kar' => 'audio/midi',
			  'mpga' => 'audio/mpeg',
			  'mp2' => 'audio/mpeg',
			  'mp3' => 'audio/mpeg',
			  'aif' => 'audio/x-aiff',
			  'aiff' => 'audio/x-aiff',
			  'aifc' => 'audio/x-aiff',
			  'm3u' => 'audio/x-mpegurl',
			  'ram' => 'audio/x-pn-realaudio',
			  'ra' => 'audio/x-pn-realaudio',
			  'rm' => 'application/vnd.rn-realmedia',
			  'wav' => 'audio/x-wav',
			  'pdb' => 'chemical/x-pdb',
			  'xyz' => 'chemical/x-xyz',
			  'bmp' => 'image/bmp',
			  'cgm' => 'image/cgm',
			  'gif' => 'image/gif',
			  'ief' => 'image/ief',
			  'jpeg' => 'image/jpeg',
			  'jpg' => 'image/jpeg',
			  'jpe' => 'image/jpeg',
			  'png' => 'image/png',
			  'svg' => 'image/svg+xml',
			  'tiff' => 'image/tiff',
			  'tif' => 'image/tiff',
			  'djvu' => 'image/vnd.djvu',
			  'djv' => 'image/vnd.djvu',
			  'wbmp' => 'image/vnd.wap.wbmp',
			  'ras' => 'image/x-cmu-raster',
			  'ico' => 'image/x-icon',
			  'pnm' => 'image/x-portable-anymap',
			  'pbm' => 'image/x-portable-bitmap',
			  'pgm' => 'image/x-portable-graymap',
			  'ppm' => 'image/x-portable-pixmap',
			  'rgb' => 'image/x-rgb',
			  'xbm' => 'image/x-xbitmap',
			  'xpm' => 'image/x-xpixmap',
			  'xwd' => 'image/x-xwindowdump',
			  'igs' => 'model/iges',
			  'iges' => 'model/iges',
			  'msh' => 'model/mesh',
			  'mesh' => 'model/mesh',
			  'silo' => 'model/mesh',
			  'wrl' => 'model/vrml',
			  'vrml' => 'model/vrml',
			  'ics' => 'text/calendar',
			  'ifb' => 'text/calendar',
			  'css' => 'text/css',
			  'html' => 'text/html',
			  'htm' => 'text/html',
			  'asc' => 'text/plain',
			  'txt' => 'text/plain',
			  'rtx' => 'text/richtext',
			  'rtf' => 'text/rtf',
			  'sgml' => 'text/sgml',
			  'sgm' => 'text/sgml',
			  'tsv' => 'text/tab-separated-values',
			  'wml' => 'text/vnd.wap.wml',
			  'wmls' => 'text/vnd.wap.wmlscript',
			  'etx' => 'text/x-setext',
			  'mpeg' => 'video/mpeg',
			  'mpg' => 'video/mpeg',
			  'mpe' => 'video/mpeg',
			  'qt' => 'video/quicktime',
			  'mov' => 'video/quicktime',
			  'mxu' => 'video/vnd.mpegurl',
			  'm4u' => 'video/vnd.mpegurl',
			  'avi' => 'video/x-msvideo',
			  'movie' => 'video/x-sgi-movie',
			  'ice' => 'x-conference/x-cooltalk',
			);
		$extension=strtolower($extension);
		if (preg_match('/\.([a-z]+)$/',$extension,$m)) $extension=$m[1];
		if (isset($cache[$extension])) return $cache[$extension];
		return 'application/unknown';
	}
}