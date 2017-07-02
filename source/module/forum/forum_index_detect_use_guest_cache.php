<?php if(!defined('IN_DISCUZ')) exit('Access Denied');

if(!$_G['uid'] && !$gid && $_G['setting']['cacheindexlife'] && !defined('IN_ARCHIVER') && !defined('IN_MOBILE')) {
    get_index_page_guest_cache();
}