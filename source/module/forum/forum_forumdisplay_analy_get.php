<?php if(!defined('IN_DISCUZ')) exit('Access Denied');

$st_t = $_G['uid'].'|'.TIMESTAMP;
dsetcookie('st_t', $st_t.'|'.md5($st_t.$_G['config']['security']['authkey']));

$_G['action']['fid'] = $_G['fid'];

$_GET['specialtype'] = isset($_GET['specialtype']) ? $_GET['specialtype'] : '';
$_GET['dateline'] = isset($_GET['dateline']) ? intval($_GET['dateline']) : 0;
$_GET['digest'] = isset($_GET['digest']) ? 1 : '';
$_GET['archiveid'] = isset($_GET['archiveid']) ? intval($_GET['archiveid']) : 0;