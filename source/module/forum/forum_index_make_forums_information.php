<?php if(!defined('IN_DISCUZ')) exit('Access Denied');

/**
 * 备用，这个功能可以查询空版块的下级版块中的主题内容并列出（不完善）
 * @param $fid
 * @param $is_parent
 * @return array
 */
function func_get_last_post_threads($fid, $is_parent) {
    $list = C::t('forum_thread')->fetch_all_by_fid_lastpost($fid);
    if ($is_parent && count($list) == 0) {
        $temp_subforums = C::t('forum_forum')->fetch_all_sub_group_by_fup($fid);
        foreach ($temp_subforums as $temp_subforum) {
            $list = array_merge($list, func_get_last_post_threads($temp_subforum['fid'], false));
        }
        $flag_lastpost = [];
        foreach ($list as $item) {
            $flag_lastpost[] = $item['lastpost'];
        }
        array_multisort($flag_lastpost, SORT_ASC, $list); // sort: http://bbs.csdn.net/topics/391018701
    }
    return $list;
}

require_once "forum_index_make_forums_information__make_last_posts.php";

if(!$gid && (!defined('FORUM_INDEX_PAGE_MEMORY') || !FORUM_INDEX_PAGE_MEMORY)) {
    $announcements = get_index_announcements();

    $forums = C::t('forum_forum')->fetch_all_by_status(1);
    $fids = array();
    foreach($forums as $forum) {
        $fids[$forum['fid']] = $forum['fid'];
    }

    $forum_access = array();
    if(!empty($_G['member']['accessmasks'])) {
        $forum_access = C::t('forum_access')->fetch_all_by_fid_uid($fids, $_G['uid']);
    }

    $forum_fields = C::t('forum_forumfield')->fetch_all($fids);

    foreach($forums as $forum) {
        if($forum_fields[$forum['fid']]['fid']) {
            $forum = array_merge($forum, $forum_fields[$forum['fid']]);
        }
        if($forum_access['fid']) {
            $forum = array_merge($forum, $forum_access[$forum['fid']]);
        }
        $forumname[$forum['fid']] = strip_tags($forum['name']);
        $forum['extra'] = empty($forum['extra']) ? array() : dunserialize($forum['extra']);
        if(!is_array($forum['extra'])) {
            $forum['extra'] = array();
        }

        if($forum['type'] != 'group') {

            $threads += $forum['threads'];
            $posts += $forum['posts'];
            $todayposts += $forum['todayposts'];

            $forum['threadlist'] = C::t('forum_thread')->fetch_all_by_fid_lastpost($forum['fid']);

            if($forum['type'] == 'forum' && isset($catlist[$forum['fup']])) {
                if(forum($forum)) {
                    $catlist[$forum['fup']]['forums'][] = $forum['fid'];
                    $forum['orderid'] = $catlist[$forum['fup']]['forumscount']++;
                    $forum['subforums'] = '';
                    $forumlist[$forum['fid']] = $forum;
                }

            } elseif(isset($forumlist[$forum['fup']])) {

                $forumlist[$forum['fup']]['threads'] += $forum['threads'];
                $forumlist[$forum['fup']]['posts'] += $forum['posts'];
                $forumlist[$forum['fup']]['todayposts'] += $forum['todayposts'];
                if($_G['setting']['subforumsindex'] && $forumlist[$forum['fup']]['permission'] == 2 && !($forumlist[$forum['fup']]['simple'] & 16) || ($forumlist[$forum['fup']]['simple'] & 8)) {
                    $forumurl = !empty($forum['domain']) && !empty($_G['setting']['domain']['root']['forum']) ? 'http://'.$forum['domain'].'.'.$_G['setting']['domain']['root']['forum'] : 'forum.php?mod=forumdisplay&fid='.$forum['fid'];
                    $forumlist[$forum['fup']]['subforums'] .= (empty($forumlist[$forum['fup']]['subforums']) ? '' : ', ').'<a href="'.$forumurl.'" '.(!empty($forum['extra']['namecolor']) ? ' style="color: ' . $forum['extra']['namecolor'].';"' : '') . '>'.$forum['name'].'</a>';
                }
            }

        } else {

            if($forum['moderators']) {
                $forum['moderators'] = moddisplay($forum['moderators'], 'flat');
            }
            $forum['forumscount'] 	= 0;
            $catlist[$forum['fid']] = $forum;

        }
    }
    unset($forum_access, $forum_fields);

    foreach($catlist as $catid => $category) {
        $catlist[$catid]['collapseimg'] = 'collapsed_no.gif';
        if($catlist[$catid]['forumscount'] && $category['forumcolumns']) {
            $catlist[$catid]['forumcolwidth'] = (floor(100 / $category['forumcolumns']) - 0.1).'%';
            $catlist[$catid]['endrows'] = '';
            if($colspan = $category['forumscount'] % $category['forumcolumns']) {
                while(($category['forumcolumns'] - $colspan) > 0) {
                    $catlist[$catid]['endrows'] .= '<td width="'.$catlist[$catid]['forumcolwidth'].'">&nbsp;</td>';
                    $colspan ++;
                }
                $catlist[$catid]['endrows'] .= '</tr>';
            }
        } elseif(empty($category['forumscount'])) {
            unset($catlist[$catid]);
        }
    }
    unset($catid, $category);

    if(isset($catlist[0]) && $catlist[0]['forumscount']) {
        $catlist[0]['fid'] = 0;
        $catlist[0]['type'] = 'group';
        $catlist[0]['name'] = $_G['setting']['bbname'];
        $catlist[0]['collapseimg'] = 'collapsed_no.gif';
    } else {
        unset($catlist[0]);
    }

    if(!IS_ROBOT && ($_G['setting']['whosonlinestatus'] == 1 || $_G['setting']['whosonlinestatus'] == 3)) {
        $_G['setting']['whosonlinestatus'] = 1;

        $onlineinfo = explode("\t", $_G['cache']['onlinerecord']);
        if(empty($_G['cookie']['onlineusernum'])) {
            $onlinenum = C::app()->session->count();
            if($onlinenum > $onlineinfo[0]) {
                $onlinerecord = "$onlinenum\t".TIMESTAMP;
                C::t('common_setting')->update('onlinerecord', $onlinerecord);
                savecache('onlinerecord', $onlinerecord);
                $onlineinfo = array($onlinenum, TIMESTAMP);
            }
            dsetcookie('onlineusernum', intval($onlinenum), 300);
        } else {
            $onlinenum = intval($_G['cookie']['onlineusernum']);
        }
        $onlineinfo[1] = dgmdate($onlineinfo[1], 'd');

        $detailstatus = $showoldetails == 'yes' || (((!isset($_G['cookie']['onlineindex']) && !$_G['setting']['whosonline_contract']) || $_G['cookie']['onlineindex']) && $onlinenum < 500 && !$showoldetails);

        $guestcount = $membercount = 0;
        if(!empty($_G['setting']['sessionclose'])) {
            $detailstatus = false;
            $membercount = C::app()->session->count(1);
            $guestcount = $onlinenum - $membercount;
        }

        if($detailstatus) {
            $actioncode = lang('action');

            $_G['uid'] && updatesession();
            $whosonline = array();

            $_G['setting']['maxonlinelist'] = $_G['setting']['maxonlinelist'] ? $_G['setting']['maxonlinelist'] : 500;
            foreach(C::app()->session->fetch_member(1, 0, $_G['setting']['maxonlinelist']) as $online){
                $membercount ++;
                if($online['invisible']) {
                    $invisiblecount++;
                    continue;
                } else {
                    $online['icon'] = !empty($_G['cache']['onlinelist'][$online['groupid']]) ? $_G['cache']['onlinelist'][$online['groupid']] : $_G['cache']['onlinelist'][0];
                }
                $online['lastactivity'] = dgmdate($online['lastactivity'], 't');
                $whosonline[] = $online;
            }
            if(isset($_G['cache']['onlinelist'][7]) && $_G['setting']['maxonlinelist'] > $membercount) {
                foreach(C::app()->session->fetch_member(2, 0, $_G['setting']['maxonlinelist'] - $membercount) as $online){
                    $online['icon'] = $_G['cache']['onlinelist'][7];
                    $online['username'] = $_G['cache']['onlinelist']['guest'];
                    $online['lastactivity'] = dgmdate($online['lastactivity'], 't');
                    $whosonline[] = $online;
                }
            }
            unset($actioncode, $online);

            if($onlinenum > $_G['setting']['maxonlinelist']) {
                $membercount = C::app()->session->count(1);
                $invisiblecount = C::app()->session->count_invisible();
            }

            if($onlinenum < $membercount) {
                $onlinenum = C::app()->session->count();
                dsetcookie('onlineusernum', intval($onlinenum), 300);
            }

            $invisiblecount = intval($invisiblecount);
            $guestcount = $onlinenum - $membercount;

            unset($online);
        }

    } else {
        $_G['setting']['whosonlinestatus'] = 0;
    }

    if(defined('FORUM_INDEX_PAGE_MEMORY') && !FORUM_INDEX_PAGE_MEMORY) {
        $key = !IS_ROBOT ? $_G['member']['groupid'] : 'for_robot';
        memory('set', 'forum_index_page_'.$key, array(
            'catlist' => $catlist,
            'forumlist' => $forumlist,
            'sublist' => $sublist,
            'whosonline' => $whosonline,
            'onlinenum' => $onlinenum,
            'membercount' => $membercount,
            'guestcount' => $guestcount,
            'grids' => $grids,
            'announcements' => $announcements,
            'threads' => $threads,
            'posts' => $posts,
            'todayposts' => $todayposts,
            'onlineinfo' => $onlineinfo,
            'announcepm' => $announcepm), getglobal('setting/memory/forumindex')
        );
    }
} else {
    require_once DISCUZ_ROOT.'./source/include/misc/misc_category.php';
}