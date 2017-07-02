<?php if(!defined('IN_DISCUZ')) exit('Access Denied');

if($gid && !empty($catlist)) {
    $_G['category'] = $catlist[$gid];
    $forumseoset = array(
        'seotitle' => $catlist[$gid]['seotitle'],
        'seokeywords' => $catlist[$gid]['keywords'],
        'seodescription' => $catlist[$gid]['seodescription']
    );
    $seodata = array('fgroup' => $catlist[$gid]['name']);
    list($navtitle, $metadescription, $metakeywords) = get_seosetting('threadlist', $seodata, $forumseoset);
    if(empty($navtitle)) {
        $navtitle = $navtitle_g;
        $nobbname = false;
    } else {
        $nobbname = true;
    }
    $_G['fid'] = $gid;
}