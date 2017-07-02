<?php
/*
This php program will convert jass text to a formed style.
To use this one you need to set a string variable and convey it to the function jassc.
The function jassc will return the formed jass text as a string.
------------------------------
(Unthinkable now!)Be care that the conveyed text should less than 5000 bytes.
If the text is a much longer,the function will cut the spare bytes.
The output string is html format.
------------------------------
The infomation about which things should be convert will write in jassstyle.txt .
The color and font style will be seen after every title.
If you find any mistakes or have any suggestions please e-mail:
shingoscar@yahoo.com.cn .
------------------------------
Made by shingoscar at 08.1.25
Version 0.41
*/
require_once('sjhl_fb/jasshl_header.php');

define('SEPARATOR',"\r\n");

require_once('sjhl_ab/sjhl_setup.php');
require_once('sjhl_ab/jasshl_ge.ab.php');
require_once('sjhl_ab/jasshl_cl.ab.php');

$jassn=0;//for count how much jass code in this page
$stack_string=array();
$stack_number=array();
$stack_comment=array();
//for record stack
$jassrep_mode=array('c'=>FALSE,'b'=>FALSE,'a'=>FALSE,'f'=>FALSE,'t'=>FALSE);
//mode c=common.j; b=blizzard.j; a=common.ai; f=auto format; t=title;

function jassc($conveyjt=''){
	global $ge_base,$colorjs,
	$stack_string,$stack_number,$stack_comment,
	$jassrep_mode,$sjh_setup;
	
	$regex_all=array(
		'comment'  => array(),
		'string'   => array(),
		'number'   => array(),
		'type'     => array(),
		'constant' => array(),
		'native'   => array(),
		'key'      => array()
	);
	
	$regex_all['comment'][]='/\/\/.*/';
	$regex_all['string'][]='/(?<!\\\)".*?(?<!\\\)"/';
	$regex_all['number'][]='/(?<!\w)[-+]?(?:\d*\.\d\d|\d+|0x[1-9]+)(?!\w)/';
	$regex_all['number'][]='/\'\w\w\w\w\'/';
	
	$regex_all['type']=$ge_base['type'];
	$regex_all['key']=$ge_base['key'];
	$regex_all['constant']=$ge_base['const'];

	if($jassrep_mode['c'] && $sjh_setup['cj']){
		include_once('sjhl_ab/jasshl_cj.ab.php');
		$jassrep_mode['c']=FALSE;
		$regex_all['native']=array_merge($regex_all['native'],$cj_base['native']);
		$regex_all['constant']=array_merge($regex_all['constant'],$cj_base['const']);
	}
	if($jassrep_mode['b'] && $sjh_setup['bj']){
		include_once('sjhl_ab/jasshl_bj.ab.php');
		$jassrep_mode['b']=FALSE;
		$regex_all['native']=array_merge($regex_all['native'],$bj_base['func']);
		$regex_all['constant']=array_merge($regex_all['constant'],$bj_base['const']);
	}
	if($jassrep_mode['a'] && $sjh_setup['ai']){
		include_once('sjhl_ab/jasshl_ai.ab.php');
		$jassrep_mode['a']=FALSE;
		$regex_all['native']=array_merge($regex_all['native'],$ai_base['native']);	
		$regex_all['native']=array_merge($regex_all['native'],$ai_base['func']);
		$regex_all['constant']=array_merge($regex_all['constant'],$ai_base['const']);
	}
	
	if(strpos($conveyjt,'</') !== FALSE){
		$conveyjt=preg_replace('/<(?:\w+(?:[^"\'>]|"[^"]*"|\'[^\']*\')*|\/\w+[^>]*)>/','',$conveyjt);
	}
	
	$conveyjt=preg_replace_callback($regex_all['string'],"jassc_callback_st",$conveyjt);
	$conveyjt=preg_replace_callback($regex_all['comment'],"jassc_callback_cm",$conveyjt);
	$conveyjt=preg_replace_callback($regex_all['number'],"jassc_callback_nu",$conveyjt);
	
	if($jassrep_mode['t'] && $sjh_setup['tm']){ //title
		$jassrep_mode['t']=FALSE;
		$conveyjt=preg_replace('/t\/(.*?)\/t/',"@tis@$1@tie@",$conveyjt);
	}
	
	if($jassrep_mode['f'] && $sjh_setup['af']){ //auto format
		$jassrep_mode['f']=FALSE;
		$indent='    ';
		$indent_level=0;
		$indent_stack=array();
		$indent_line=explode("\n",$conveyjt);
		$indent_match=array();
		foreach($indent_line as &$indent_value){
			$indent_value=trim($indent_value);
			//other format here
			$of_keyword=array('search'=>array(),'replace'=>array(),'searchregex'=>array(),'replaceregex'=>array());
			$of_keyword['search'][]='=';
			$of_keyword['search'][]='>';
			$of_keyword['search'][]='<';
			$of_keyword['search'][]='+';
			$of_keyword['search'][]='-';
			$of_keyword['search'][]='*';
			$of_keyword['search'][]='/';
			foreach($of_keyword['search'] as $of_value){
				$of_keyword['replace'][]=' '.$of_value.' ';
			}
			$of_keyword['search'][]='=  =';$of_keyword['replace'][]='==';
			$of_keyword['search'][]='! = ';$of_keyword['replace'][]=' !=';
			$of_keyword['search'][]='>  =';$of_keyword['replace'][]='>=';
			$of_keyword['search'][]='<  =';$of_keyword['replace'][]='<=';
			$of_keyword['search'][]='then';$of_keyword['replace'][]=' then';
			$of_keyword['searchregex'][]='{\s*\(\s*}';$of_keyword['replaceregex'][]='(';
			$of_keyword['searchregex'][]='{\s*\)\s*}';$of_keyword['replaceregex'][]=')';
			$of_keyword['searchregex'][]='{\s*\[\s*}';$of_keyword['replaceregex'][]='[';
			$of_keyword['searchregex'][]='{\s*\]\s*}';$of_keyword['replaceregex'][]=']';
			$of_keyword['searchregex'][]='{\s*\,\s*}';$of_keyword['replaceregex'][]=', ';
			$indent_value=preg_replace($of_keyword['searchregex'],$of_keyword['replaceregex'],$indent_value);
			$indent_value=str_replace($of_keyword['search'],$of_keyword['replace'],$indent_value);
			$indent_value=preg_replace('/\s\s+/',' ',$indent_value);
			//auto indent
			preg_match('/^([a-z]+)\s?/',$indent_value,$indent_match);
			switch($indent_match[1]){
				case 'function':
				case 'globals':
				case 'loop':
				case 'if':
					array_push($indent_stack,$indent_match[1]);
					for($i=0;$i<$indent_level;$i++){$indent_value=$indent.$indent_value;}
					$indent_level++;
					break;
				case 'else':
				case 'elseif':
					if(end($indent_stack)=='if'){
						for($i=0;$i<$indent_level-1;$i++){$indent_value=$indent.$indent_value;}
					}
					break;
				case 'endfunction':
					if(end($indent_stack)=='function'){
						array_pop($indent_stack);
						for($i=0;$i<$indent_level-1;$i++){$indent_value=$indent.$indent_value;}
						$indent_level--;
					}
					break;
				case 'endglobals':
					if(end($indent_stack)=='globals'){
						array_pop($indent_stack);
						for($i=0;$i<$indent_level-1;$i++){$indent_value=$indent.$indent_value;}
						$indent_level--;
					}
					break;
				case 'endloop':
					if(end($indent_stack)=='loop'){
						array_pop($indent_stack);
						for($i=0;$i<$indent_level-1;$i++){$indent_value=$indent.$indent_value;}
						$indent_level--;
					}
					break;
				case 'endif':
					if(end($indent_stack)=='if'){
						array_pop($indent_stack);
						for($i=0;$i<$indent_level-1;$i++){$indent_value=$indent.$indent_value;}
						$indent_level--;
					}
					break;
				default:
					for($i=0;$i<$indent_level;$i++){$indent_value=$indent.$indent_value;}
			}
		}
		$conveyjt=implode("\n",$indent_line);
	}
	
	$conveyjt=preg_replace_callback($regex_all['type'],"jassc_callback_ty",$conveyjt);
	$conveyjt=preg_replace_callback($regex_all['key'],"jassc_callback_ke",$conveyjt);
	$conveyjt=preg_replace_callback($regex_all['constant'],"jassc_callback_cs",$conveyjt);
	if(count($regex_all['native'])>0){
		$conveyjt=preg_replace_callback($regex_all['native'],"jassc_callback_na",$conveyjt);
	}
	
	while(count($stack_number)){
		$conveyjt=str_replace('@n'.count($stack_number).'@',array_pop($stack_number),$conveyjt);
	}
	while(count($stack_comment)){
		$conveyjt=str_replace('@c'.count($stack_comment).'@',array_pop($stack_comment),$conveyjt);
	}
	while(count($stack_string)){
		$conveyjt=str_replace('@s'.count($stack_string).'@',array_pop($stack_string),$conveyjt);
	}
	
	$conveyjt=htmlspecialchars($conveyjt,ENT_QUOTES);
	$conveyjt=str_replace(array(' ',"\n"),array('&nbsp;','<br />'),$conveyjt);
	$conveyjt=str_replace($colorjs['s'],$colorjs['r'],$conveyjt);
	
	return $conveyjt;
}

function jassc_callback_cm($match){
	global $stack_comment;
	array_push($stack_comment, $match[0]);
	return '@cms@@c'.count($stack_comment).'@@cme@';
}
function jassc_callback_st($match){
	global $stack_string;
	array_push($stack_string, $match[0]);
	return '@sts@@s'.count($stack_string).'@@ste@';
}
function jassc_callback_nu($match){
	global $stack_number;
	array_push($stack_number, $match[0]);
	return '@nus@@n'.count($stack_number).'@@nue@';
}
function jassc_callback_ty($match){return '@tys@'.$match[0].'@tye@';}
function jassc_callback_na($match){return '@nas@'.$match[0].'@nae@';}
function jassc_callback_cs($match){return '@css@'.$match[0].'@cse@';}
function jassc_callback_ke($match){return '@kes@'.$match[0].'@kee@';}

require_once('sjhl_fb/jasshl_jassg_header_'.SJHLBBN.'.php');
require_once('sjhl_fb/jasshl_jassg_box.php');

function jassg($message){
	global $sjh_setup;
	
	if(!$sjh_setup['ja'] || jassg_header())return $message;
	
	global ${$sjh_setup['fv']['var']};
	
	if(empty($sjh_setup['fv']['key'])){
		$sjh_forum=${$sjh_setup['fv']['var']};
	}else{
		$sjh_forum=${$sjh_setup['fv']['var']}[$sjh_setup['fv']['key']];
	}
	
	if(!in_array($sjh_forum,$sjh_setup['fv']['id']))return $message;

	if((strpos($message,'[jass]') !== FALSE) && (strpos($message,'[/jass]') !== FALSE) && $sjh_setup['um']){
		$message=preg_replace_callback("/\[jass\](.+?)\[\/jass\]/s", "jassg_callback", $message);
	}elseif((strpos($message,'j/') !== FALSE) && (strpos($message,'/j') !== FALSE) && $sjh_setup['sm']){
		$message=preg_replace_callback("/j\/(.+?)\/j([abcft]{0,5})/s", "jassg_callback", $message);
	}else{
		return $message;
	}
	return $message;
}

function jassg_callback($match){
	global $jassrep_mode;
	
	for($i=0;$i<strlen($match[2]);$i++){
		switch($match[2]{$i}){
			case 'c':$jassrep_mode['c']=TRUE;break;
			case 'b':$jassrep_mode['b']=TRUE;break;
			case 'a':$jassrep_mode['a']=TRUE;break;
			case 'f':$jassrep_mode['f']=TRUE;break;
			case 't':$jassrep_mode['t']=TRUE;break;
		}
	}
	
	$match[1]=jassg_box($match[1]);
	return $match[1];
}
?>