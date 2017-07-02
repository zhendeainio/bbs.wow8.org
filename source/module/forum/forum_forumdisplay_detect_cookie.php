<?php if(!defined('IN_DISCUZ')) exit('Access Denied');

if(!isset($_G['cookie']['atarget'])) {
    if($_G['setting']['targetblank']) {
        dsetcookie('atarget', 1, 2592000);
        $_G['cookie']['atarget'] = 1;
    }
}