<?php if(!defined('IN_DISCUZ')) exit('Access Denied');

$forumseoset = array(
    'seotitle' => $_G['forum']['seotitle'],
    'seokeywords' => $_G['forum']['keywords'],
    'seodescription' => $_G['forum']['seodescription']
);
$seotype = 'threadlist';
if($_G['forum']['status'] == 3) {
    $navtitle = helper_seo::get_title_page($_G['forum']['name'], $_G['page']).' - '.$_G['setting']['navs'][3]['navname'];
    $metakeywords = $_G['forum']['metakeywords'];
    $metadescription = $_G['forum']['description'];
    if($_G['forum']['level'] == -1) {
        showmessage('group_verify', '', array(), array('alert' => 'info'));
    }
    $_G['seokeywords'] = $_G['setting']['seokeywords']['group'];
    $_G['seodescription'] = $_G['setting']['seodescription']['group'];
    $action = getgpc('action') ? $_GET['action'] : 'list';
    require_once libfile('function/group');
    $status = groupperm($_G['forum'], $_G['uid']);
    if($status == -1) {
        showmessage('forum_not_group', 'group.php');
    } elseif($status == 1) {
        showmessage('forum_group_status_off');
    } elseif($status == 2) {
        showmessage('forum_group_noallowed', 'forum.php?mod=group&fid='.$_G['fid']);
    } elseif($status == 3) {
        showmessage('forum_group_moderated', 'forum.php?mod=group&fid='.$_G['fid']);
    }
    $_G['forum']['icon'] = get_groupimg($_G['forum']['icon'], 'icon');
    $_G['grouptypeid'] = $_G['forum']['fup'];
    $_G['forum']['dateline'] = dgmdate($_G['forum']['dateline'], 'd');

    $nav = get_groupnav($_G['forum']);
    $groupnav = $nav['nav'];
    $onlinemember = grouponline($_G['fid']);
    $groupmanagers = $_G['forum']['moderators'];
    $groupcache = getgroupcache($_G['fid'], array('replies', 'views', 'digest', 'lastpost', 'ranking', 'activityuser', 'newuserlist'));
    $seotype = 'grouppage';
    $seodata['first'] = $nav['first']['name'];
    $seodata['second'] = $nav['second']['name'];
    $seodata['gdes'] = $_G['forum']['description'];
    $forumseoset = array();
}
$_G['forum']['banner'] = get_forumimg($_G['forum']['banner']);
list($navtitle, $metadescription, $metakeywords) = get_seosetting($seotype, $seodata, $forumseoset);

if(!$navtitle) {
    $navtitle = helper_seo::get_title_page($_G['forum']['name'], $_G['page']);
    $nobbname = false;
} else {
    $nobbname = true;
}
$_GET['typeid'] = intval($_GET['typeid']);
if(!empty($_GET['typeid']) && !empty($_G['forum']['threadtypes']['types'][$_GET['typeid']])) {
    $navtitle = strip_tags($_G['forum']['threadtypes']['types'][$_GET['typeid']]).' - '.$navtitle;
}
if(!$metakeywords) {
    $metakeywords = $_G['forum']['name'];
}
if(!$metadescription) {
    $metadescription = $_G['forum']['name'];
}
if($_G['forum']['viewperm'] && !forumperm($_G['forum']['viewperm']) && !$_G['forum']['allowview']) {
    showmessagenoperm('viewperm', $_G['fid'], $_G['forum']['formulaperm']);
} elseif($_G['forum']['formulaperm']) {
    formulaperm($_G['forum']['formulaperm']);
}