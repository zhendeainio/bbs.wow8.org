<?php if(!defined('IN_DISCUZ')) exit('Access Denied');

$post_count_per_page = 15;

$page = intval($_GET['page']);
if ($page < 1) $page = 1;

$forum_last_posts = C::t('forum_post')->fetch_last_visible_posts('', ($page - 1) * $post_count_per_page, $post_count_per_page);

$last_tids = [];
foreach ($forum_last_posts as $forum_last_post) $last_tids[] = $forum_last_post['tid'];

$forum_last_posts_threads = C::t('forum_thread')->fetch_all_by_tid($last_tids);
$last_fids = [];
foreach ($forum_last_posts_threads as $forum_last_posts_thread) $last_fids[] = $forum_last_posts_thread['fid'];

$forum_last_posts_forums = C::t('forum_forum')->fetch_all_name_by_fid($last_fids);