<?php

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
    exit('Access Denied');
}

showformheader('index');
showtableheader('home_notes', 'fixpadding"', '', '3');
foreach(C::t('common_adminnote')->fetch_all_by_access(0) as $note) {
    if($note['expiration'] < TIMESTAMP) {
        C::t('common_adminnote')->delete($note['id']);
    } else {
        $note['adminenc'] = rawurlencode($note['admin']);
        $note['expiration'] = ceil(($note['expiration'] - $note['dateline']) / 86400);
        $note['dateline'] = dgmdate($note['dateline'], 'dt');
        showtablerow('', array('', '', ''), array(
            $isfounder || $_G['member']['username'] == $note['admin'] ? '<a href="'.ADMINSCRIPT.'?action=index&notesubmit=yes&noteid='.$note['id'].'"><img src="static/image/admincp/close.gif" width="7" height="8" title="'.cplang('delete').'" /></a>' : '',
            "<span class=\"bold\"><a href=\"home.php?mod=space&username=$note[adminenc]\" target=\"_blank\">@$note[admin]</a></span>: $note[message] | $note[dateline] (".cplang('validity').": $note[expiration] ".cplang('days').")",
        ));
    }
}
showtablerow('', array(), array(
    cplang('home_notes_add'),
    '<input type="text" class="txt" name="newmessage" value="" style="width:300px;" />'.cplang('validity').': <input type="text" class="txt" name="newexpiration" value="30" style="width:30px;" />'.cplang('days').'&nbsp;<input name="notesubmit" value="'.cplang('submit').'" type="submit" class="btn" />'
));
showtablefooter();
showformfooter();