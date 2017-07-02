<?php if(!defined('IN_DISCUZ')) exit('Access Denied');

$subexists = 0;
foreach($_G['cache']['forums'] as $sub) {
    if($sub['type'] == 'sub' && $sub['fup'] == $_G['fid'] && (!$_G['setting']['hideprivate'] || !$sub['viewperm'] || forumperm($sub['viewperm']) || strstr($sub['users'], "\t$_G[uid]\t"))) {
        if(!$sub['status']) {
            continue;
        }
        $subexists = 1;
        $sublist = array();
        $query = C::t('forum_forum')->fetch_all_info_by_fids(0, 'available', 0, $_G['fid'], 1, 0, 0, 'sub');

        if(!empty($_G['member']['accessmasks'])) {
            $fids = array_keys($query);
            $accesslist = C::t('forum_access')->fetch_all_by_fid_uid($fids, $_G['uid']);
            foreach($query as $key => $val) {
                $query[$key]['allowview'] = $accesslist[$key];
            }
        }
        foreach($query as $sub) {
            $sub['extra'] = dunserialize($sub['extra']);
            if(!is_array($sub['extra'])) {
                $sub['extra'] = array();
            }
            if(forum($sub)) {
                $sub['orderid'] = count($sublist);
                $sublist[] = $sub;
            }
        }
        break;
    }
}

if(!empty($_GET['archiveid']) && in_array($_GET['archiveid'], $threadtableids)) {
    $subexists = 0;
}

if($subexists) {
    if($_G['forum']['forumcolumns']) {
        $_G['forum']['forumcolwidth'] = (floor(100 / $_G['forum']['forumcolumns']) - 0.1).'%';
        $_G['forum']['subscount'] = count($sublist);
        $_G['forum']['endrows'] = '';
        if($colspan = $_G['forum']['subscount'] % $_G['forum']['forumcolumns']) {
            while(($_G['forum']['forumcolumns'] - $colspan) > 0) {
                $_G['forum']['endrows'] .= '<td>&nbsp;</td>';
                $colspan ++;
            }
            $_G['forum']['endrows'] .= '</tr>';
        }
    }
    if(empty($_G['cookie']['collapse']) || strpos($_G['cookie']['collapse'], 'subforum_'.$_G['fid']) === FALSE) {
        $collapse['subforum'] = '';
        $collapseimg['subforum'] = 'collapsed_no.gif';
    } else {
        $collapse['subforum'] = 'display: none';
        $collapseimg['subforum'] = 'collapsed_yes.gif';
    }
}