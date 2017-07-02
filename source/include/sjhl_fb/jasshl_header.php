<?php
//constant SJHLBBN = BBS NAME
if(function_exists('readover')){
	define('SJHLBBN','pw');
}elseif(defined('IN_DISCUZ')){
	define('SJHLBBN','dz');
}elseif(defined('IN_PHPBB')){
	define('SJHLBBN','pb');
}else{
	exit('UNKNOWN BBS SYSTEM!');
}
?>