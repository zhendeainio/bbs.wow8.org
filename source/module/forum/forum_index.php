<?php if(!defined('IN_DISCUZ')) exit('Access Denied');

if(isset($_GET['yoyo'])) exit('yo');

require_once libfile('function/forumlist');
require_once 'forum_index_functions.php';

$gid = intval(getgpc('gid'));
$showoldetails = get_index_online_details();
require_once 'forum_index_detect_use_guest_cache.php';
$newthreads = round((TIMESTAMP - $_G['member']['lastvisit'] + 600) / 1000) * 1000;
$catlist = $forumlist = $sublist = $forumname = $collapse = $favforumlist = array();
$threads = $posts = $todayposts = $announcepm = 0;
$postdata = $_G['cache']['historyposts'] ? explode("\t", $_G['cache']['historyposts']) : array(0,0);
$postdata[0] = intval($postdata[0]);
$postdata[1] = intval($postdata[1]);
require_once 'forum_index_make_seo_setting.php';
require_once 'forum_index_update_cache_heats.php';
require_once 'forum_index_make_favforumlist.php';
require_once 'forum_index_make_recommend_and_follow_collections.php';
require_once 'forum_index_detect_archiver.php';
require_once 'forum_index_make_grids.php';
require_once 'forum_index_make_forums_information.php';
require_once 'forum_index_detect_output_archiver.php';
categorycollapse();
require_once 'forum_index_apply_seo_information.php';
require_once 'forum_index_detect_output_origin.php';