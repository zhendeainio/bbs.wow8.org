<?php if(!defined('IN_DISCUZ')) exit('Access Denied');

$forumlastvisit = 0;
if(empty($_G['forum']['picstyle']) && isset($_G['cookie']['forum_lastvisit']) && strexists($_G['cookie']['forum_lastvisit'], 'D_'.$_G['fid'])) {
    preg_match('/D\_'.$_G['fid'].'\_(\d+)/', $_G['cookie']['forum_lastvisit'], $a);
    $forumlastvisit = $a[1];
    unset($a);
}
dsetcookie('forum_lastvisit', preg_replace("/D\_".$_G['fid']."\_\d+/", '', $_G['cookie']['forum_lastvisit']).'D_'.$_G['fid'].'_'.TIMESTAMP, 604800);