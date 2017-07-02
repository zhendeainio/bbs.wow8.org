<?php if(!defined('IN_DISCUZ')) exit('Access Denied');

$grids = array();
if($_G['setting']['grid']['showgrid']) {
    loadcache('grids');
    $cachelife = $_G['setting']['grid']['cachelife'] ? $_G['setting']['grid']['cachelife'] : 600;
    $now = dgmdate(TIMESTAMP, lang('form/misc', 'y_m_d')).' '.lang('forum/misc', 'week_'.dgmdate(TIMESTAMP, 'w'));
    if(TIMESTAMP - $_G['cache']['grids']['cachetime'] < $cachelife) {
        $grids = $_G['cache']['grids'];
    } else {
        $images = array();
        $_G['setting']['grid']['fids'] = in_array(0, $_G['setting']['grid']['fids']) ? 0 : $_G['setting']['grid']['fids'];

        if($_G['setting']['grid']['gridtype']) {
            $grids['digest'] = C::t('forum_thread')->fetch_all_for_guide('digest', 0, array(), 3, 0, 0, 10, $_G['setting']['grid']['fids']);
        } else {
            $images = C::t('forum_threadimage')->fetch_all_order_by_tid(10);
            foreach($images as $key => $value) {
                $tids[$value['tid']] = $value['tid'];
            }
            $grids['image'] = C::t('forum_thread')->fetch_all_by_tid($tids);
        }
        $grids['newthread'] = C::t('forum_thread')->fetch_all_for_guide('newthread', 0, array(), 0, 0, 0, 10, $_G['setting']['grid']['fids']);

        $grids['newreply'] = C::t('forum_thread')->fetch_all_for_guide('reply', 0, array(), 0, 0, 0, 10, $_G['setting']['grid']['fids']);
        $grids['hot'] = C::t('forum_thread')->fetch_all_for_guide('hot', 0, array(), 3, 0, 0, 10, $_G['setting']['grid']['fids']);

        $_G['forum_colorarray'] = array('', '#EE1B2E', '#EE5023', '#996600', '#3C9D40', '#2897C5', '#2B65B7', '#8F2A90', '#EC1282');
        foreach($grids as $type => $gridthreads) {
            foreach($gridthreads as $key => $gridthread) {
                $gridthread['dateline'] = str_replace('"', '\'', dgmdate($gridthread['dateline'], 'u', '9999', getglobal('setting/dateformat')));
                $gridthread['lastpost'] = str_replace('"', '\'', dgmdate($gridthread['lastpost'], 'u', '9999', getglobal('setting/dateformat')));
                if($gridthread['highlight'] && $_G['setting']['grid']['highlight']) {
                    $string = sprintf('%02d', $gridthread['highlight']);
                    $stylestr = sprintf('%03b', $string[0]);

                    $gridthread['highlight'] = ' style="';
                    $gridthread['highlight'] .= $stylestr[0] ? 'font-weight: bold;' : '';
                    $gridthread['highlight'] .= $stylestr[1] ? 'font-style: italic;' : '';
                    $gridthread['highlight'] .= $stylestr[2] ? 'text-decoration: underline;' : '';
                    $gridthread['highlight'] .= $string[1] ? 'color: '.$_G['forum_colorarray'][$string[1]] : '';
                    $gridthread['highlight'] .= '"';
                } else {
                    $gridthread['highlight'] = '';
                }
                if($_G['setting']['grid']['textleng']) {
                    $gridthread['oldsubject'] = dhtmlspecialchars($gridthread['subject']);
                    $gridthread['subject'] = cutstr($gridthread['subject'], $_G['setting']['grid']['textleng']);
                }

                $grids[$type][$key] = $gridthread;
            }
        }
        if(!$_G['setting']['grid']['gridtype']) {

            $focuspic = $focusurl = $focustext = array();
            $grids['focus'] = 'config=5|0xffffff|0x0099ff|50|0xffffff|0x0099ff|0x000000';
            foreach($grids['image'] as $ithread) {
                if($ithread['displayorder'] < 0) {
                    continue;
                }
                if($images[$ithread['tid']]['remote']) {
                    $imageurl = $_G['setting']['ftp']['attachurl'].'forum/'.$images[$ithread['tid']]['attachment'];
                } else {
                    $imageurl = $_G['setting']['attachurl'].'forum/'.$images[$ithread['tid']]['attachment'];
                }
                $grids['slide'][$ithread['tid']] = array(
                    'image' => $imageurl,
                    'url' => 'forum.php?mod=viewthread&tid='.$ithread['tid'],
                    'subject' => $ithread['subject']
                );
            }
        }
        $grids['cachetime'] = TIMESTAMP;
        savecache('grids', $grids);
    }
}