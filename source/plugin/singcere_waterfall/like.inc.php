<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
global $_G;


if(empty($_G['uid'])) {
	showmessage('login_before_enter_home', null, array(), array('showmsg' => true, 'login' => 1));
}

$tid = intval($_GET['tid']);
$thread = C::t('forum_thread')->fetch($tid);
	
dsetcookie('discuz_recommend', '', -1, 0);

if(!$_G['setting']['recommendthread']['status'] || !$_G['group']['allowrecommend']) {
	if($_GET['ajax'])
		output_json(array('err'=>1,'msg'=>diconv("没有权限评分",CHARSET,'utf-8')));
	showmessage('no_privilege_recommend');
}

if($thread['authorid'] == $_G['uid'] && !$_G['setting']['recommendthread']['ownthread']) {
	
	if($_GET['ajax'])
		output_json(array('err'=>1,'msg'=>diconv("不能对自己的帖子评分",CHARSET,'utf-8')));
	
	showmessage('recommend_self_disallow', '', array('recommendc' => $thread['recommends']), array('closetime' => 1, 'showdialog' => 1));
}

if(C::t('forum_memberrecommend')->fetch_by_recommenduid_tid($_G['uid'], $tid)) {
	if($_GET['ajax'])
		output_json(array('err'=>1,'msg'=>diconv("已经评分过了",CHARSET,'utf-8')));
	showmessage('recommend_duplicate', '', array('recommendc' => $thread['recommends']), array('alert' => 'info', 'closetime' => 1, 'showdialog' => 1));
}

$recommendcount = C::t('forum_memberrecommend')->count_by_recommenduid_dateline($_G['uid'], $_G['timestamp']-86400);
if($_G['setting']['recommendthread']['daycount'] && $recommendcount >= $_G['setting']['recommendthread']['daycount']) {
		if($_GET['ajax'])
			output_json(array('err'=>1,'msg'=>diconv("已经评分次超过",CHARSET,'utf-8')));
	showmessage('recommend_outoftimes', '', array('recommendc' => $thread['recommends']), array('closetime' => 1, 'showdialog' => 1));
}

$_G['group']['allowrecommend'] = intval($_GET['do'] == 'add' ? $_G['group']['allowrecommend'] : -$_G['group']['allowrecommend']);
$fieldarr = array();
if($_GET['do'] == 'add') {
	$heatadd = 'recommend_add=recommend_add+1';
	$fieldarr['recommend_add'] = 1;
} else {
	$heatadd = 'recommend_sub=recommend_sub+1';
	$fieldarr['recommend_sub'] = 1;
}
require_once './source/function/function_forum.php';
update_threadpartake($tid);
$fieldarr['heats'] = 0;
$fieldarr['recommends'] = $_G['group']['allowrecommend'];
C::t('forum_thread')->increase($tid, $fieldarr);
C::t('forum_memberrecommend')->insert(array('tid'=>$tid, 'recommenduid'=>$_G['uid'], 'dateline'=>$_G['timestamp']));

dsetcookie('recommend', 1, 43200);
$recommendv = $_G['group']['allowrecommend'] > 0 ? '+'.$_G['group']['allowrecommend'] : $_G['group']['allowrecommend'];
if($_G['setting']['recommendthread']['daycount']) {
	$daycount = $_G['setting']['recommendthread']['daycount'] - $recommendcount;
			if($_GET['ajax'])
			output_json(array('err'=>0,'msg'=>''));
	showmessage('recommend_daycount_succed', '', array('recommendv' => $recommendv, 'recommendc' => $thread['recommends'], 'daycount' => $daycount), array('alert' => 'right', 'closetime' => 1, 'showdialog' => 1, 'extrajs'=>'<script>updatetotalnum(\''.$_GET['tid'].'\', 0, 0, 1, 0);</script>'));
} else {
		if($_GET['ajax'])
			output_json(array('err'=>0,'msg'=>''));
	showmessage('recommend_succed', '', array('recommendv' => $recommendv, 'recommendc' => $thread['recommends']), array('alert' => 'right', 'closetime' => 1, 'showdialog' => 1, 'extrajs'=>'<script>updatetotalnum(\''.$_GET['tid'].'\', 0, 0, 1, 0);</script>')); 
}

function output_json($data) {
    echo json_encode($data);
    exit;
}