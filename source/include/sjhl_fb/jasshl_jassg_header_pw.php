<?php
function jassg_header(){
	global $read,$foruminfo;
	if((!$foruminfo['allowencode']) || $read['ifsign'] != 1 || $foruminfo['allowhtm'])return true;
	return false;
}
?>