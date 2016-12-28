<?php

$from_return='';

$extra=unserialize($field_one['extra']);
$extra_show="";


if($G_from_type=='add'){
	$from_return='<input id="fields_'.$field_one['name'].'_box" name="'.$field_one['name'].'" value="'.$field_one['value'].'" class="easyui-textbox" data-options="buttonText:\'上传\',buttonIcon:\'iconfont icon-search\',prompt:\'上传文件...\',onClickButton:function(){updata_fields(\'fields_'.$field_one['name'].'_box\')}" style="width:250px;height:24px;" >';
}elseif($G_from_type=='edit'){
	$from_return='<input id="fields_'.$field_one['name'].'_box" name="'.$field_one['name'].'" value="{$_info["'.$field_one['name'].'"]}" class="easyui-textbox" data-options="buttonText:\'上传\',buttonIcon:\'iconfont icon-search\',prompt:\'上传文件...\',onClickButton:function(){updata_fields(\'fields_'.$field_one['name'].'_box\')}" style="width:250px;height:24px;" >';
}else{
	$from_return='<input name="s_'.$field_one['name'].'" type="text" class="easyui-textbox" style="height:30px;">';
}
return $from_return;