<?php if(!defined('IN_DISCUZ')) exit('Access Denied');

$forum_up = $_G['cache']['forums'][$_G['forum']['fup']];
if($_G['forum']['type'] == 'forum') {
    $fgroupid = $_G['forum']['fup'];
    if(empty($_GET['archiveid'])) {
        $navigation = ' <em>&rsaquo;</em> <a href="forum.php?gid='.$forum_up['fid'].'">'.$forum_up['name'].'</a><em>&rsaquo;</em> <a href="forum.php?mod=forumdisplay&fid='.$_G['forum']['fid'].'">'.$_G['forum']['name'].'</a>';
    } else {
        $navigation = ' <em>&rsaquo;</em> '.'<a href="forum.php?mod=forumdisplay&fid='.$_G['fid'].'">'.$_G['forum']['name'].'</a> <em>&rsaquo;</em> '.$forumarchive[$_GET['archiveid']]['displayname'];
    }
    $seodata = array('forum' => $_G['forum']['name'], 'fgroup' => $forum_up['name'], 'page' => intval($_GET['page']));
} else {
    $fgroupid = $forum_up['fup'];
    if(empty($_GET['archiveid'])) {
        $forum_top =  $_G['cache']['forums'][$forum_up[fup]];
        $navigation = ' <em>&rsaquo;</em> <a href="forum.php?gid='.$forum_top['fid'].'">'.$forum_top['name'].'</a><em>&rsaquo;</em> <a href="forum.php?mod=forumdisplay&fid='.$forum_up['fid'].'">'.$forum_up['name'].'</a><em>&rsaquo;</em> '.$_G['forum']['name'];
    } else {
        $navigation = ' <em>&rsaquo;</em> <a href="forum.php?mod=forumdisplay&fid='.$_G['forum']['fup'].'">'.$forum_up['name'].'</a> <em>&rsaquo;</em> '.'<a href="forum.php?mod=forumdisplay&fid='.$_G['fid'].'">'.$_G['forum']['name'].'</a> <em>&rsaquo;</em> '.$forumarchive[$_GET['archiveid']]['displayname'];
    }
    $seodata = array('forum' => $_G['forum']['name'], 'fup' => $forum_up['name'], 'fgroup' => $forum_top['name'], 'page' => intval($_GET['page']));
}