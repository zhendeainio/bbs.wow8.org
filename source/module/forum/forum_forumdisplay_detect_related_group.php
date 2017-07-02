<?php if(!defined('IN_DISCUZ')) exit('Access Denied');
if($_G['forum']['relatedgroup']) {
    $relatedgroup = explode(',', $_G['forum']['relatedgroup']);
    $relatedgroup[] = $_G['fid'];
    $filterarr['inforum'] = $relatedgroup;
} else {
    $filterarr['inforum'] = $_G['fid'];
}
if(empty($filter) && empty($_GET['sortid']) && empty($_G['forum']['relatedgroup'])) {
    if($forumarchive) {
        if($_GET['archiveid']) {
            $_G['forum_threadcount'] = $forumarchive[$_GET['archiveid']]['threads'];
        } else {
            $primarytabthreads = $_G['forum']['threads'];
            foreach($forumarchive as $arcid => $avalue) {
                if($arcid) {
                    $primarytabthreads = $primarytabthreads - $avalue['threads'];
                }
            }
            $_G['forum_threadcount'] = $primarytabthreads;
        }
    } else {
        $_G['forum_threadcount'] = $_G['forum']['threads'];
    }
} else {
    $filterarr['sticky'] = 0;
    $_G['forum_threadcount'] = C::t('forum_thread')->count_search($filterarr, $tableid);
    if($threadclasscount) {
        threadclasscount($_G['fid'], $threadclasscount['id'], $threadclasscount['idtype'], $_G['forum_threadcount']);
    }
}