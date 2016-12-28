<?php

$from_return='';

$extra=unserialize($field_one['extra']);
$extra_show="";

if($extra['multiple']==1){
	$input_type="radio";
}else{
	$input_type="checkbox";
}
$extra_show='editable:'.$extra['editable'];

//新增时
if($G_from_type=='add'){
	$ops = model_field_attr($extra['option']);
	foreach ($ops as $opkey=>$opkeyval){
		$from_return=$from_return.'<label for="'.$field_one['name'].'_'.$opkey.'"><input name="'.$field_one['name'].'" id="'.$field_one['name'].'_'.$opkey.'" type="'.$input_type.'" value="'.$opkey.'"';
		if($field_one['value']==$opkey){
			$from_return=$from_return.' checked="checked"';
		}
		$from_return=$from_return.'/> '.$opkeyval.'</label>';
	}
}elseif($G_from_type=='edit'){
	$ops = model_field_attr($extra['option']);
	foreach ($ops as $opkey=>$opkeyval){
		$from_return=$from_return.'<label for="'.$field_one['name'].'_'.$opkey.'"><input name="'.$field_one['name'].'" id="'.$field_one['name'].'_'.$opkey.'" type="'.$input_type.'" value="'.$opkey.'"<?php if($_info["'.$field_one['name'].'"]=='.$opkey.'){ ?> checked="checked"<?php }?>/> '.$opkeyval.'</label>';
	}
}else{
	$ops = model_field_attr($extra['option']);
	foreach ($ops as $opkey=>$opkeyval){
		$from_return=$from_return.'<label for="'.$field_one['name'].'_'.$opkey.'"><input name="'.$field_one['name'].'" id="'.$field_one['name'].'_'.$opkey.'" type="'.$input_type.'" value="'.$opkey.'"/> '.$opkeyval.'</label>';
	}
}
return $from_return;