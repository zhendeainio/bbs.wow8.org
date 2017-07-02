<?php if(!defined('IN_DISCUZ')) exit('Access Denied');

if(!$simplestyle || !$_G['forum']['allowside'] && $page == 1) {
    if($_G['cache']['announcements_forum'] && (!$_G['cache']['announcements_forum']['endtime'] || $_G['cache']['announcements_forum']['endtime'] > TIMESTAMP)) {
        $announcement = $_G['cache']['announcements_forum'];
        $announcement['starttime'] = dgmdate($announcement['starttime'], 'd');
    } else {
        $announcement = NULL;
    }
}