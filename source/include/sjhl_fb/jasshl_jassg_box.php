<?php
function jassg_box($message){
	global $jassn,$sjh_setup;
	
	$inc = '';
	if($jassn == 0){
		include_once('jasshl_css.php');
		include_once('jasshl_js.php');
		$inc .= '<style type="text/css">'.$sjh_css.'</style>';
		$inc .= '<script type="text/javascript">'.$sjh_js.'</script>';
	}
	
	$strf1='<div class="sjhblock">
<span class="sjhzoom" onclick="sjh_swith(';
	$strf2 = ')">&nbsp;+&nbsp;</span>
<span class="sjhcopyrt">Shingo Jass Highlighter '.$sjh_setup['vs'].'</span>
<div class="sjhcode" id="sjh';
	$strf3='">';
	$strb='</div></div>';
	
	$jassn++;
	$message=htmlspecialchars_decode($message,ENT_QUOTES);
	if(SJHLBBN == 'dz'){
		$message=str_replace(array('&nbsp;','<br />'),array(' ',''),$message);
	}else{
		$message=str_replace(array('&nbsp;','<br />'),array(' ',"\n"),$message);
	}
	$message=jassc($message);
	$message=$inc.$strf1.$jassn.$strf2.$jassn.$strf3.$message.$strb;
	return $message;
}
?>