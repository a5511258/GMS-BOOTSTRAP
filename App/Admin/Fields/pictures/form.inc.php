<?php

$from_return='';

$extra=unserialize($field_one['extra']);
$extra_show="";



//新增时
if($G_from_type=='add'){
	if($extra['updata_type']=='0'){
		$from_return='<input id="img_'.$field_one['name'].'_box" name="'.$field_one['name'].'" value="'.$field_one['value'].'" class="easyui-textbox" data-options="buttonText:\'选择\',buttonIcon:\'iconfont icon-pic\',prompt:\'上传图片...\',onClickButton:function(){updata_image(\'img_'.$field_one['name'].'_box\')}" style="width:250px;height:30px;" >';
	}else{
		$from_return='<div class="t_img_box"><img src="'.$field_one['value'].'" /></div><input id="img_'.$field_one['name'].'_box" name="'.$field_one['name'].'" value="'.$field_one['value'].'" class="easyui-textbox" data-options="buttonText:\'选择\',buttonIcon:\'iconfont icon-pic\',prompt:\'上传图片...\',onClickButton:function(){updata_image(\'img_'.$field_one['name'].'_box\')}" style="width:250px;height:30px;" >';
	}
}
//修改时
if($G_from_type=='edit'){
	if($extra['updata_type']=='0'){
		$from_return='<input id="img_'.$field_one['name'].'_box" name="'.$field_one['name'].'" value="{$_info["'.$field_one['name'].'"]}" class="easyui-textbox" data-options="buttonText:\'选择\',buttonIcon:\'iconfont icon-pic\',prompt:\'上传图片...\',onClickButton:function(){updata_image(\'img_'.$field_one['name'].'_box\')}" style="width:250px;height:24px;">';
	}else{
		$from_return='<div class="t_img_box"><img src="{$_info["'.$field_one['name'].'"]}" /></div><input id="img_'.$field_one['name'].'_box" name="'.$field_one['name'].'" value="{$_info["'.$field_one['name'].'"]}" class="easyui-textbox" data-options="buttonText:\'选择\',buttonIcon:\'iconfont icon-pic\',prompt:\'上传图片...\',onClickButton:function(){updata_image(\'img_'.$field_one['name'].'_box\')}" style="width:250px;height:24px;">';
	}
}
//查询时
if($G_from_type=='search'){
	$from_return='<input name="s_'.$field_one['name'].'" type="text" class="easyui-textbox" style="height:30px;">';
}
return $from_return;