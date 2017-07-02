<?php if(!defined('IN_DISCUZ')) exit('Access Denied');
$showsticky = !defined('MOBILE_HIDE_STICKY') || !MOBILE_HIDE_STICKY;
if($showsticky) {
    $forumstickytids = array();
    if($_G['page'] !== 1 || $filterbool === false) {
        if($_G['setting']['globalstick'] && $_G['forum']['allowglobalstick']) {
            $stickytids = explode(',', str_replace("'", '', $_G['cache']['globalstick']['global']['tids']));
            if(!empty($_G['cache']['globalstick']['categories'][$thisgid]['count'])) {
                $stickytids = array_merge($stickytids, explode(',', str_replace("'", '', $_G['cache']['globalstick']['categories'][$thisgid]['tids'])));
            }

            if($_G['forum']['status'] != 3) {
                $stickycount = $_G['cache']['globalstick']['global']['count'];
                if(!empty($_G['cache']['globalstick']['categories'][$thisgid])) {
                    $stickycount += $_G['cache']['globalstick']['categories'][$thisgid]['count'];
                }
            }
        }

        if($_G['forum']['allowglobalstick']) {
            $forumstickycount = 0;
            $forumstickfid = $_G['forum']['status'] != 3 ? $_G['fid'] : $_G['forum']['fup'];
            if(isset($_G['cache']['forumstick'][$forumstickfid])) {
                $forumstickycount = count($_G['cache']['forumstick'][$forumstickfid]);
                $forumstickytids = $_G['cache']['forumstick'][$forumstickfid];
            }
            if(!empty($forumstickytids)) {
                $stickytids = array_merge($stickytids, $forumstickytids);
            }
            $stickycount += $forumstickycount;
        }
    }
}