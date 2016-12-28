<?php

$from_return='';

$extra=unserialize($field_one['extra']);
$extra_show="";

if($extra['required']==1){//是否必填
	$extra_show=$extra_show.'required:true';
}else{
	$extra_show=$extra_show.'required:false';
}

if($extra['width']==''){
	$extra['width']='300px';
}
if($extra['height']==''){
	$extra['height']='80px';
}


if($G_from_type=='add'){
	$from_return='<input name="'.$field_one['name'].'" value="'.$field_one['value'].'" type="text" class="easyui-textbox" data-options="'.$extra_show.',multiline:true" style="width:'.$extra['width'].'; height:'.$extra['height'].';">';
}elseif($G_from_type=='edit'){
	$from_return='<input name="'.$field_one['name'].'" value="{$_info["'.$field_one['name'].'"]}" type="text" class="easyui-textbox" data-options="'.$extra_show.',multiline:true" style="width:'.$extra['width'].'; height:'.$extra['height'].';">';
}else{
	$from_return='<input name="s_'.$field_one['name'].'" type="text" class="easyui-textbox" style="height:30px;">';
}

return $from_return;