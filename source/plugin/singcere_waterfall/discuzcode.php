<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}



function parsemedia2($params, $url) {
	$params = explode(',', $params);
	$width = intval($params[1]) > 800 ? 800 : intval($params[1]);
	$height = intval($params[2]) > 600 ? 600 : intval($params[2]);
	$url = addslashes($url);
	if($flv = parseflv2($url, $width, $height)) {
		return $flv;
	}
	if(in_array(count($params), array(3, 4))) {
		$type = $params[0];
		$url = str_replace(array('<', '>'), '', str_replace('\\"', '\"', $url));
		switch($type) {
			case 'mp3':
			case 'wma':
			case 'ra':
			case 'ram':
			case 'wav':
			case 'mid':
				return parseaudio2($url, $width);
			case 'rm':
			case 'rmvb':
			case 'rtsp':
				$mediaid = 'media_'.random(3);
				return '<object classid="clsid:CFCDAA03-8BE4-11cf-B84B-0020AFBBCCFA" width="'.$width.'" height="'.$height.'"><param name="autostart" value="0" /><param name="src" value="'.$url.'" /><param name="controls" value="imagewindow" /><param name="console" value="'.$mediaid.'_" /><embed src="'.$url.'" autostart="0" type="audio/x-pn-realaudio-plugin" controls="imagewindow" console="'.$mediaid.'_" width="'.$width.'" height="'.$height.'"></embed></object><br /><object classid="clsid:CFCDAA03-8BE4-11CF-B84B-0020AFBBCCFA" width="'.$width.'" height="32"><param name="src" value="'.$url.'" /><param name="controls" value="controlpanel" /><param name="console" value="'.$mediaid.'_" /><embed src="'.$url.'" autostart="0" type="audio/x-pn-realaudio-plugin" controls="controlpanel" console="'.$mediaid.'_" width="'.$width.'" height="32"></embed></object>';
			case 'flv':
				$randomid = 'flv_'.random(3);
				return '<span id="'.$randomid.'"></span><script type="text/javascript" reload="1">$(\''.$randomid.'\').innerHTML=AC_FL_RunContent(\'width\', \''.$width.'\', \'height\', \''.$height.'\', \'allowNetworking\', \'internal\', \'allowScriptAccess\', \'never\', \'src\', \''.STATICURL.'image/common/flvplayer.swf\', \'flashvars\', \'file='.rawurlencode($url).'\', \'quality\', \'high\', \'wmode\', \'transparent\', \'allowfullscreen\', \'true\');</script>';
			case 'swf':
				$randomid = 'swf_'.random(3);
				return '<span id="'.$randomid.'"></span><script type="text/javascript" reload="1">$(\''.$randomid.'\').innerHTML=AC_FL_RunContent(\'width\', \''.$width.'\', \'height\', \''.$height.'\', \'allowNetworking\', \'internal\', \'allowScriptAccess\', \'never\', \'src\', \''.$url.'\', \'quality\', \'high\', \'bgcolor\', \'#ffffff\', \'wmode\', \'transparent\', \'allowfullscreen\', \'true\');</script>';
			case 'asf':
			case 'asx':
			case 'wmv':
			case 'mms':
			case 'avi':
			case 'mpg':
			case 'mpeg':
				return '<object classid="clsid:6BF52A52-394A-11d3-B153-00C04F79FAA6" width="'.$width.'" height="'.$height.'"><param name="invokeURLs" value="0"><param name="autostart" value="0" /><param name="url" value="'.$url.'" /><embed src="'.$url.'" autostart="0" type="application/x-mplayer2" width="'.$width.'" height="'.$height.'"></embed></object>';
			case 'mov':
				return '<object classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B" width="'.$width.'" height="'.$height.'"><param name="autostart" value="false" /><param name="src" value="'.$url.'" /><embed src="'.$url.'" autostart="false" type="video/quicktime" controller="true" width="'.$width.'" height="'.$height.'"></embed></object>';
			default:
				return '<a href="'.$url.'" target="_blank">'.$url.'</a>';
		}
	}
	return;
}


function parseaudio2($url, $width = 400) {
	$ext = strtolower(substr(strrchr($url, '.'), 1, 5));
	switch($ext) {
		case 'mp3':
			$randomid = 'mp3_'.random(3);
			return '<span id="'.$randomid.'"></span><script type="text/javascript" reload="1">$(\''.$randomid.'\').innerHTML=AC_FL_RunContent(\'FlashVars\', \'soundFile='.urlencode($url).'\', \'width\', \'290\', \'height\', \'24\', \'allowNetworking\', \'internal\', \'allowScriptAccess\', \'never\', \'src\', \''.STATICURL.'image/common/player.swf\', \'quality\', \'high\', \'bgcolor\', \'#FFFFFF\', \'menu\', \'false\', \'wmode\', \'transparent\', \'allowscriptaccess\', \'none\', \'allowNetworking\', \'internal\');</script>';
		case 'wma':
		case 'mid':
		case 'wav':
			return '<object classid="clsid:6BF52A52-394A-11d3-B153-00C04F79FAA6" width="'.$width.'" height="64"><param name="invokeURLs" value="0"><param name="autostart" value="0" /><param name="url" value="'.$url.'" /><embed src="'.$url.'" autostart="0" type="application/x-mplayer2" width="'.$width.'" height="64"></embed></object>';
		case 'ra':
		case 'rm':
		case 'ram':
			$mediaid = 'media_'.random(3);
			return '<object classid="clsid:CFCDAA03-8BE4-11CF-B84B-0020AFBBCCFA" width="'.$width.'" height="32"><param name="autostart" value="0" /><param name="src" value="'.$url.'" /><param name="controls" value="controlpanel" /><param name="console" value="'.$mediaid.'_" /><embed src="'.$url.'" autostart="0" type="audio/x-pn-realaudio-plugin" controls="ControlPanel" console="'.$mediaid.'_" width="'.$width.'" height="32"></embed></object>';
	}
}


function parseflv2($url, $width = 0, $height = 0) {
	$lowerurl = strtolower($url);
	$flv = '';
	$imgurl = '';
	if($lowerurl != str_replace(array('player.youku.com/player.php/sid/','tudou.com/v/','player.ku6.com/refer/'), '', $lowerurl)) {
		$flv = $url;
	} elseif(strpos($lowerurl, 'v.youku.com/v_show/') !== FALSE) {
		$ctx = stream_context_create(array('http' => array('timeout' => 10)));
		if(preg_match("/http:\/\/v.youku.com\/v_show\/id_([^\/]+)(.html|)/i", $url, $matches)) {
			$flv = 'http://player.youku.com/player.php/sid/'.$matches[1].'/v.swf';
			if(!$width && !$height) {
				$api = 'http://v.youku.com/player/getPlayList/VideoIDS/'.$matches[1];
				$str = stripslashes(file_get_contents($api, false, $ctx));
				if(!empty($str) && preg_match("/\"logo\":\"(.+?)\"/i", $str, $image)) {
					$url = substr($image[1], 0, strrpos($image[1], '/')+1);
					$filename = substr($image[1], strrpos($image[1], '/')+2);
					$imgurl = $url.'0'.$filename;
				}
			}
		}
	} elseif(strpos($lowerurl, 'tudou.com/programs/view/') !== FALSE) {
		if(preg_match("/http:\/\/(www.)?tudou.com\/programs\/view\/([^\/]+)/i", $url, $matches)) {
			$flv = 'http://www.tudou.com/v/'.$matches[2];
			if(!$width && !$height) {
				$str = file_get_contents($url, false, $ctx);
				if(!empty($str) && preg_match("/<span class=\"s_pic\">(.+?)<\/span>/i", $str, $image)) {
					$imgurl = trim($image[1]);
				}
			}
		}
	} elseif(strpos($lowerurl, 'v.ku6.com/show/') !== FALSE) {
		if(preg_match("/http:\/\/v.ku6.com\/show\/([^\/]+).html/i", $url, $matches)) {
			$flv = 'http://player.ku6.com/refer/'.$matches[1].'/v.swf';
			if(!$width && !$height) {
				$api = 'http://vo.ku6.com/fetchVideo4Player/1/'.$matches[1].'.html';
				$str = file_get_contents($api, false, $ctx);
				if(!empty($str) && preg_match("/\"picpath\":\"(.+?)\"/i", $str, $image)) {
					$imgurl = str_replace(array('\u003a', '\u002e'), array(':', '.'), $image[1]);
				}
			}
		}
	} elseif(strpos($lowerurl, 'v.ku6.com/special/show_') !== FALSE) {
		if(preg_match("/http:\/\/v.ku6.com\/special\/show_\d+\/([^\/]+).html/i", $url, $matches)) {
			$flv = 'http://player.ku6.com/refer/'.$matches[1].'/v.swf';
			if(!$width && !$height) {
				$api = 'http://vo.ku6.com/fetchVideo4Player/1/'.$matches[1].'.html';
				$str = file_get_contents($api, false, $ctx);
				if(!empty($str) && preg_match("/\"picpath\":\"(.+?)\"/i", $str, $image)) {
					$imgurl = str_replace(array('\u003a', '\u002e'), array(':', '.'), $image[1]);
				}
			}
		}
	} elseif(strpos($lowerurl, 'www.youtube.com/watch?') !== FALSE) {
		if(preg_match("/http:\/\/www.youtube.com\/watch\?v=([^\/&]+)&?/i", $url, $matches)) {
			$flv = 'http://www.youtube.com/v/'.$matches[1].'&hl=zh_CN&fs=1';
			if(!$width && !$height) {
				$str = file_get_contents($url, false, $ctx);
				if(!empty($str) && preg_match("/'VIDEO_HQ_THUMB':\s'(.+?)'/i", $str, $image)) {
					$url = substr($image[1], 0, strrpos($image[1], '/')+1);
					$filename = substr($image[1], strrpos($image[1], '/')+3);
					$imgurl = $url.$filename;
				}
			}
		}
	} elseif(strpos($lowerurl, 'tv.mofile.com/') !== FALSE) {
		if(preg_match("/http:\/\/tv.mofile.com\/([^\/]+)/i", $url, $matches)) {
			$flv = 'http://tv.mofile.com/cn/xplayer.swf?v='.$matches[1];
			if(!$width && !$height) {
				$str = file_get_contents($url, false, $ctx);
				if(!empty($str) && preg_match("/thumbpath=\"(.+?)\";/i", $str, $image)) {
					$imgurl = trim($image[1]);
				}
			}
		}
	} elseif(strpos($lowerurl, 'v.mofile.com/show/') !== FALSE) {
		if(preg_match("/http:\/\/v.mofile.com\/show\/([^\/]+).shtml/i", $url, $matches)) {
			$flv = 'http://tv.mofile.com/cn/xplayer.swf?v='.$matches[1];
			if(!$width && !$height) {
				$str = file_get_contents($url, false, $ctx);
				if(!empty($str) && preg_match("/thumbpath=\"(.+?)\";/i", $str, $image)) {
					$imgurl = trim($image[1]);
				}
			}
		}
	} elseif(strpos($lowerurl, 'video.sina.com.cn/v/b/') !== FALSE) {
		if(preg_match("/http:\/\/video.sina.com.cn\/v\/b\/(\d+)-(\d+).html/i", $url, $matches)) {
			$flv = 'http://vhead.blog.sina.com.cn/player/outer_player.swf?vid='.$matches[1];
			if(!$width && !$height) {
				$api = 'http://interface.video.sina.com.cn/interface/common/getVideoImage.php?vid='.$matches[1];
				$str = file_get_contents($api, false, $ctx);
				if(!empty($str)) {
					$imgurl = str_replace('imgurl=', '', trim($str));
				}
			}
		}
	} elseif(strpos($lowerurl, 'you.video.sina.com.cn/b/') !== FALSE) {
		if(preg_match("/http:\/\/you.video.sina.com.cn\/b\/(\d+)-(\d+).html/i", $url, $matches)) {
			$flv = 'http://vhead.blog.sina.com.cn/player/outer_player.swf?vid='.$matches[1];
			if(!$width && !$height) {
				$api = 'http://interface.video.sina.com.cn/interface/common/getVideoImage.php?vid='.$matches[1];
				$str = file_get_contents($api, false, $ctx);
				if(!empty($str)) {
					$imgurl = str_replace('imgurl=', '', trim($str));
				}
			}
		}
	} elseif(strpos($lowerurl, 'http://my.tv.sohu.com/u/') !== FALSE) {
		if(preg_match("/http:\/\/my.tv.sohu.com\/u\/[^\/]+\/(\d+)/i", $url, $matches)) {
			$flv = 'http://v.blog.sohu.com/fo/v4/'.$matches[1];
			if(!$width && !$height) {
				$api = 'http://v.blog.sohu.com/videinfo.jhtml?m=view&id='.$matches[1].'&outType=3';
				$str = file_get_contents($api, false, $ctx);
				if(!empty($str) && preg_match("/\"cutCoverURL\":\"(.+?)\"/i", $str, $image)) {
					$imgurl = str_replace(array('\u003a', '\u002e'), array(':', '.'), $image[1]);
				}
			}
		}
	} elseif(strpos($lowerurl, 'http://v.blog.sohu.com/u/') !== FALSE) {
		if(preg_match("/http:\/\/v.blog.sohu.com\/u\/[^\/]+\/(\d+)/i", $url, $matches)) {
			$flv = 'http://v.blog.sohu.com/fo/v4/'.$matches[1];
			if(!$width && !$height) {
				$api = 'http://v.blog.sohu.com/videinfo.jhtml?m=view&id='.$matches[1].'&outType=3';
				$str = file_get_contents($api, false, $ctx);
				if(!empty($str) && preg_match("/\"cutCoverURL\":\"(.+?)\"/i", $str, $image)) {
					$imgurl = str_replace(array('\u003a', '\u002e'), array(':', '.'), $image[1]);
				}
			}
		}
	} elseif(strpos($lowerurl, 'http://www.ouou.com/fun_funview') !== FALSE) {
		$str = file_get_contents($url, false, $ctx);
		if(!empty($str) && preg_match("/var\sflv\s=\s'(.+?)';/i", $str, $matches)) {
			$flv = $_G['style']['imgdir'].'/flvplayer.swf?&autostart=true&file='.urlencode($matches[1]);
			if(!$width && !$height && preg_match("/var\simga=\s'(.+?)';/i", $str, $image)) {
				$imgurl = trim($image[1]);
			}
		}
	} elseif(strpos($lowerurl, 'http://www.56.com') !== FALSE) {

		if(preg_match("/http:\/\/www.56.com\/\S+\/play_album-aid-(\d+)_vid-(.+?).html/i", $url, $matches)) {
			$flv = 'http://player.56.com/v_'.$matches[2].'.swf';
			$matches[1] = $matches[2];
		} elseif(preg_match("/http:\/\/www.56.com\/\S+\/([^\/]+).html/i", $url, $matches)) {
			$flv = 'http://player.56.com/'.$matches[1].'.swf';
		}
		if(!$width && !$height && !empty($matches[1])) {
			$api = 'http://vxml.56.com/json/'.str_replace('v_', '', $matches[1]).'/?src=out';
			$str = file_get_contents($api, false, $ctx);
			if(!empty($str) && preg_match("/\"img\":\"(.+?)\"/i", $str, $image)) {
				$imgurl = trim($image[1]);
			}
		}
	}
	if($flv) {
		if(!$width && !$height) {
			return array('flv' => $flv, 'imgurl' => $imgurl);
		} else {
			$width = addslashes($width);
			$height = addslashes($height);
			$flv = addslashes($flv);
			$randomid = 'flv_'.random(3);
			return '<span id="'.$randomid.'"></span><script type="text/javascript" reload="1">$(\''.$randomid.'\').innerHTML=eval(" \"showDialog(\'\" + AC_FL_RunContent(\'width\', \''.$width.'\', \'height\', \''.$height.'\', \'allowNetworking\', \'internal\', \'allowScriptAccess\', \'never\', \'src\', \''.$flv.'\', \'quality\', \'high\', \'bgcolor\', \'#ffffff\', \'wmode\', \'transparent\', \'allowfullscreen\', \'true\').toString()");</script>';
			
		}
	} else {
		return FALSE;
	}
}

//From:www_caogen8_co
?>