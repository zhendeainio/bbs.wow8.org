<?php if(!defined('IN_DISCUZ')) exit('Access Denied');

if($_G['setting']['indexhot']['status'] && $_G['cache']['heats']['expiration'] < TIMESTAMP) {
    require_once libfile('function/cache');
    updatecache('heats');
}