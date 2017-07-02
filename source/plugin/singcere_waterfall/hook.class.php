<?php

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

class plugin_singcere_waterfall {

    function global_footer() {
        global $_G;
        $echo = '';
        //$echo .= "<script src=\"source/plugin/singcere_waterfall/template/js/waterfall.js\"  type=\"text/javascript\"></script>";
        return $echo;
    }

}

class plugin_singcere_waterfall_forum extends plugin_singcere_waterfall {

    function post_produce_cover_message($params) {
        global $_G, $tid, $pid, $threadimageaid, $message, $modthread, $htmlon;
        list($msg, $url_forward, $values, $extraparam) = $params['param'];
        $wf_forum_limit = unserialize($_G['cache']['plugin']['singcere_waterfall']['wf_forum_limit']);

        // X3 MODLE (:
        if ($_G['setting']['version'] != "X2.5") {
            $pid = $modthread->pid;
            if ($modthread->param['special'] == 4 && $_GET['activityaid']) {
                $threadimageaid = $_GET['activityaid'];
            } else {
                $_GET['action'] == 'edit' && $tid = $_G['tid'];
                $threadimage = C::t('forum_attachment_n')->fetch_max_image('tid:' . $tid, 'tid', $tid);
                $threadimageaid = $threadimage['aid'];
            }
        }

        if ($msg == 'post_newthread_succeed' || $msg == 'post_newthread_mod_succeed') {
            require_once libfile('function/post');
            if (in_array($_G[fid], $wf_forum_limit) && !$_G['forum']['picstyle']) {
                if (!setthreadcover($pid, 0, $threadimageaid)) {
                    if ($htmlon) {
                        preg_match_all('/(\[img\]|\[img=\d{1,4}[x|\,]\d{1,4}\]|<img.*?src=")\s*([^\[\<\r\n]+?)\s*(\[\/img\]|".*>)/is', $message, $imglist, PREG_SET_ORDER);
                    } else {
                        preg_match_all("/(\[img\]|\[img=\d{1,4}[x|\,]\d{1,4}\])\s*([^\[\<\r\n]+?)\s*\[\/img\]/is", $message, $imglist, PREG_SET_ORDER);
                    }
                    $values['coverimg'] = "<p id=\"showsetcover\">" . lang('message', 'post_newthread_set_cover') . "<span id=\"setcoverwait\"></span></p><script>if($('forward_a')){\$('forward_a').style.display='none';setTimeout(\"$('forward_a').style.display=''\", 5000);};ajaxget('forum.php?mod=ajax&action=setthreadcover&tid=$tid&pid=$pid&fid=$_G[fid]&imgurl={$imglist[0][2]}&newthread=1', 'showsetcover', 'setcoverwait')</script>";
                    $param['clean_msgforward'] = 1;
                    $param['timeout'] = $param['refreshtime'] = 15;
                    //showmessage($msg, "forum.php?mod=viewthread&tid=$tid", $values, $param);
                    if ($_GET['handlekey'] != 'fastnewpost') {
                        $_G['setting']['msgforward']['quick'] = false;
                    }
                }
            }
            return true;
        } else if ($msg == 'post_edit_succeed') {
            global $thread, $message;

            if (in_array($_G[fid], $wf_forum_limit) && !$_G['forum']['picstyle']) {
                if (empty($thread['cover'])) {
                    $rtn = setthreadcover($pid, 0, $threadimageaid);
                } else {
                    $rtn = setthreadcover($pid, $_G['tid'], 0, 1);
                }
                if (!$rtn) {
                    preg_match_all("/(\[img\]|\[img=\d{1,4}[x|\,]\d{1,4}\])\s*([^\[\<\r\n]+?)\s*\[\/img\]/is", $message, $imglist, PREG_SET_ORDER);
                    $values['coverimg'] = "<p id=\"showsetcover\">" . "<span id=\"setcoverwait\"></span></p><script>if($('forward_a')){\$('forward_a').style.display='none';setTimeout(\"$('forward_a').style.display=''\", 5000);};ajaxget('forum.php?mod=ajax&action=setthreadcover&tid=$_G[tid]&pid=$pid&fid=$_G[fid]&imgurl={$imglist[0][2]}&newthread=1', 'showsetcover', 'setcoverwait')</script>";
                    $param['clean_msgforward'] = 1;
                    $param['timeout'] = $param['refreshtime'] = 15;
                    showmessage(lang('plugin/singcere_waterfall', 'post_edit_succeed'), "forum.php?mod=viewthread&tid=$_G[tid]", $values, $param);
                }
            }
            return true;
        } else if ($msg == 'post_reply_succeed' && $_GET['handlekey'] == 'k_reply') {
            showmessage(lang('plugin/singcere_waterfall', 'reply_success'), null, array(), array('showdialog' => 0, 'showmsg' => false, 'closetime' => 0.0001,
              'extrajs' => '<script>addreply(\'' . $_G['tid'] . '\', ' . $_G['uid'] . ', \'' . $_G['member']['username'] . '\', \'' . $message . '\'); updatetotalnum(\'' . $_G['tid'] . '\', 0, 0, 0, 1);</script>'));
        }
    }

    function collection_edit_inwf_message($params) {
        list($msg, $url_forward, $values, $extraparam) = $params['param'];
        if ($msg == 'collection_collect_succ' && $_GET['handlekey'] == 'k_coll') {
            showmessage('collection_collect_succ', null, array(), array('alert' => 'right', 'closetime' => 1, 'showdialog' => 1,
              'extrajs' => '<script>updatetotalnum(\'' . $_GET['tid'] . '\', 1, 0, 0, 0,' . TIMESTAMP . ');</script>'));
        }
    }

}

class plugin_singcere_waterfall_home extends plugin_singcere_waterfall {

    function spacecp_follow_inwf_message($params) {
        global $_G;
        list($msg, $url_forward, $values, $extraparam) = $params['param'];
        if ($msg == 'relay_feed_success' && $_GET['handlekey'] == 'k_relay') {
            if ($_GET['addnewreply']) {
                $message = str_replace("\r\n", '</br>', $_GET['note']);
                showmessage('relay_feed_success', null, array(), array('alert' => 'right', 'closetime' => 1, 'showdialog' => 1,
                  'extrajs' => '<script>addreply(\'' . $_G['tid'] . '\', ' . $_G['uid'] . ', \'' . $_G['member']['username'] . '\', \'' . $message . '\');updatetotalnum(\'' . $_G['tid'] . '\', 0, 1, 0, 1);</script>'));
            } else {
                showmessage('relay_feed_success', null, array(), array('alert' => 'right', 'closetime' => 1, 'showdialog' => 1,
                  'extrajs' => '<script>updatetotalnum(\'' . $_GET['tid'] . '\', 0, 1, 0, 0);</script>'));
            }
        }
    }

}

class mobileplugin_singcere_waterfall_forum extends plugin_singcere_waterfall {

    function post_produce_cover_message($params) {
        global $_G, $tid, $pid, $threadimageaid, $message, $modthread, $htmlon;
        list($msg, $url_forward, $values, $extraparam) = $params['param'];
        $wf_forum_limit = unserialize($_G['cache']['plugin']['singcere_waterfall']['wf_forum_limit']);

        // X3 MODLE (:
        if ($_G['setting']['version'] != "X2.5") {
            $pid = $modthread->pid;
            if ($modthread->param['special'] == 4 && $_GET['activityaid']) {
                $threadimageaid = $_GET['activityaid'];
            } else {
                $_GET['action'] == 'edit' && $tid = $_G['tid'];
                $threadimage = C::t('forum_attachment_n')->fetch_max_image('tid:' . $tid, 'tid', $tid);
                $threadimageaid = $threadimage['aid'];
            }
        }

        if ($msg == 'post_newthread_succeed' || $msg == 'post_newthread_mod_succeed') {
            require_once libfile('function/post');
            if (in_array($_G[fid], $wf_forum_limit) && !$_G['forum']['picstyle']) {
                if (!setthreadcover($pid, 0, $threadimageaid)) {
                    if ($htmlon) {
                        preg_match_all('/(\[img\]|\[img=\d{1,4}[x|\,]\d{1,4}\]|<img.*?src=")\s*([^\[\<\r\n]+?)\s*(\[\/img\]|".*>)/is', $message, $imglist, PREG_SET_ORDER);
                    } else {
                        preg_match_all("/(\[img\]|\[img=\d{1,4}[x|\,]\d{1,4}\])\s*([^\[\<\r\n]+?)\s*\[\/img\]/is", $message, $imglist, PREG_SET_ORDER);
                    }
                    $values['coverimg'] = "<p id=\"showsetcover\">" . lang('message', 'post_newthread_set_cover') . "<span id=\"setcoverwait\"></span></p><script>if($('forward_a')){\$('forward_a').style.display='none';setTimeout(\"$('forward_a').style.display=''\", 5000);};ajaxget('forum.php?mod=ajax&action=setthreadcover&tid=$tid&pid=$pid&fid=$_G[fid]&imgurl={$imglist[0][2]}&newthread=1', 'showsetcover', 'setcoverwait')</script>";
                    $param['clean_msgforward'] = 1;
                    $param['timeout'] = $param['refreshtime'] = 15;
                    //showmessage($msg, "forum.php?mod=viewthread&tid=$tid", $values, $param);
                    if ($_GET['handlekey'] != 'fastnewpost') {
                        $_G['setting']['msgforward']['quick'] = false;
                    }
                }
            }
            return true;
        } else if ($msg == 'post_edit_succeed') {
            global $thread, $message;

            if (in_array($_G[fid], $wf_forum_limit) && !$_G['forum']['picstyle']) {
                if (empty($thread['cover'])) {
                    $rtn = setthreadcover($pid, 0, $threadimageaid);
                } else {
                    $rtn = setthreadcover($pid, $_G['tid'], 0, 1);
                }
                if (!$rtn) {
                    preg_match_all("/(\[img\]|\[img=\d{1,4}[x|\,]\d{1,4}\])\s*([^\[\<\r\n]+?)\s*\[\/img\]/is", $message, $imglist, PREG_SET_ORDER);
                    $values['coverimg'] = "<p id=\"showsetcover\">" . "<span id=\"setcoverwait\"></span></p><script>if($('forward_a')){\$('forward_a').style.display='none';setTimeout(\"$('forward_a').style.display=''\", 5000);};ajaxget('forum.php?mod=ajax&action=setthreadcover&tid=$_G[tid]&pid=$pid&fid=$_G[fid]&imgurl={$imglist[0][2]}&newthread=1', 'showsetcover', 'setcoverwait')</script>";
                    $param['clean_msgforward'] = 1;
                    $param['timeout'] = $param['refreshtime'] = 15;
                    showmessage(lang('plugin/singcere_waterfall', 'post_edit_succeed'), "forum.php?mod=viewthread&tid=$_G[tid]", $values, $param);
                }
            }
            return true;
        } else if ($msg == 'post_reply_succeed' && $_GET['handlekey'] == 'k_reply') {
            showmessage(lang('plugin/singcere_waterfall', 'reply_success'), null, array(), array('showdialog' => 0, 'showmsg' => false, 'closetime' => 0.0001,
              'extrajs' => '<script>addreply(\'' . $_G['tid'] . '\', ' . $_G['uid'] . ', \'' . $_G['member']['username'] . '\', \'' . $message . '\'); updatetotalnum(\'' . $_G['tid'] . '\', 0, 0, 0, 1);</script>'));
        }
    }

    function collection_edit_inwf_message($params) {
        list($msg, $url_forward, $values, $extraparam) = $params['param'];
        if ($msg == 'collection_collect_succ' && $_GET['handlekey'] == 'k_coll') {
            showmessage('collection_collect_succ', null, array(), array('alert' => 'right', 'closetime' => 1, 'showdialog' => 1,
              'extrajs' => '<script>updatetotalnum(\'' . $_GET['tid'] . '\', 1, 0, 0, 0,' . TIMESTAMP . ');</script>'));
        }
    }

}