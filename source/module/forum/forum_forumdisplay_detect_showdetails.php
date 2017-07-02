<?php if(!defined('IN_DISCUZ')) exit('Access Denied');

$showoldetails = isset($_GET['showoldetails']) ? $_GET['showoldetails'] : '';
switch($showoldetails) {
    case 'no': dsetcookie('onlineforum', ''); break;
    case 'yes': dsetcookie('onlineforum', 1, 31536000); break;
}