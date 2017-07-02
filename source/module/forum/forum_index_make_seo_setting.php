<?php if(!defined('IN_DISCUZ')) exit('Access Denied');

list($navtitle, $metadescription, $metakeywords) = get_seosetting('forum');
if(!$navtitle) {
    $navtitle = $_G['setting']['navs'][2]['navname'];
    $nobbname = false;
} else {
    $nobbname = true;
}
if(!$metadescription) {
    $metadescription = $navtitle;
}
if(!$metakeywords) {
    $metakeywords = $navtitle;
}