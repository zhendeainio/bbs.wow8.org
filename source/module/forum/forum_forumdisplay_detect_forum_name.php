<?php if(!defined('IN_DISCUZ')) exit('Access Denied');

$_G['forum']['name'] = strip_tags($_G['forum']['name']) ? strip_tags($_G['forum']['name']) : $_G['forum']['name'];
$_G['forum']['extra'] = empty($_G['forum']['extra']) ? array() : dunserialize($_G['forum']['extra']);
if(!is_array($_G['forum']['extra'])) {
    $_G['forum']['extra'] = array();
}