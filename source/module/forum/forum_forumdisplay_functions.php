<?php if(!defined('IN_DISCUZ')) exit('Access Denied');
function forumdisplay_verify_author($ids) {
    foreach(C::t('common_member_verify')->fetch_all($ids) as $value) {
        foreach($_G['setting']['verify'] as $vid => $vsetting) {
            if($vsetting['available'] && $vsetting['showicon'] && $value['verify'.$vid] == 1) {
                $srcurl = !empty($vsetting['icon']) ? $vsetting['icon'] : '';
                $verify[$value['uid']] .= "<a href=\"home.php?mod=spacecp&ac=profile&op=verify&vid=$vid\" target=\"_blank\">".(!empty($srcurl) ? '<img src="'.$srcurl.'" class="vm" alt="'.$vsetting['title'].'" title="'.$vsetting['title'].'" />' : $vsetting['title']).'</a>';
            }
        }

    }
    return $verify;
}