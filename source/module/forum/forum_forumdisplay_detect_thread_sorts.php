<?php if(!defined('IN_DISCUZ')) exit('Access Denied');
if($_G['forum']['threadsorts']['types'] && $sortoptionarray && ($_GET['searchoption'] || $_GET['searchsort'])) {
    $sortid = intval($_GET['sortid']);

    if($_GET['searchoption']){
        $forumdisplayadd['page'] = '&sortid='.$sortid;
        foreach($_GET['searchoption'] as $optionid => $option) {
            $optionid = intval($optionid);
            $searchoption = '';
            if(is_array($option['value'])) {
                foreach($option['value'] as $v) {
                    $v = rawurlencode((string)$v);
                    $searchoption .= "&searchoption[$optionid][value][$v]=$v";
                }
            } else {
                $option['value'] = rawurlencode((string)$option['value']);
                $option['value'] && $searchoption = "&searchoption[$optionid][value]=$option[value]";
            }
            $option['type'] = rawurlencode((string)$option['type']);
            $identifier = $sortoptionarray[$sortid][$optionid]['identifier'];
            $forumdisplayadd['page'] .= $searchoption ? "$searchoption&searchoption[$optionid][type]=$option[type]" : '';
        }
    }

    $searchsorttids = sortsearch($_GET['sortid'], $sortoptionarray, $_GET['searchoption'], $selectadd, $_G['fid']);
    $filterarr['intids'] = $searchsorttids ? $searchsorttids : array(0);
}