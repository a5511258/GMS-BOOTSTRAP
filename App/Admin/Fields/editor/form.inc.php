<?php

$from_return='';

$extra=unserialize($field_one['extra']);

if($G_from_type=='add'){
	$from_return='<textarea id="editor_'.$field_one['name'].'" name="'.$field_one['name'].'" config_date="'.$extra['config'].'" style="width:'.$extra['width'].';height:'.$extra['height'].';" class="easyui-kindeditor">'.$field_one['value'].'</textarea>';
}elseif($G_from_type=='edit'){
	$from_return='<textarea id="editor_'.$field_one['name'].'" name="'.$field_one['name'].'" config_date="'.$extra['config'].'" style="width:'.$extra['width'].';height:'.$extra['height'].';" class="easyui-kindeditor">{$_info["'.$field_one['name'].'"]}</textarea>';
}else{
	$from_return='<input name="s_'.$field_one['name'].'" type="text" class="easyui-textbox" style="height:30px;">';
}

return $from_return;