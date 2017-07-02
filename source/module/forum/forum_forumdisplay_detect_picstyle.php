<?php if(!defined('IN_DISCUZ')) exit('Access Denied');
if($_G['forum']['picstyle']) {
    $forumdefstyle = isset($_GET['forumdefstyle']) ? $_GET['forumdefstyle'] : '';
    if($forumdefstyle) {
        switch($forumdefstyle) {
            case 'no': dsetcookie('forumdefstyle', ''); break;
            case 'yes': dsetcookie('forumdefstyle', 1, 31536000); break;
        }
    }
    if(empty($_G['cookie']['forumdefstyle'])) {
        if(!empty($_G['setting']['forumpicstyle']['thumbnum'])) {
            $_G['tpp'] = $_G['setting']['forumpicstyle']['thumbnum'];
        }
        $stickycount = $showsticky = 0;
    }
}