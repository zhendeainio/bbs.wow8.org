<?php

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
    exit('Access Denied');
}

if($securityadvise) {
    showtableheader('home_security_tips', '', '', 0);
    showtablerow('', 'class="tipsblock"', '<ul>'.$securityadvise.'</ul>');
    showtablefooter();
}