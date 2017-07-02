<?php

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
    exit('Access Denied');
}

showtableheader('home_sys_info', 'fixpadding');
showtablerow('', array('class="vtop td24 lineheight"', 'class="lineheight smallfont"'), array(
    cplang('home_discuz_version'),
    'Discuz! '.DISCUZ_VERSION.' Release '.DISCUZ_RELEASE.' <a href="http://faq.comsenz.com/checkversion.php?product=Discuz&version='.DISCUZ_VERSION.'&release='.DISCUZ_RELEASE.'&charset='.CHARSET.'&dbcharset='.$dbcharset.'" class="lightlink2 smallfont" target="_blank">'.cplang('home_check_newversion').'</a> <a href="http://www.comsenz.com/purchase/discuz/" class="lightlink2 smallfont" target="_blank">&#19987;&#19994;&#25903;&#25345;&#19982;&#26381;&#21153;</a> <a href="http://idc.comsenz.com" class="lightlink2 smallfont" target="_blank">&#68;&#105;&#115;&#99;&#117;&#122;&#33;&#19987;&#29992;&#20027;&#26426;</a>'
));
showtablerow('', array('class="vtop td24 lineheight"', 'class="lineheight smallfont"'), array(
    cplang('home_ucclient_version'),
    'UCenter '.UC_CLIENT_VERSION.' Release '.UC_CLIENT_RELEASE
));
showtablerow('', array('class="vtop td24 lineheight"', 'class="lineheight smallfont"'), array(
    cplang('home_environment'),
    $serverinfo
));
showtablerow('', array('class="vtop td24 lineheight"', 'class="lineheight smallfont"'), array(
    cplang('home_serversoftware'),
    $serversoft
));
showtablerow('', array('class="vtop td24 lineheight"', 'class="lineheight smallfont"'), array(
    cplang('home_database'),
    $dbversion
));
showtablerow('', array('class="vtop td24 lineheight"', 'class="lineheight smallfont"'), array(
    cplang('home_upload_perm'),
    $fileupload
));
showtablerow('', array('class="vtop td24 lineheight"', 'class="lineheight smallfont"'), array(
    cplang('home_database_size'),
    $dbsize
));
showtablerow('', array('class="vtop td24 lineheight"', 'class="lineheight smallfont"'), array(
    cplang('home_attach_size'),
    $attachsize
));
showtablefooter();