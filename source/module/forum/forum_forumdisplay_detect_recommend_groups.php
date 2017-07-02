<?php if(!defined('IN_DISCUZ')) exit('Access Denied');

$recommendgroups = array();
if($_G['forum']['status'] != 3 && helper_access::check_module('group')) {
    loadcache('forumrecommend');
    $recommendgroups = $_G['cache']['forumrecommend'][$_G['fid']];
}

if($recommendgroups) {
    if(empty($_G['cookie']['collapse']) || strpos($_G['cookie']['collapse'], 'recommendgroups_'.$_G['fid']) === FALSE) {
        $collapse['recommendgroups'] = '';
        $collapseimg['recommendgroups'] = 'collapsed_no.gif';
    } else {
        $collapse['recommendgroups'] = 'display: none';
        $collapseimg['recommendgroups'] = 'collapsed_yes.gif';
    }
}