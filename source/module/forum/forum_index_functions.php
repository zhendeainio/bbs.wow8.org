<?php if(!defined('IN_DISCUZ')) exit('Access Denied');

function get_index_announcements() {
    global $_G;
    $announcements = '';
    if($_G['cache']['announcements']) {
        $readapmids = !empty($_G['cookie']['readapmid']) ? explode('D', $_G['cookie']['readapmid']) : array();
        foreach($_G['cache']['announcements'] as $announcement) {
            if(!$announcement['endtime'] || $announcement['endtime'] > TIMESTAMP && (empty($announcement['groups']) || in_array($_G['member']['groupid'], $announcement['groups']))) {
                if(empty($announcement['type'])) {
                    $announcements .= '<li><span><a href="forum.php?mod=announcement&id='.$announcement['id'].'" target="_blank" class="xi2">'.$announcement['subject'].
                        '</a></span><em>('.dgmdate($announcement['starttime'], 'd').')</em></li>';
                } elseif($announcement['type'] == 1) {
                    $announcements .= '<li><span><a href="'.$announcement['message'].'" target="_blank" class="xi2">'.$announcement['subject'].
                        '</a></span><em>('.dgmdate($announcement['starttime'], 'd').')</em></li>';
                }
            }
        }
    }
    return $announcements;
}

function get_index_page_guest_cache() {
    global $_G;
    $indexcache = getcacheinfo(0);
    if(TIMESTAMP - $indexcache['filemtime'] > $_G['setting']['cacheindexlife']) {
        @unlink($indexcache['filename']);
        define('CACHE_FILE', $indexcache['filename']);
    } elseif($indexcache['filename']) {
        @readfile($indexcache['filename']);
        $updatetime = dgmdate($indexcache['filemtime'], 'H:i:s');
        $gzip = $_G['gzipcompress'] ? ', Gzip enabled' : '';
        echo "<script type=\"text/javascript\">
			if($('debuginfo')) {
				$('debuginfo').innerHTML = '. This page is cached  at $updatetime $gzip .';
			}
			</script>";
        exit();
    }
}

function get_index_memory_by_groupid($key) {
    $enable = getglobal('setting/memory/forumindex');
    if($enable !== null && memory('check')) {
        if(IS_ROBOT) {
            $key = 'for_robot';
        }
        $ret = memory('get', 'forum_index_page_'.$key);
        define('FORUM_INDEX_PAGE_MEMORY', $ret ? 1 : 0);
        if($ret) {
            return $ret;
        }
    }
    return array('none' => null);
}

function get_index_online_details() {
    // $showoldetails = getgpc('showoldetails');
    $showoldetails = 'yes'; // bbs.wow8.org: Always Display Online List
    switch($showoldetails) {
        case 'no': dsetcookie('onlineindex', ''); break;
        case 'yes': dsetcookie('onlineindex', 1, 86400 * 365); break;
    }
    return $showoldetails;
}

function do_forum_bind_domains() {
    global $_G;
    if($_G['setting']['binddomains'] && $_G['setting']['forumdomains']) {
        $loadforum = isset($_G['setting']['binddomains'][$_SERVER['HTTP_HOST']]) ? max(0, intval($_G['setting']['binddomains'][$_SERVER['HTTP_HOST']])) : 0;
        if($loadforum) {
            dheader('Location: '.$_G['setting']['siteurl'].'/forum.php?mod=forumdisplay&fid='.$loadforum);
        }
    }
}

function categorycollapse() {
    global $_G, $collapse, $catlist;
    if(!$_G['uid']) {
        return;
    }
    foreach($catlist as $fid => $forum) {
        if(!isset($_G['cookie']['collapse']) || strpos($_G['cookie']['collapse'], '_category_'.$fid.'_') === FALSE) {
            $catlist[$fid]['collapseimg'] = 'collapsed_no.gif';
            $collapse['category_'.$fid] = '';
        } else {
            $catlist[$fid]['collapseimg'] = 'collapsed_yes.gif';
            $collapse['category_'.$fid] = 'display: none';
        }
    }

    for($i = -2; $i <= 0; $i++) {
        if(!isset($_G['cookie']['collapse']) || strpos($_G['cookie']['collapse'], '_category_'.$i.'_') === FALSE) {
            $collapse['collapseimg_'.$i] = 'collapsed_no.gif';
            $collapse['category_'.$i] = '';
        } else {
            $collapse['collapseimg_'.$i] = 'collapsed_yes.gif';
            $collapse['category_'.$i] = 'display: none';
        }
    }
}