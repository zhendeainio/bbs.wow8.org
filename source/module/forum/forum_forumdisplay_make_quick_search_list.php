<?php if(!defined('IN_DISCUZ')) exit('Access Denied');

$quicksearchlist = array();
if(!empty($_G['forum']['threadsorts']['types'])) {
    require_once libfile('function/threadsort');

    $showpic = intval($_GET['showpic']);
    $templatearray = $sortoptionarray = array();
    foreach($_G['forum']['threadsorts']['types'] as $stid => $sortname) {
        loadcache(array('threadsort_option_'.$stid, 'threadsort_template_'.$stid));
        sortthreadsortselectoption($stid);
        $templatearray[$stid] = $_G['cache']['threadsort_template_'.$stid]['subject'];
        $sortoptionarray[$stid] = $_G['cache']['threadsort_option_'.$stid];
    }

    if(!empty($_G['forum']['threadsorts']['defaultshow']) && empty($_GET['sortid']) && empty($_GET['sortall'])) {
        $_GET['sortid'] = $_G['forum']['threadsorts']['defaultshow'];
        $_GET['filter'] = 'sortid';
        $_SERVER['QUERY_STRING'] = $_SERVER['QUERY_STRING'] ? $_SERVER['QUERY_STRING'].'&sortid='.$_GET['sortid'] : 'sortid='.$_GET['sortid'];
        $filterurladd = '&amp;filter=sort';
    }

    $_GET['sortid'] = $_GET['sortid'] ? $_GET['sortid'] : $_GET['searchsortid'];
    if(isset($_GET['sortid']) && $_G['forum']['threadsorts']['types'][$_GET['sortid']]) {
        $searchsortoption = $sortoptionarray[$_GET['sortid']];
        $quicksearchlist = quicksearch($searchsortoption);
        $_G['forum_optionlist'] = $_G['cache']['threadsort_option_'.$_GET['sortid']];
        $forum_optionlist = getsortedoptionlist();
    }
}