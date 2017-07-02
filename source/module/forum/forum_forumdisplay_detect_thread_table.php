<?php if(!defined('IN_DISCUZ')) exit('Access Denied');

$threadtable_info = !empty($_G['cache']['threadtable_info']) ? $_G['cache']['threadtable_info'] : array();
$forumarchive = array();
if($_G['forum']['archive']) {
    foreach(C::t('forum_forum_threadtable')->fetch_all_by_fid($_G['fid']) as $archive) {
        $forumarchive[$archive['threadtableid']] = array(
            'displayname' => dhtmlspecialchars($threadtable_info[$archive['threadtableid']]['displayname']),
            'threads' => $archive['threads'],
            'posts' => $archive['posts'],
        );
        if(empty($forumarchive[$archive['threadtableid']]['displayname'])) {
            $forumarchive[$archive['threadtableid']]['displayname'] = lang('forum/thread', 'forum_archive').' '.$archive['threadtableid'];
        }
    }
}