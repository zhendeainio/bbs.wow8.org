<?php if(!defined('IN_DISCUZ')) exit('Access Denied');

if($_G['forum']['password']) {
    if($_GET['action'] == 'pwverify') {
        if($_GET['pw'] != $_G['forum']['password']) {
            showmessage('forum_passwd_incorrect', NULL);
        } else {
            dsetcookie('fidpw'.$_G['fid'], $_GET['pw']);
            showmessage('forum_passwd_correct', "forum.php?mod=forumdisplay&fid=$_G[fid]");
        }
    } elseif($_G['forum']['password'] != $_G['cookie']['fidpw'.$_G['fid']]) {
        include template('forum/forumdisplay_passwd');
        exit();
    }
}