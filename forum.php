<?php
if ($_SERVER['HTTP_HOST'] == 'lazyknight.com') {
    header("HTTP/1.1 301 Moved Permanently");
    header("location: /blog");
    return;
}
elseif ($_SERVER['HTTP_HOST'] == 'wow9.org') {
	header('HTTP/1.1 301 Moved Permanently');
	header($_SERVER['REQUEST_URI'] == '/forum.php' ? 'Location: http://bbs.wow8.org' : 'Location: http://wow9.org/center');
	exit();
}

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: forum.php 33828 2013-08-20 02:29:32Z nemohou $
 */
define('APPTYPEID', 2);
define('CURSCRIPT', 'forum');
require './source/class/class_core.php';
require './source/function/function_forum.php';
$modarray = array('ajax','announcement','attachment','forumdisplay',
	'group','image','index','medal','misc','modcp','notice','post','redirect',
	'relatekw','relatethread','rss','topicadmin','trade','viewthread','tag','collection','guide',
);
$modcachelist = array(
	'index'		=> array('announcements', 'onlinelist', 'forumlinks',
			'heats', 'historyposts', 'onlinerecord', 'userstats', 'diytemplatenameforum'),
	'forumdisplay'	=> array('smilies', 'announcements_forum', 'globalstick', 'forums',
			'onlinelist', 'forumstick', 'threadtable_info', 'threadtableids', 'stamps', 'diytemplatenameforum'),
	'viewthread'	=> array('smilies', 'smileytypes', 'forums', 'usergroups',
			'stamps', 'bbcodes', 'smilies',	'custominfo', 'groupicon', 'stamps',
			'threadtableids', 'threadtable_info', 'posttable_info', 'diytemplatenameforum'),
	'redirect'	=> array('threadtableids', 'threadtable_info', 'posttable_info'),
	'post'		=> array('bbcodes_display', 'bbcodes', 'smileycodes', 'smilies', 'smileytypes',
			'domainwhitelist', 'albumcategory'),
	'space'		=> array('fields_required', 'fields_optional', 'custominfo'),
	'group'		=> array('grouptype', 'diytemplatenamegroup'),
);
$mod = !in_array(C::app()->var['mod'], $modarray) ? 'index' : C::app()->var['mod'];
define('CURMODULE', $mod);
$cachelist = array();
if(isset($modcachelist[CURMODULE])) {
	$cachelist = $modcachelist[CURMODULE];
	$cachelist[] = 'plugin';
	$cachelist[] = 'pluginlanguage_system';
}
if(C::app()->var['mod'] == 'group') {
	$_G['basescript'] = 'group';
}
C::app()->cachelist = $cachelist;
C::app()->init();

// Support "Forum Name" to enter a forum.
if (CURMODULE == 'forumdisplay' && isset($_GET['fn']) && empty($_GET['fid'])) {
    require './source/function/function_cache.php';
	function copy_threadclasses($threadtypes, $fid) {
		global $_G;
		if($threadtypes) {
			$threadtypes = dunserialize($threadtypes);
			$i = 0;
			$data = array();
			foreach($threadtypes['types'] as $key => $val) {
				$data = array('fid' => $fid, 'name' => $val, 'displayorder' => $i++, 'icon' => $threadtypes['icons'][$key], 'moderators' => $threadtypes['moderators'][$key]);
				$newtypeid = C::t('forum_threadclass')->insert($data, true);
				$newtypes[$newtypeid] = $val;
				$newicons[$newtypeid] = $threadtypes['icons'][$key];
				$newmoderators[$newtypeid] = $threadtypes['moderators'][$key];
			}
			$threadtypes['types'] = $newtypes;
			$threadtypes['icons'] = $newicons;
			$threadtypes['moderators'] = $newmoderators;
			return serialize($threadtypes);
		}
		return '';
	}
	
    $fup = 498; // always use 498 as container
    $displayorder = 0; // always use 0 because it's doesn't matter the sort order, we use "name" or man-made index to enter.
	$forumname = iconv('utf8', 'gbk', strval(mysql_real_escape_string($_GET['fn'])));
    // -------------------------------------------
    $usergroups = array();
    $query = C::t('common_usergroup')->range();
    foreach($query as $group) $usergroups[$group['groupid']] = $group;
    $table_forum_columns = array('fup', 'type', 'name', 'status', 'displayorder', 'styleid', 'allowsmilies',
        'allowhtml', 'allowbbcode', 'allowimgcode', 'allowmediacode', 'allowanonymous', 'allowpostspecial', 'alloweditrules',
        'alloweditpost', 'modnewposts', 'recyclebin', 'jammer', 'forumcolumns', 'threadcaches', 'disablewatermark', 'disablethumb',
        'autoclose', 'simple', 'allowside', 'allowfeed');
    $table_forumfield_columns = array('fid', 'attachextensions', 'threadtypes', 'viewperm', 'postperm', 'replyperm',
        'getattachperm', 'postattachperm', 'postimageperm');
	$_GET['fid'] = C::t('forum_forum')->fetch_fid_by_name($forumname);
    if (empty($_GET['fid'])) {
        if (!$_G['uid']) {
            header("refresh:5; url=/member.php?mod=register", true);
            exit(iconv("utf8", "gbk", '版块不存在：'.$_GET['fn'].'<br/>如果你想创建一个新版块，请先注册或登陆。<br/>5秒后跳转到<a href="/member.php?mod=register">注册页面</a>。'));
        }

        $fupforum = C::t('forum_forum')->get_forum_by_fid($fup);
        if($fupforum['fup']) {
            $groupforum = C::t('forum_forum')->get_forum_by_fid($fupforum['fup']);
        } else {
            $groupforum = $fupforum;
        }

        if(empty($forumname) || strlen($forumname) > 50) exit;

        $forumfields['allowsmilies'] = 1;
        $forumfields['allowbbcode'] = 1;
        $forumfields['allowimgcode'] = 1;
        $forumfields['allowmediacode'] = 1;
        $forumfields['allowpostspecial'] = 1;
        $forumfields['allowside'] = 0;
        $forumfields['allowfeed'] = 0;
        $forumfields['recyclebin'] = 1;
        $forumfields['fup'] = $fup;
        $forumfields['type'] = $fupforum['type'] == 'forum' ? 'sub' : 'forum';
        $forumfields['styleid'] = $groupforum['styleid'];
        $forumfields['name'] = $forumname;
        $forumfields['status'] = 1;
        $forumfields['displayorder'] = $displayorder;

        $data = array();
        foreach($table_forum_columns as $field) {
            if(isset($forumfields[$field])) {
                $data[$field] = $forumfields[$field];
            }
        }
        $forumfields['fid'] = $fid = C::t('forum_forum')->insert($data, 1);
        $data = array();
        $forumfields['threadtypes'] = copy_threadclasses($forumfields['threadtypes'], $fid);
        foreach($table_forumfield_columns as $field) {
            if(isset($forumfields[$field])) {
                $data[$field] = $forumfields[$field];
            }
        }
        C::t('forum_forumfield')->insert($data);
        foreach(C::t('forum_moderator')->fetch_all_by_fid($fup, false) as $mod) {
            if($mod['inherited'] || $fupforum['inheritedmod']) {
                C::t('forum_moderator')->insert(array('uid' => $mod['uid'], 'fid' => $fid, 'inherited' => 1), false, true);
            }
        }
        updatecache('forums');
		$_GET['fid'] = C::t('forum_forum')->fetch_fid_by_name($forumname); // refresh after added
		// header("Location: /forum.php?mod=forumdisplay&fn=$forumname");
    }
}

loadforum();
set_rssauth();
runhooks();
$navtitle = str_replace('{bbname}', $_G['setting']['bbname'], $_G['setting']['seotitle']['forum']);
$_G['setting']['threadhidethreshold'] = 1;
require DISCUZ_ROOT.'./source/module/forum/forum_'.$mod.'.php';
?>