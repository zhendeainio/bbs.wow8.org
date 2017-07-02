<?php if(!defined('IN_DISCUZ')) exit('Access Denied');
if(($_G['forum']['status'] != 3 && $_G['forum']['allowside'])) {
    updatesession();
    $onlinenum = C::app()->session->count_by_fid($_G['fid']);
    if(!IS_ROBOT && ($_G['setting']['whosonlinestatus'] == 2 || $_G['setting']['whosonlinestatus'] == 3)) {
        $_G['setting']['whosonlinestatus'] = 1;
        $detailstatus = $showoldetails == 'yes' || (((!isset($_G['cookie']['onlineforum']) && !$_G['setting']['whosonline_contract']) || $_G['cookie']['onlineforum']) && !$showoldetails);

        if($detailstatus) {
            $actioncode = lang('forum/action');
            $whosonline = array();
            $forumname = strip_tags($_G['forum']['name']);

            $whosonline = C::app()->session->fetch_all_by_fid($_G['fid'], 12);
            $_G['setting']['whosonlinestatus'] = 1;
        }
    } else {
        $_G['setting']['whosonlinestatus'] = 0;
    }
}