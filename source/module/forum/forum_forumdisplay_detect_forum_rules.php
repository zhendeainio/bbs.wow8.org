<?php if(!defined('IN_DISCUZ')) exit('Access Denied');
if(!isset($_G['cookie']['collapse']) || strpos($_G['cookie']['collapse'], 'forum_rules_'.$_G['fid']) === FALSE) {
    $collapse['forum_rules'] = '';
    $collapse['forum_rulesimg'] = 'no';
} else {
    $collapse['forum_rules'] = 'display: none';
    $collapse['forum_rulesimg'] = 'yes';
}