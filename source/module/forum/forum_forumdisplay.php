<?php if(!defined('IN_DISCUZ')) exit('Access Denied');

require_once libfile('function/forumlist');
require_once 'forum_forumdisplay_functions.php';
require_once 'forum_forumdisplay_make_redirect.php';
require_once 'forum_forumdisplay_analy_get.php';
require_once 'forum_forumdisplay_detect_showdetails.php';
require_once 'forum_forumdisplay_detect_cookie.php';
require_once 'forum_forumdisplay_detect_forum_name.php';
require_once 'forum_forumdisplay_detect_thread_table.php';
require_once 'forum_forumdisplay_make_navgation_and_seo_data.php';
require_once 'forum_forumdisplay_make_rss.php';
require_once 'forum_forumdisplay_make_set.php';
require_once 'forum_forumdisplay_detect_password.php';
require_once 'forum_forumdisplay_detect_pay.php';
require_once 'forum_forumdisplay_detect_forum_rules.php';
require_once 'forum_forumdisplay_detect_last_visit.php';
$threadtableids = !empty($_G['cache']['threadtableids']) ? $_G['cache']['threadtableids'] : array();
$tableid = $_GET['archiveid'] && in_array($_GET['archiveid'], $threadtableids) ? intval($_GET['archiveid']) : 0;
if($_G['setting']['allowmoderatingthread'] && $_G['uid']) $threadmodcount = C::t('forum_thread')->count_by_fid_displayorder_authorid($_G['fid'], -2, $_G['uid'], $tableid);
$optionadd = $filterurladd = $searchsorton = '';
require_once 'forum_forumdisplay_make_quick_search_list.php';
$_GET['sortid'] = intval($_GET['sortid']);
$moderatedby = $_G['forum']['status'] != 3 ? moddisplay($_G['forum']['moderators'], 'forumdisplay') : '';
$_GET['highlight'] = empty($_GET['highlight']) ? '' : dhtmlspecialchars($_GET['highlight']);
if($_G['forum']['autoclose']) {
    $closedby = $_G['forum']['autoclose'] > 0 ? 'dateline' : 'lastpost';
    $_G['forum']['autoclose'] = abs($_G['forum']['autoclose']) * 86400;
}
require_once 'forum_forumdisplay_detect_sub_exists.php';
$page = $_G['page'];
$subforumonly = $_G['forum']['simple'] & 1;
$simplestyle = !$_G['forum']['allowside'] || $page > 1 ? true : false;
if($subforumonly) {
    $_G['setting']['fastpost'] = false;
    $_GET['orderby'] = '';
    if(!defined('IN_ARCHIVER')) {
        include template('diy:forum/forumdisplay:'.$_G['fid']);
    } else {
        include loadarchiver('forum/forumdisplay');
    }
    exit();
}
if($_GET['filter'] != 'hot') {
    $page = $_G['setting']['threadmaxpages'] && $page > $_G['setting']['threadmaxpages'] ? 1 : $page;
}
if($_G['forum']['modrecommend'] && $_G['forum']['modrecommend']['open']) {
    $_G['forum']['recommendlist'] = recommendupdate($_G['fid'], $_G['forum']['modrecommend'], '', 1);
}
require_once 'forum_forumdisplay_detect_recommend_groups.php';
require_once 'forum_forumdisplay_detect_announcement.php';
require_once 'forum_forumdisplay_detect_filter.php';
require_once 'forum_forumdisplay_detect_orderby.php';
require_once 'forum_forumdisplay_detect_side_status.php';
require_once 'forum_forumdisplay_detect_thread_sorts.php';
if(isset($_GET['searchoption'])) $_GET['searchoption'] = dhtmlspecialchars($_GET['searchoption']);
require_once 'forum_forumdisplay_detect_related_group.php';
$thisgid = $_G['forum']['type'] == 'forum' ? $_G['forum']['fup'] : (!empty($_G['cache']['forums'][$_G['forum']['fup']]['fup']) ? $_G['cache']['forums'][$_G['forum']['fup']]['fup'] : 0);
$forumstickycount = $stickycount = 0;
$stickytids = '';
require_once 'forum_forumdisplay_detect_show_sticky.php';
require_once 'forum_forumdisplay_detect_picstyle.php';
if($filter != 'hot' && @ceil($_G['forum_threadcount']/$_G['tpp']) < $page) $page = 1;
$start_limit = ($page - 1) * $_G['tpp'];
require_once 'forum_forumdisplay_make_page_information.php';
$_G['setting']['visitedforums'] = $_G['setting']['visitedforums'] && $_G['forum']['status'] != 3 ? visitedforums() : '';
$_G['group']['allowpost'] = (!$_G['forum']['postperm'] && $_G['group']['allowpost']) || ($_G['forum']['postperm'] && forumperm($_G['forum']['postperm'])) || (isset($_G['forum']['allowpost']) && $_G['forum']['allowpost'] == 1 && $_G['group']['allowpost']);
$fastpost = $_G['setting']['fastpost'] && !$_G['forum']['allowspecialonly'] && !$_G['forum']['threadsorts']['required'] && !$_G['forum']['picstyle'];
$allowfastpost = $fastpost && $_G['group']['allowpost'];
$_G['group']['allowpost'] = isset($_G['forum']['allowpost']) && $_G['forum']['allowpost'] == -1 ?  false : $_G['group']['allowpost'];
$_G['forum']['allowpostattach'] = isset($_G['forum']['allowpostattach']) ? $_G['forum']['allowpostattach'] : '';
$allowpostattach = $fastpost && ($_G['forum']['allowpostattach'] != -1 && ($_G['forum']['allowpostattach'] == 1 || (!$_G['forum']['postattachperm'] && $_G['group']['allowpostattach']) || ($_G['forum']['postattachperm'] && forumperm($_G['forum']['postattachperm']))));
if($fastpost || $livethread) {
    if(!$_G['adminid'] && (!cknewuser(1) || $_G['setting']['newbiespan'] && (!getuserprofile('lastpost') || TIMESTAMP - getuserprofile('lastpost') < $_G['setting']['newbiespan'] * 60) && TIMESTAMP - $_G['member']['regdate'] < $_G['setting']['newbiespan'] * 60)) {
        $allowfastpost = false;
    }
    $usesigcheck = $_G['uid'] && $_G['group']['maxsigsize'];
    list($seccodecheck, $secqaacheck) = seccheck('post', 'newthread');
} elseif(!$_G['uid']) {
    $fastpostdisabled = true;
}
$showpoll = $showtrade = $showreward = $showactivity = $showdebate = 0;
if($_G['forum']['allowpostspecial']) {
    $showpoll = $_G['forum']['allowpostspecial'] & 1;
    $showtrade = $_G['forum']['allowpostspecial'] & 2;
    $showreward = isset($_G['setting']['extcredits'][$_G['setting']['creditstransextra'][2]]) && ($_G['forum']['allowpostspecial'] & 4);
    $showactivity = $_G['forum']['allowpostspecial'] & 8;
    $showdebate = $_G['forum']['allowpostspecial'] & 16;
}
if($_G['group']['allowpost']) {
    $_G['group']['allowpostpoll'] = $_G['group']['allowpostpoll'] && $showpoll;
    $_G['group']['allowposttrade'] = $_G['group']['allowposttrade'] && $showtrade;
    $_G['group']['allowpostreward'] = $_G['group']['allowpostreward'] && $showreward;
    $_G['group']['allowpostactivity'] = $_G['group']['allowpostactivity'] && $showactivity;
    $_G['group']['allowpostdebate'] = $_G['group']['allowpostdebate'] && $showdebate;
}
$showthreadclasscount = array();
if(($_G['forum']['threadtypes'] && $_G['forum']['threadtypes']['listable']) || count($_G['forum']['threadsorts']['types']) > 0) {
    $showthreadclasscount = threadclasscount($_G['fid']);
}
$_G['forum']['threadplugin'] = $_G['group']['allowpost'] && $_G['setting']['threadplugins'] ? dunserialize($_G['forum']['threadplugin']) : array();
$allowleftside = !$subforumonly && $_G['setting']['leftsidewidth'] && !$_G['forum']['allowside'];
if(isset($_GET['leftsidestatus'])) {
    dsetcookie('disableleftside', $_GET['leftsidestatus'], 2592000);
    $_G['cookie']['disableleftside'] = $_GET['leftsidestatus'];
}
$leftside = empty($_G['cookie']['disableleftside']) && $allowleftside ? forumleftside() : array();
$leftsideswitch = $allowleftside ? "forum.php?mod=forumdisplay&fid=$_G[fid]&page=$page".($multiadd ? '&'.implode('&', $multiadd) : '') : '';
require_once libfile('function/upload');
$swfconfig = getuploadconfig($_G['uid'], $_G['fid']);
$template = 'diy:forum/forumdisplay:'.$_G['fid'];
if($_G['forum']['status'] == 3) {
    $groupviewed_list = get_viewedgroup();
    write_groupviewed($_G['fid']);
    $template = 'diy:group/group:'.$_G['fid'];
}
require_once 'forum_forumdisplay_detect_archiver.php';