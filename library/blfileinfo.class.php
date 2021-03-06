<?php

class BLFileinfo {
	var $extension;
		
	public function __construct(){
		$ext_to_content_type=array();
		$ext_to_content_type['ez']='application/andrew-inset';
		$ext_to_content_type['atom']='application/atom+xml';
		$ext_to_content_type['hqx']='application/mac-binhex40';
		$ext_to_content_type['cpt']='application/mac-compactpro';
		$ext_to_content_type['mathml']='application/mathml+xml';
		$ext_to_content_type['doc']='application/msword';
		$ext_to_content_type['bin']='application/octet-stream';
		$ext_to_content_type['dms']='application/octet-stream';
		$ext_to_content_type['lha']='application/octet-stream';
		$ext_to_content_type['lzh']='application/octet-stream';
		$ext_to_content_type['exe']='application/octet-stream';
		$ext_to_content_type['class']='application/octet-stream';
		$ext_to_content_type['so']='application/octet-stream';
		$ext_to_content_type['dll']='application/octet-stream';
		$ext_to_content_type['dmg']='application/octet-stream';
		$ext_to_content_type['oda']='application/oda';
		$ext_to_content_type['ogg']='application/ogg';
		$ext_to_content_type['pdf']='application/pdf';
		$ext_to_content_type['ai']='application/postscript';
		$ext_to_content_type['eps']='application/postscript';
		$ext_to_content_type['ps']='application/postscript';
		$ext_to_content_type['rdf']='application/rdf+xml';
		$ext_to_content_type['smi']='application/smil';
		$ext_to_content_type['smil']='application/smil';
		$ext_to_content_type['gram']='application/srgs';
		$ext_to_content_type['grxml']='application/srgs+xml';
		$ext_to_content_type['mif']='application/vnd.mif';
		$ext_to_content_type['xul']='application/vnd.mozilla.xul+xml';
		$ext_to_content_type['xls']='application/vnd.ms-excel';
		$ext_to_content_type['ppt']='application/vnd.ms-powerpoint';
		$ext_to_content_type['wbxml']='application/vnd.wap.wbxml';
		$ext_to_content_type['wmlc']='application/vnd.wap.wmlc';
		$ext_to_content_type['wmlsc']='application/vnd.wap.wmlscriptc';
		$ext_to_content_type['vxml']='application/voicexml+xml';
		$ext_to_content_type['bcpio']='application/x-bcpio';
		$ext_to_content_type['vcd']='application/x-cdlink';
		$ext_to_content_type['pgn']='application/x-chess-pgn';
		$ext_to_content_type['cpio']='application/x-cpio';
		$ext_to_content_type['csh']='application/x-csh';
		$ext_to_content_type['dcr']='application/x-director';
		$ext_to_content_type['dir']='application/x-director';
		$ext_to_content_type['dxr']='application/x-director';
		$ext_to_content_type['dvi']='application/x-dvi';
		$ext_to_content_type['spl']='application/x-futuresplash';
		$ext_to_content_type['gtar']='application/x-gtar';
		$ext_to_content_type['hdf']='application/x-hdf';
		$ext_to_content_type['js']='application/x-javascript';
		$ext_to_content_type['skp']='application/x-koan';
		$ext_to_content_type['skd']='application/x-koan';
		$ext_to_content_type['skt']='application/x-koan';
		$ext_to_content_type['skm']='application/x-koan';
		$ext_to_content_type['latex']='application/x-latex';
		$ext_to_content_type['nc']='application/x-netcdf';
		$ext_to_content_type['cdf']='application/x-netcdf';
		$ext_to_content_type['sh']='application/x-sh';
		$ext_to_content_type['shar']='application/x-shar';
		$ext_to_content_type['swf']='application/x-shockwave-flash';
		$ext_to_content_type['sit']='application/x-stuffit';
		$ext_to_content_type['sv4cpio']='application/x-sv4cpio';
		$ext_to_content_type['sv4crc']='application/x-sv4crc';
		$ext_to_content_type['tar']='application/x-tar';
		$ext_to_content_type['tcl']='application/x-tcl';
		$ext_to_content_type['tex']='application/x-tex';
		$ext_to_content_type['texinfo']='application/x-texinfo';
		$ext_to_content_type['texi']='application/x-texinfo';
		$ext_to_content_type['t']='application/x-troff';
		$ext_to_content_type['tr']='application/x-troff';
		$ext_to_content_type['roff']='application/x-troff';
		$ext_to_content_type['man']='application/x-troff-man';
		$ext_to_content_type['me']='application/x-troff-me';
		$ext_to_content_type['ms']='application/x-troff-ms';
		$ext_to_content_type['ustar']='application/x-ustar';
		$ext_to_content_type['src']='application/x-wais-source';
		$ext_to_content_type['xhtml']='application/xhtml+xml';
		$ext_to_content_type['xht']='application/xhtml+xml';
		$ext_to_content_type['xslt']='application/xslt+xml';
		$ext_to_content_type['xml']='application/xml';
		$ext_to_content_type['xsl']='application/xml';
		$ext_to_content_type['dtd']='application/xml-dtd';
		$ext_to_content_type['zip']='application/zip';
		$ext_to_content_type['au']='audio/basic';
		$ext_to_content_type['snd']='audio/basic';
		$ext_to_content_type['mid']='audio/midi';
		$ext_to_content_type['midi']='audio/midi';
		$ext_to_content_type['kar']='audio/midi';
		$ext_to_content_type['mpga']='audio/mpeg';
		$ext_to_content_type['mp2']='audio/mpeg';
		$ext_to_content_type['mp3']='audio/mpeg';
		$ext_to_content_type['aif']='audio/x-aiff';
		$ext_to_content_type['aiff']='audio/x-aiff';
		$ext_to_content_type['aifc']='audio/x-aiff';
		$ext_to_content_type['m3u']='audio/x-mpegurl';
		$ext_to_content_type['ram']='audio/x-pn-realaudio';
		$ext_to_content_type['ra']='audio/x-pn-realaudio';
		$ext_to_content_type['rm']='application/vnd.rn-realmedia';
		$ext_to_content_type['wav']='audio/x-wav';
		$ext_to_content_type['pdb']='chemical/x-pdb';
		$ext_to_content_type['xyz']='chemical/x-xyz';
		$ext_to_content_type['bmp']='image/bmp';
		$ext_to_content_type['cgm']='image/cgm';
		$ext_to_content_type['gif']='image/gif';
		$ext_to_content_type['ief']='image/ief';
		$ext_to_content_type['jpeg']='image/jpeg';
		$ext_to_content_type['jpg']='image/jpeg';
		$ext_to_content_type['jpe']='image/jpeg';
		$ext_to_content_type['png']='image/png';
		$ext_to_content_type['svg']='image/svg+xml';
		$ext_to_content_type['tiff']='image/tiff';
		$ext_to_content_type['tif']='image/tiff';
		$ext_to_content_type['djvu']='image/vnd.djvu';
		$ext_to_content_type['djv']='image/vnd.djvu';
		$ext_to_content_type['wbmp']='image/vnd.wap.wbmp';
		$ext_to_content_type['ras']='image/x-cmu-raster';
		$ext_to_content_type['ico']='image/x-icon';
		$ext_to_content_type['pnm']='image/x-portable-anymap';
		$ext_to_content_type['pbm']='image/x-portable-bitmap';
		$ext_to_content_type['pgm']='image/x-portable-graymap';
		$ext_to_content_type['ppm']='image/x-portable-pixmap';
		$ext_to_content_type['rgb']='image/x-rgb';
		$ext_to_content_type['xbm']='image/x-xbitmap';
		$ext_to_content_type['xpm']='image/x-xpixmap';
		$ext_to_content_type['xwd']='image/x-xwindowdump';
		$ext_to_content_type['igs']='model/iges';
		$ext_to_content_type['iges']='model/iges';
		$ext_to_content_type['msh']='model/mesh';
		$ext_to_content_type['mesh']='model/mesh';
		$ext_to_content_type['silo']='model/mesh';
		$ext_to_content_type['wrl']='model/vrml';
		$ext_to_content_type['vrml']='model/vrml';
		$ext_to_content_type['ics']='text/calendar';
		$ext_to_content_type['ifb']='text/calendar';
		$ext_to_content_type['css']='text/css';
		$ext_to_content_type['html']='text/html';
		$ext_to_content_type['htm']='text/html';
		$ext_to_content_type['asc']='text/plain';
		$ext_to_content_type['txt']='text/plain';
		$ext_to_content_type['rtx']='text/richtext';
		$ext_to_content_type['rtf']='text/rtf';
		$ext_to_content_type['sgml']='text/sgml';
		$ext_to_content_type['sgm']='text/sgml';
		$ext_to_content_type['tsv']='text/tab-separated-values';
		$ext_to_content_type['wml']='text/vnd.wap.wml';
		$ext_to_content_type['wmls']='text/vnd.wap.wmlscript';
		$ext_to_content_type['etx']='text/x-setext';
		$ext_to_content_type['mpeg']='video/mpeg';
		$ext_to_content_type['mpg']='video/mpeg';
		$ext_to_content_type['mpe']='video/mpeg';
		$ext_to_content_type['qt']='video/quicktime';
		$ext_to_content_type['mov']='video/quicktime';
		$ext_to_content_type['mxu']='video/vnd.mpegurl';
		$ext_to_content_type['m4u']='video/vnd.mpegurl';
		$ext_to_content_type['avi']='video/x-msvideo';
		$ext_to_content_type['movie']='video/x-sgi-movie';
		$ext_to_content_type['ice']='x-conference/x-cooltalk';
		$this->extensions=$ext_to_content_type;
	}
	
	function ext2mimetype($ext){
		$ext=preg_replace("@.*\.@", "", strtolower($ext));
	    if ($this->extensions[$ext]) {
	        return $this->extensions[$ext];
	    } else {
	        return "application/octet-stream";
	    }
	}
	

}

?>
