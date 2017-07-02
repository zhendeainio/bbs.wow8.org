<?php

if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

if($_G['forum']['redirect']) {
    dheader("Location: {$_G[forum][redirect]}");
} elseif($_G['forum']['type'] == 'group') {
    dheader("Location: forum.php?gid=$_G[fid]");
} elseif(empty($_G['forum']['fid'])) {
    showmessage('forum_nonexistence', NULL);
} elseif($_G['fid'] == $_G['setting']['followforumid'] && $_G['adminid'] != 1) {
    dheader("Location: home.php?mod=follow");
}