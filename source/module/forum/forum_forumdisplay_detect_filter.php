<?php if(!defined('IN_DISCUZ')) exit('Access Denied');

$filteradd = $sortoptionurl = $sp = '';
$sorturladdarray = $selectadd = array();
$forumdisplayadd = array('orderby' => '');
$specialtype = array('poll' => 1, 'trade' => 2, 'reward' => 3, 'activity' => 4, 'debate' => 5);
$filterfield = array('digest', 'recommend', 'sortall', 'typeid', 'sortid', 'dateline', 'page', 'orderby', 'specialtype', 'author', 'view', 'reply', 'lastpost', 'hot');

foreach($filterfield as $v) {
    $forumdisplayadd[$v] = '';
}

$filter = isset($_GET['filter']) && in_array($_GET['filter'], $filterfield) ? $_GET['filter'] : '';
$filterbool = !empty($filter);
$filterarr = $multiadd = array();
$threadclasscount = array();

if($filter && $filter != 'hot') {
    if($query_string = $_SERVER['QUERY_STRING']) {
        $query_string = substr($query_string, (strpos($query_string, "&") + 1));
        parse_str($query_string, $geturl);
        $geturl = daddslashes($geturl, 1);
        if($geturl && is_array($geturl)) {
            $issort = isset($_GET['sortid']) && isset($_G['forum']['threadsorts']['types'][$_GET['sortid']]) && $quicksearchlist ? TRUE : FALSE;
            $selectadd = $issort ? $geturl : array();
            foreach($filterfield as $option) {
                foreach($geturl as $field => $value) {
                    if(in_array($field, $filterfield) && $option != $field && $field != 'page' && ($field != 'orderby' || !in_array($option, array('author', 'reply', 'view', 'lastpost', 'heat')))) {
                        if(!(in_array($option, array('digest', 'recommend')) && in_array($field, array('digest', 'recommend')))) {
                            $forumdisplayadd[$option] .= '&'.rawurlencode($field).'='.rawurlencode($value);
                        }
                    }
                }
                if($issort) {
                    $sfilterfield = array_merge(array('filter', 'sortid', 'orderby', 'fid'), $filterfield);
                    foreach($geturl as $soption => $value) {
                        $forumdisplayadd[$soption] .= !in_array($soption, $sfilterfield) ? '&'.rawurlencode($soption).'='.rawurlencode($value) : '';
                    }
                    unset($sfilterfield);
                }
            }
            if($issort && is_array($quicksearchlist)) {
                foreach($quicksearchlist as $option) {
                    $identifier = $option['identifier'];
                    foreach($geturl as $option => $value) {
                        $sorturladdarray[$identifier] .= !in_array($option, array('filter', 'sortid', 'orderby', 'fid', 'searchsort', $identifier)) ? '&amp;'.rawurlencode($option).'='.rawurlencode($value) : '';
                    }
                }
            }

            foreach($geturl as $field => $value) {
                if($field != 'page' && $field != 'fid' && $field != 'searchoption') {
                    $multiadd[] = rawurlencode($field).'='.rawurlencode($value);
                    if(in_array($field, $filterfield)) {
                        if($field == 'digest') {
                            $filterarr['digest'] = 1;
                        } elseif($field == 'recommend') {
                            $filterarr['recommends'] = intval($_G['setting']['recommendthread']['iconlevels'][0]);
                        } elseif($field == 'specialtype') {
                            $filterarr['special'] = $specialtype[$value];
                            $filterarr['specialthread'] = 1;
                            if($value == 'reward') {
                                if($_GET['rewardtype'] == 1) {
                                    $filterarr['pricemore'] = 0;
                                } elseif($_GET['rewardtype'] == 2) {
                                    $filterarr['pricesless'] = 0;
                                }
                            }
                        } elseif($field == 'dateline') {
                            if($value) {
                                $filterarr['lastpostmore'] = TIMESTAMP - $value;
                            }
                        } elseif($field == 'typeid' || $field == 'sortid') {
                            $fieldstr = $field == 'typeid' ? 'intype' : 'insort';
                            $filterarr[$fieldstr] = $value;
                        }
                        $sp = ' ';
                    }
                }
            }
            if(count($filterarr) == 1) {
                foreach($filterarr as $key => $value) {
                    if($key == 'intype') {
                        $threadclasscount = array('id' => $value, 'idtype' => 'typeid');
                    } elseif($key == 'insort') {
                        $threadclasscount = array('id' => $value, 'idtype' => 'sortid');
                    }
                }
            }
        }
    }
    $simplestyle = true;
}
