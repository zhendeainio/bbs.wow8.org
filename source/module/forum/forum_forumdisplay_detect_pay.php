<?php if(!defined('IN_DISCUZ')) exit('Access Denied');

if($_G['forum']['price'] && !$_G['forum']['ismoderator']) {
    $membercredits = C::t('common_member_forum_buylog')->get_credits($_G['uid'], $_G['fid']);
    $paycredits = $_G['forum']['price'] - $membercredits;
    if($paycredits > 0) {
        if($_GET['action'] == 'paysubmit') {
            updatemembercount($_G['uid'], array($_G['setting']['creditstransextra'][1] => -$paycredits), 1, 'FCP', $_G['fid']);
            C::t('common_member_forum_buylog')->update_credits($_G['uid'], $_G['fid'], $_G['forum']['price']);
            showmessage('forum_pay_correct', "forum.php?mod=forumdisplay&fid=$_G[fid]");
        } else {
            if(getuserprofile('extcredits'.$_G['setting']['creditstransextra'][1]) < $paycredits) {
                showmessage('forum_pay_incorrect', NULL, array('paycredits' => $paycredits, 'credits' => $_G['setting']['extcredits'][$_G['setting']['creditstransextra'][1]]['unit'].$_G['setting']['extcredits'][$_G['setting']['creditstransextra'][1]]['title'], 'title' => $_G['setting']['extcredits'][$_G['setting']['creditstransextra'][1]]['title']));
            } else {
                include template('forum/forumdisplay_pay');
                exit();
            }
        }
    }
}