<?php if(!defined('IN_DISCUZ')) exit('Access Denied');

$forumdisplayadd['page'] = !empty($forumdisplayadd['page']) ? $forumdisplayadd['page'] : '';
$multipage_archive = $_GET['archiveid'] && in_array($_GET['archiveid'], $threadtableids) ? "&archiveid={$_GET['archiveid']}" : '';
$multipage = multi($_G['forum_threadcount'], $_G['tpp'], $page, "forum.php?mod=forumdisplay&fid=$_G[fid]".$forumdisplayadd['page'].($multiadd ? '&'.implode('&', $multiadd) : '')."$multipage_archive", $_G['setting']['threadmaxpages']);

$realpages = @ceil($_G['forum_threadcount']/$_G['tpp']);
$maxpage = ($_G['setting']['threadmaxpages'] && $_G['setting']['threadmaxpages'] < $realpages) ? $_G['setting']['threadmaxpages'] : $realpages;
$nextpage = ($page + 1) > $maxpage ? 1 : ($page + 1);
$multipage_more = "forum.php?mod=forumdisplay&fid=$_G[fid]".$forumdisplayadd['page'].($multiadd ? '&'.implode('&', $multiadd) : '')."$multipage_archive".'&page='.$nextpage;

$extra = rawurlencode(!IS_ROBOT ? 'page='.$page.($forumdisplayadd['page'] ? '&filter='.$filter.$forumdisplayadd['page'] : '') : 'page=1');

$separatepos = 0;
$_G['forum_threadlist'] = $threadids = array();
$_G['forum_colorarray'] = array('', '#EE1B2E', '#EE5023', '#996600', '#3C9D40', '#2897C5', '#2B65B7', '#8F2A90', '#EC1282');

$filterarr['sticky'] = 4;
$filterarr['displayorder'] = !$filterbool && $stickycount ? array(0, 1) : array(0, 1, 2, 3, 4);
$threadlist = array();
if($filter !== 'hot') {
    $indexadd = '';
    $_order = "displayorder DESC, $_GET[orderby] $_GET[ascdesc]";
    if($filterbool) {
        if($filterarr['digest']) {
            $indexadd = " FORCE INDEX (digest) ";
        }
    } elseif($showsticky && is_array($stickytids) && $stickytids[0]) {
        $filterarr1 = $filterarr;
        $filterarr1['inforum'] = '';
        $filterarr1['intids'] = $stickytids;
        $filterarr1['displayorder'] = array(2, 3, 4);
        $threadlist = C::t('forum_thread')->fetch_all_search($filterarr1, $tableid, $start_limit, $_G['tpp'], $_order, '');
        unset($filterarr1);
    }
    $threadlist = array_merge($threadlist, C::t('forum_thread')->fetch_all_search($filterarr, $tableid, $start_limit, $_G['tpp'], $_order, '', $indexadd));
    unset($_order);

    if(empty($threadlist) && $page <= ceil($_G['forum_threadcount'] / $_G['tpp'])) {
        require_once libfile('function/post');
        updateforumcount($_G['fid']);
    }
} else {
    $hottime = dintval(str_replace('-', '', $_GET['time']));
    $multipage = '';
    if($hottime && checkdate(substr($hottime, 4, 2), substr($hottime, 6, 2), substr($hottime, 0, 4))) {
        $calendartime = abs($hottime);
        $ctime = sprintf('%04d', substr($hottime, 0, 4)).'-'.sprintf('%02d', substr($hottime, 4, 2)).'-'.sprintf('%02d', substr($hottime, 6, 2));
    } else {
        $calendartime = dgmdate(strtotime(dgmdate(TIMESTAMP, 'Y-m-d')) - 86400, 'Ymd');
        $ctime = dgmdate(strtotime(dgmdate(TIMESTAMP, 'Y-m-d')) - 86400, 'Y-m-d');
    }
    $caldata = C::t('forum_threadcalendar')->fetch_by_fid_dateline($_G['fid'], $calendartime);
    $_G['forum_threadcount'] = 0;
    if($caldata) {
        $hottids = C::t('forum_threadhot')->fetch_all_tid_by_cid($caldata['cid']);
        $threadlist = C::t('forum_thread')->fetch_all_by_tid($hottids);
        $_G['forum_threadcount'] = count($threadlist);
    }
}

$_G['ppp'] = $_G['forum']['threadcaches'] && !$_G['uid'] ? $_G['setting']['postperpage'] : $_G['ppp'];
$page = $_G['page'];
$todaytime = strtotime(dgmdate(TIMESTAMP, 'Ymd'));

$verify = $verifyuids = $authorids = $grouptids = $rushtids = array();

$thide = !empty($_G['cookie']['thide']) ? explode('|', $_G['cookie']['thide']) : array();
$_G['showrows'] = $_G['hiddenexists'] = 0;

$threadindex = 0;
foreach($threadlist as $thread) {
    $thread['allreplies'] = $thread['replies'] + $thread['comments'];
    $thread['ordertype'] = getstatus($thread['status'], 4);
    if($_G['forum']['picstyle'] && empty($_G['cookie']['forumdefstyle'])) {
        if($thread['fid'] != $_G['fid'] && empty($thread['cover'])) {
            continue;
        }
        $thread['coverpath'] = getthreadcover($thread['tid'], $thread['cover']);
        $thread['cover'] = abs($thread['cover']);
    }
    $thread['forumstick'] = in_array($thread['tid'], $forumstickytids);
    $thread['related_group'] = 0;
    if($_G['forum']['relatedgroup'] && $thread['fid'] != $_G['fid']) {
        if($thread['closed'] > 1) continue;
        $thread['related_group'] = 1;
        $grouptids[] = $thread['tid'];
    }
    $thread['lastposterenc'] = rawurlencode($thread['lastposter']);
    if($thread['typeid'] && !empty($_G['forum']['threadtypes']['prefix']) && isset($_G['forum']['threadtypes']['types'][$thread['typeid']])) {
        if($_G['forum']['threadtypes']['prefix'] == 1) {
            $thread['typehtml'] = '<em>[<a href="forum.php?mod=forumdisplay&fid='.$_G['fid'].'&amp;filter=typeid&amp;typeid='.$thread['typeid'].'">'.$_G['forum']['threadtypes']['types'][$thread['typeid']].'</a>]</em>';
        } elseif($_G['forum']['threadtypes']['icons'][$thread['typeid']] && $_G['forum']['threadtypes']['prefix'] == 2) {
            $thread['typehtml'] = '<em><a title="'.$_G['forum']['threadtypes']['types'][$thread['typeid']].'" href="forum.php?mod=forumdisplay&fid='.$_G['fid'].'&amp;filter=typeid&amp;typeid='.$thread['typeid'].'">'.'<img style="vertical-align: middle;padding-right:4px;" src="'.$_G['forum']['threadtypes']['icons'][$thread['typeid']].'" alt="'.$_G['forum']['threadtypes']['types'][$thread['typeid']].'" /></a></em>';
        }
        $thread['typename'] = $_G['forum']['threadtypes']['types'][$thread['typeid']];
    } else {
        $thread['typename'] = $thread['typehtml'] = '';
    }

    $thread['sorthtml'] = $thread['sortid'] && !empty($_G['forum']['threadsorts']['prefix']) && isset($_G['forum']['threadsorts']['types'][$thread['sortid']]) ?
        '<em>[<a href="forum.php?mod=forumdisplay&fid='.$_G['fid'].'&amp;filter=sortid&amp;sortid='.$thread['sortid'].'">'.$_G['forum']['threadsorts']['types'][$thread['sortid']].'</a>]</em>' : '';
    $thread['multipage'] = '';
    $topicposts = $thread['special'] ? $thread['replies'] : $thread['replies'] + 1;
    $multipate_archive = $_GET['archiveid'] && in_array($_GET['archiveid'], $threadtableids) ? "archiveid={$_GET['archiveid']}" : '';
    if($topicposts > $_G['ppp']) {
        $pagelinks = '';
        $thread['pages'] = ceil($topicposts / $_G['ppp']);
        $realtid = $_G['forum']['status'] != 3 && $thread['isgroup'] == 1 ? $thread['closed'] : $thread['tid'];
        for($i = 2; $i <= 6 && $i <= $thread['pages']; $i++) {
            $pagelinks .= "<a href=\"forum.php?mod=viewthread&tid=$realtid&amp;".(!empty($multipate_archive) ? "$multipate_archive&amp;" : '')."extra=$extra&amp;page=$i\">$i</a>";
        }
        if($thread['pages'] > 6) {
            $pagelinks .= "..<a href=\"forum.php?mod=viewthread&tid=$realtid&amp;".(!empty($multipate_archive) ? "$multipate_archive&amp;" : '')."extra=$extra&amp;page=$thread[pages]\">$thread[pages]</a>";
        }
        $thread['multipage'] = '&nbsp;...'.$pagelinks;
    }

    if($thread['highlight']) {
        $string = sprintf('%02d', $thread['highlight']);
        $stylestr = sprintf('%03b', $string[0]);

        $thread['highlight'] = ' style="';
        $thread['highlight'] .= $stylestr[0] ? 'font-weight: bold;' : '';
        $thread['highlight'] .= $stylestr[1] ? 'font-style: italic;' : '';
        $thread['highlight'] .= $stylestr[2] ? 'text-decoration: underline;' : '';
        $thread['highlight'] .= $string[1] ? 'color: '.$_G['forum_colorarray'][$string[1]].';' : '';
        if($thread['bgcolor']) {
            $thread['highlight'] .= "background-color: $thread[bgcolor];";
        }
        $thread['highlight'] .= '"';
    } else {
        $thread['highlight'] = '';
    }

    $thread['recommendicon'] = '';
    if(!empty($_G['setting']['recommendthread']['status']) && $thread['recommends']) {
        foreach($_G['setting']['recommendthread']['iconlevels'] as $k => $i) {
            if($thread['recommends'] > $i) {
                $thread['recommendicon'] = $k+1;
                break;
            }
        }
    }

    $thread['moved'] = $thread['heatlevel'] = $thread['new'] = 0;
    if($_G['forum']['status'] != 3 && ($thread['closed'] || ($_G['forum']['autoclose'] && $thread['fid'] == $_G['fid'] && TIMESTAMP - $thread[$closedby] > $_G['forum']['autoclose']))) {
        if($thread['isgroup'] == 1) {
            $thread['folder'] = 'common';
            $grouptids[] = $thread['closed'];
        } else {
            if($thread['closed'] > 1) {
                $thread['moved'] = $thread['tid'];
                $thread['allreplies'] = $thread['replies'] = '-';
                $thread['views'] = '-';
            }
            $thread['folder'] = 'lock';
        }
    } elseif($_G['forum']['status'] == 3 && $thread['closed'] == 1) {
        $thread['folder'] = 'lock';
    } else {
        $thread['folder'] = 'common';
        $thread['weeknew'] = TIMESTAMP - 604800 <= $thread['dbdateline'];
        if($thread['allreplies'] > $thread['views']) {
            $thread['views'] = $thread['allreplies'];
        }
        if($_G['setting']['heatthread']['iconlevels']) {
            foreach($_G['setting']['heatthread']['iconlevels'] as $k => $i) {
                if($thread['heats'] > $i) {
                    $thread['heatlevel'] = $k + 1;
                    break;
                }
            }
        }
    }
    $thread['icontid'] = $thread['forumstick'] || !$thread['moved'] && $thread['isgroup'] != 1 ? $thread['tid'] : $thread['closed'];
    if(!$thread['forumstick'] && ($thread['isgroup'] == 1 || $thread['fid'] != $_G['fid'])) {
        $thread['icontid'] = $thread['closed'] > 1 ? $thread['closed'] : $thread['tid'];
    }
    $thread['istoday'] = $thread['dateline'] > $todaytime ? 1 : 0;
    $thread['dbdateline'] = $thread['dateline'];
    $thread['dateline'] = dgmdate($thread['dateline'], 'u', '9999', getglobal('setting/dateformat'));
    $thread['dblastpost'] = $thread['lastpost'];
    $thread['lastpost'] = dgmdate($thread['lastpost'], 'u');
    $thread['hidden'] = $_G['setting']['threadhidethreshold'] && $thread['hidden'] >= $_G['setting']['threadhidethreshold'] || in_array($thread['tid'], $thide);
    if($thread['hidden']) {
        $_G['hiddenexists']++;
    }

    if(in_array($thread['displayorder'], array(1, 2, 3, 4))) {
        $thread['id'] = 'stickthread_'.$thread['tid'];
        $separatepos++;
    } else {
        $thread['id'] = 'normalthread_'.$thread['tid'];
        if($thread['folder'] == 'common' && $thread['dblastpost'] >= $forumlastvisit || !$forumlastvisit) {
            $thread['new'] = 1;
            $thread['folder'] = 'new';
            $thread['weeknew'] = TIMESTAMP - 604800 <= $thread['dbdateline'];
        }
        $_G['showrows']++;
    }
    if(isset($_G['setting']['verify']['enabled']) && $_G['setting']['verify']['enabled']) {
        $verifyuids[$thread['authorid']] = $thread['authorid'];
    }
    $authorids[$thread['authorid']] = $thread['authorid'];
    $thread['mobile'] = base_convert(getstatus($thread['status'], 13).getstatus($thread['status'], 12).getstatus($thread['status'], 11), 2, 10);
    $thread['rushreply'] = getstatus($thread['status'], 3);
    if($thread['rushreply']) {
        $rushtids[$thread['tid']] = $thread['tid'];
    }
    $threadids[$threadindex] = $thread['tid'];
    $_G['forum_threadlist'][$threadindex] = $thread;
    $threadindex++;
}

$_G['hiddenexists'] = !$_G['forum']['ismoderator'] && $_G['hiddenexists'] && $_G['showrows'] >= $_G['hiddenexists'];

$livethread = array();
if($_G['forum']['livetid'] && $page == 1 && (!$filter || ($filter == 'sortid' && $_G['forum']['threadsorts']['defaultshow'] == $_GET['sortid']))) {
    include_once libfile('function/post');
    $livethread = C::t('forum_thread')->fetch($_G['forum']['livetid']);
    $livepost = C::t('forum_post')->fetch_threadpost_by_tid_invisible($_G['forum']['livetid']);
    $livemessage = messagecutstr($livepost['message'], 200);
    $liveallowpostreply = ($_G['forum']['allowreply'] != -1) && (($livethread['isgroup'] || (!$livethread['closed'] && !checkautoclose($livethread))) || $_G['forum']['ismoderator']) && ((!$_G['forum']['replyperm'] && $_G['group']['allowreply']) || ($_G['forum']['replyperm'] && forumperm($_G['forum']['replyperm'])) || $_G['forum']['allowreply']);
}

if($rushtids) {
    $rushinfo = C::t('forum_threadrush')->fetch_all($rushtids);
    foreach($rushinfo as $tid => $info) {
        if($info['starttimefrom'] > TIMESTAMP) {
            $info['timer'] = $info['starttimefrom'] - TIMESTAMP;
            $info['timertype'] = 'start';
        } elseif($info['starttimeto'] > TIMESTAMP) {
            $info['timer'] = $info['starttimeto'] - TIMESTAMP;
            $info['timertype'] = 'end';
        } else {
            $info = '';
        }
        $rushinfo[$tid] = $info;
    }
}
if(!empty($threadids)) {
    $indexlist = array_flip($threadids);
    foreach(C::t('forum_threadaddviews')->fetch_all($threadids) as $tidkey => $value) {
        $index = $indexlist[$tidkey];
        $threadlist[$index]['views'] += $value['addviews'];
        $_G['forum_threadlist'][$index]['views'] += $value['addviews'];
    }
}

$verify = array();
if($_G['setting']['verify']['enabled'] && $verifyuids) {
    $verify = forumdisplay_verify_author($verifyuids);
}

if($authorids) {
    loadcache('usergroups');
    $groupcolor = array();
    foreach(C::t('common_member')->fetch_all($authorids) as $value) {
        $groupcolor[$value['uid']] = $_G['cache']['usergroups'][$value['groupid']]['color'];
    }
}
$_G['forum_threadnum'] = count($_G['forum_threadlist']) - $separatepos;

if(!empty($grouptids)) {
    $groupfids = array();
    foreach(C::t('forum_thread')->fetch_all_by_tid($grouptids) as $row) {
        $groupnames[$row['tid']]['fid'] = $row['fid'];
        $groupnames[$row['tid']]['views'] = $row['views'];
        $groupfids[] = $row['fid'];
    }
    $forumsinfo = C::t('forum_forum')->fetch_all($groupfids);
    foreach($groupnames as $gtid => $value) {
        $gfid = $groupnames[$gtid]['fid'];
        $groupnames[$gtid]['name'] = $forumsinfo[$gfid]['name'];
    }
}

$stemplate = null;
if($_G['forum']['threadsorts']['types'] && $sortoptionarray && $templatearray && $threadids) {
    $sortid = intval($_GET['sortid']);
    if(!strexists($templatearray[$sortid], '{subject_url}') && !strexists($templatearray[$sortid], '{tid}')) {
        $sortlistarray = showsorttemplate($sortid, $_G['fid'], $sortoptionarray, $templatearray, $_G['forum_threadlist'], $threadids);
        $stemplate = $sortlistarray['template'];
    } else {
        $sorttemplate = showsortmodetemplate($sortid, $_G['fid'], $sortoptionarray, $templatearray, $_G['forum_threadlist'], $threadids, $verify);
        $_G['forum']['sortmode'] = 1;
    }

    if(($_GET['searchoption'] || $_GET['searchsort']) && empty($searchsorttids)) {
        $_G['forum_threadlist'] = $multipage = '';
    }
}

$separatepos = $separatepos ? $separatepos + 1 : 0;