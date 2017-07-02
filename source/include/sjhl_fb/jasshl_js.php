<?php
$sjh_js = '
function sjh_swith(id){
	oJCode = document.getElementById("sjh" + id);
	if(oJCode.style.display == "none"){
		oJCode.style.display = "";
	}else{
		oJCode.style.display = "none";
	}
}
';
?>