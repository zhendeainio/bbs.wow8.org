<?php if(!defined('IN_DISCUZ')) exit('Access Denied');

if(empty($gid) && empty($_G['member']['accessmasks']) && empty($showoldetails)) {
    extract(get_index_memory_by_groupid($_G['member']['groupid']));
    if(defined('FORUM_INDEX_PAGE_MEMORY') && FORUM_INDEX_PAGE_MEMORY) {
        categorycollapse();
        if(!defined('IN_ARCHIVER')) {
            include template('diy:forum/discuz');
        } else {
            include loadarchiver('forum/discuz');
        }
        dexit();
    }
}