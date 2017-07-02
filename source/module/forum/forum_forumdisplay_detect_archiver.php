<?php

if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

if(!defined('IN_ARCHIVER')) {
    include template($template);
} else {
    include loadarchiver('forum/forumdisplay');
}