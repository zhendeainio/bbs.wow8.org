<?php if(!defined('IN_DISCUZ')) exit('Access Denied');

if($_G['uid'] && empty($_G['cookie']['nofavfid'])) {
    $favfids = array();
    $forum_favlist = C::t('home_favorite')->fetch_all_by_uid_idtype($_G['uid'], 'fid');
    if(!$forum_favlist) {
        dsetcookie('nofavfid', 1, 31536000);
    }
    foreach($forum_favlist as $key => $favorite) {
        if(defined('IN_MOBILE')) {
            $forum_favlist[$key]['title'] = strip_tags($favorite['title']);
        }
        $favfids[] = $favorite['id'];
    }
    if($favfids) {
        $favforumlist = C::t('forum_forum')->fetch_all($favfids);
        $favforumlist_fields = C::t('forum_forumfield')->fetch_all($favfids);
        foreach($favforumlist as $id => $forum) {
            if($favforumlist_fields[$forum['fid']]['fid']) {
                $favforumlist[$id] = array_merge($forum, $favforumlist_fields[$forum['fid']]);
            }
            forum($favforumlist[$id]);
        }
    }
}