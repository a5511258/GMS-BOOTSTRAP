<?php

$from_return='';

$extra=unserialize($field_one['extra']);
$extra_show="";

if($extra['required']==1){//是否必填
	$extra_show=$extra_show.'required:true';
}else{
	$extra_show=$extra_show.'required:false';
}

if($G_from_type=='add'){
	$from_return='<input name="'.$field_one['name'].'" type="text" class="easyui-textbox" style="height:30px;" value="'.$field_one['value'].'" data-options="'.$extra_show.'">';
}elseif($G_from_type=='edit'){
	$from_return='<input name="'.$field_one['name'].'" type="text" class="easyui-textbox" style="height:30px;" value="{$_info["'.$field_one['name'].'"]}" data-options="'.$extra_show.'">';
}else{
	$from_return='<input name="s_'.$field_one['name'].'" type="text" class="easyui-textbox" style="height:30px;">';
}

return $from_return;