<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

function html2txt($str, $begin = 0, $end = 65535){
	$str = preg_replace("/\&lt\;/i", "<", $str);
	$str = preg_replace("/\&lt/i", "<", $str);
	$str = preg_replace("/<br \s*\/?\/>/i", "", $str);
	$str = preg_replace("/\&gt\;/i", ">", $str);
	$str = preg_replace("/\&gt/i", ">", $str);

	return substr($str, 0, $end);
}

function parseattach($tid, $pid, $attachtags, $message, $skipaids = array()) {
	$list_attach = array();
	$attachpids = is_array($pid) ? $pid : array($pid);
	foreach(C::t('forum_attachment_n')->fetch_all_by_id('tid:'.$tid, 'pid', $attachpids) as $attach) {
		
		$result['key'] = $attach['aid'];
		if($attach['isimage'] == 0) {
			$result['attachment'] = parseattachurl($attach['aid'], "singcere.net");
			$list_attach[$result['key']] = "<img height='20' width='20' src='source/plugin/singcere_waterfall/template/img/attach.gif'></img>";
		}else {
			$result['attachment'] = $attach['attachment'];
			$list_attach[$result['key']] = "<img class='zoom' onclick='zoom(this, this.src, 0, 0, 0)' src=data/attachment/forum/" . $result['attachment'] . ">";
		}
		
		
			
	}
	return $list_attach;
}

function get_tag_filter_item($num, $flag) {
	if($flag == 'custom') {
	
	} else {
		return C::t('#singcere_waterfall#dx')->get_hot_tag($num);
	} 
}

function get_forums() {
	global $wf_forum_limit, $wf_forum_display_limit_on, $_G;
	loadcache('forums');
	$forumarray = array();
	foreach($_G['cache']['forums'] as $forum) {
		if($forum['type'] == 'group') {
			$forumarray[$forum['fid']] = $forum;
		} else if(isset($forumarray[$forum['fup']]) && in_array($forum['fid'], $wf_forum_limit)) {
			foreach($_G['cache']['forums'] as $subforum) {
				if($subforum['type'] == 'sub' && $subforum['fup'] == $forum['fid']) {
					$forum['sub'][$subforum['fid']] = $subforum['fid'];
				}
			}
			$forumarray[$forum['fup']]['sub'][$forum['fid']] = $forum;
		} 
	}
	foreach($forumarray as $key=>$group) {
		if(empty($group['sub'])) {
			unset($forumarray[$key]);
		}
	}
	return $forumarray;
}

function getthreadlist($tids, $type, $fids, $sid, $havecover, $withpost, $start = 0, $num = 30) {
	require_once libfile('function/forum');
	require_once libfile('function/discuzcode');
	global $_G, $block_reply_num, $block_special_data_on, $block_attach_on, $masonry_preread;
	$query = C::t('#singcere_waterfall#dx')->fetch_all_threads($tids, $type, $fids, $sid, $havecover, $withpost, $start, $num);
	$threadlist = $tids = array();
	foreach($query as $thread) {
		$tids[] = $thread['tid'];
		if($block_special_data_on && $thread['special']) {
			$thread['sdata'] = getspecialdata($thread['tid'], $thread['special']);
		}
		$thread['coverpath'] = getthreadcover($thread['tid'], $thread['cover']);  
		if($masonry_preread) {
			list($thread['w'], $thread['h']) = getimagesize(($thread['cover'] < 0 ? $thread['coverpath'] : DISCUZ_ROOT.$_G['setting']['attachurl']).'forum/'.getthreadcover($thread['tid'], $thread['cover'], 1));
		}
		$thread['collections'] = count(explode("\t", $thread['collection'], -1));
		$thread['forumname'] = $_G['cache']['forums'][$thread['fid']]['name'];

		$thread = procthread($thread);
		$threadlist[$thread[tid]] = $thread;
	}
	if($block_reply_num > 0) {
		global $attachs;
		$replies = C::t('#singcere_waterfall#dx')->fetch_all_replies_by_tids($tids, $block_reply_num);
		foreach($replies as $reply) {
			if(count($threadlist[$reply['tid']]['replylist']) <= $block_reply_num) {
				if($block_attach_on) {
					preg_match_all("/\[attach\](\d+)\[\/attach\]/i", $reply['message'], $matchaids);
					$attachs = parseattach($reply['tid'], $reply['pid'], $matchaids, $reply['message'], $skipaids);
					foreach($attachs as $key => $attach) {
						$reply['message'] = str_replace('[attach]'.$key.'[/attach]', $attach, $reply['message']);
					}
					$reply['message'] = discuzcode($reply['message'], FALSE, FALSE);
					$reply['message'] = html2txt(($reply['message']));
					$threadlist[$reply['tid']]['replylist'][] = $reply;
				} else {
					$reply['message'] = discuzcode($reply['message'], false, false);
					$threadlist[$reply['tid']]['replylist'][] = $reply;
				}
			}
		}
	}
	return $threadlist;
}



function procthread($thread) {
	global $_G;

	$todaytime = strtotime(dgmdate(TIMESTAMP, 'Ymd'));
	$thread['lastposterenc'] = rawurlencode($thread['lastposter']);
	$thread['multipage'] = '';
	$topicposts = $thread['special'] ? $thread['replies'] : $thread['replies'] + 1;
	if($topicposts > $_G['ppp']) {
		$pagelinks = '';
		$thread['pages'] = ceil($topicposts / $_G['ppp']);
		for($i = 2; $i <= 6 && $i <= $thread['pages']; $i++) {
			$pagelinks .= "<a href=\"forum.php?mod=viewthread&tid=$thread[tid]&amp;extra=$extra&amp;page=$i\">$i</a>";
		}
		if($thread['pages'] > 6) {
			$pagelinks .= "..<a href=\"forum.php?mod=viewthread&tid=$thread[tid]&amp;extra=$extra&amp;page=$thread[pages]\">$thread[pages]</a>";
		}
		$thread['multipage'] = '&nbsp;...'.$pagelinks;
	}

	if($thread['message']) {
		preg_match_all("/((\[media=(swf|flv|x),\d{1,4}[x|\,]\d{1,4}\])\s*([^\[\<\r\n]+?)\s*\[\/media\])|((\[flash\])|(\[flash=\d{1,4}\,\d{1,4})\]\s*([^\[\<\r\n]+?)\s*\[\/flash\])/is", $thread['message'], $videoList, PREG_SET_ORDER);
		if($videoList) {
			$thread['action']['video'] = 1;
		}
		preg_match_all("/(\[audio\])\s*([^\[\<\r\n]+?)\s*\[\/audio\]/is", $thread['message'], $audiolist, PREG_SET_ORDER);
		if($audiolist) {
			$thread['action']['audio'] = $audiolist[0][2];
		}
	}

	if($thread['highlight']) {
		$string = sprintf('%02d', $thread['highlight']);
		$stylestr = sprintf('%03b', $string[0]);
		$thread['highlight'] = ' style="';
		$thread['highlight'] .= $stylestr[0] ? 'font-weight: bold;' : '';
		$thread['highlight'] .= $stylestr[1] ? 'font-style: italic;' : '';
		$thread['highlight'] .= $stylestr[2] ? 'text-decoration: underline;' : '';
		$thread['highlight'] .= $string[1] ? 'color: '.$_G['forum_colorarray'][$string[1]] : '';
		$thread['highlight'] .= '"';
	} else {
		$thread['highlight'] = '';
	}

	$thread['recommendicon'] = '';
	if(!empty($_G['setting']['recommendthread']['status']) && $thread['recommends']) {
		foreach($_G['setting']['recommendthread']['iconlevels'] as $k => $i) {
			if($thread['recommends'] > $i) {
				$thread['recommendicon'] = $k+1;
				break;
			}
		}
	}
	$thread['moved'] = $thread['heatlevel'] = $thread['new'] = 0;
	$thread['icontid'] = $thread['forumstick'] || !$thread['moved'] && $thread['isgroup'] != 1 ? $thread['tid'] : $thread['closed'];
	$thread['folder'] = 'common';
	$thread['weeknew'] = TIMESTAMP - 604800 <= $thread['dbdateline'];
	if($thread['replies'] > $thread['views']) {
		$thread['views'] = $thread['replies'];
	}
	if($_G['setting']['heatthread']['iconlevels']) {
		foreach($_G['setting']['heatthread']['iconlevels'] as $k => $i) {
			if($thread['heats'] > $i) {
				$thread['heatlevel'] = $k + 1;
				break;
			}
		}
	}
	$thread['istoday'] = $thread['dateline'] > $todaytime ? 1 : 0;
	$thread['dbdateline'] = $thread['dateline'];
	$thread['dateline'] = dgmdate($thread['dateline'], 'u', '9999', getglobal('setting/dateformat'));
	$thread['dblastpost'] = $thread['lastpost'];
	$thread['lastpost'] = dgmdate($thread['lastpost'], 'u');

	if(in_array($thread['displayorder'], array(1, 2, 3, 4))) {
		$thread['id'] = 'stickthread_'.$thread['tid'];
	} else {
		$thread['id'] = 'normalthread_'.$thread['tid'];
	}
	$thread['rushreply'] = getstatus($thread['status'], 3);
	return $thread;
}

function getspecialdata($tid, $sid) {
	if($sid == 1) {
		if($count = C::t('forum_polloption')->fetch_count_by_tid($tid)) {
			$poll = C::t('forum_poll')->fetch($tid);
			$options = C::t('forum_polloption')->fetch_all_by_tid($tid, 1);
			$colors = array('E92725', 'F27B21', 'F2A61F', '5AAF4A', '42C4F5', '0099CC', '3365AE', '2A3591', '592D8E', 'DB3191');
			return array('poll'=>$poll, 'options'=>$options, 'count'=>$count, 'colors'=>$colors);
		}
	} else if($sid == 2) {
		$trade = C::t('forum_trade')->fetch_first_goods($tid);
		if($trade['expiration']) {
			$trade['expiration'] = ($trade['expiration'] - TIMESTAMP) / 86400;
			if($trade['expiration'] > 0) {
				$trade['expirationhour'] = floor(($trade['expiration'] - floor($trade['expiration'])) * 24);
				$trade['expiration'] = floor($trade['expiration']);
			} else {
				$trade['expiration'] = -1;
			}
		}
		return $trade;
	} else if($sid == 4) {
		$activity = C::t('forum_activity')->fetch($tid);
		$activity['starttimefrom'] = dgmdate($activity['starttimefrom'], 'u');
		return $activity;
	} else if($sid == 5) {
		$debate = C::t('forum_debate')->fetch($tid);
		return $debate;
	}
}