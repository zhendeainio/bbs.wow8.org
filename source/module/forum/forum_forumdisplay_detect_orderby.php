<?php if(!defined('IN_DISCUZ')) exit('Access Denied');

if(!empty($_GET['orderby']) && !$_G['setting']['closeforumorderby'] && in_array($_GET['orderby'], array('lastpost', 'dateline', 'replies', 'views', 'recommends', 'heats'))) {
    $forumdisplayadd['orderby'] .= '&orderby='.$_GET['orderby'];
} else {
    $_GET['orderby'] = isset($_G['cache']['forums'][$_G['fid']]['orderby']) ? $_G['cache']['forums'][$_G['fid']]['orderby'] : 'lastpost';
}

$_GET['ascdesc'] = isset($_G['cache']['forums'][$_G['fid']]['ascdesc']) ? $_G['cache']['forums'][$_G['fid']]['ascdesc'] : 'DESC';

$check = array();
$check[$filter] = $check[$_GET['orderby']] = $check[$_GET['ascdesc']] = 'selected="selected"';