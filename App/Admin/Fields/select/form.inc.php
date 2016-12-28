<?php

//清空返回表单内容
$from_return='';
//解析字段参数
$extra=unserialize($field_one['extra']);
//清空扩展显示内容
$extra_show="";
//判断表单 类型（新增,更改,搜索）
if($G_from_type=='add'){
	$extra_show=$extra_show.'value:\''.$field_one['value'].'\',';//如果是新增,表单值为填写的默认值
}elseif($G_from_type=='edit'){
	$extra_show=$extra_show.'value:\'{$_info["'.$field_one['name'].'"]}\',';//如果是更改，表单值为{$_info["表单名称"]}
}else{
	$extra_show=$extra_show.'value:\'\',';//如果是搜索,表单值为空
}

//设置表单类型
$combo_type="combobox";//默认为combobox
if($extra['form_type']=='1'){//如果为表单类型值为1，即普通下拉表单 
	$f_value='valueField';
	$f_text='textField';
	
	if($extra['type']==2){//如果参数的值为2，是获取某一字段的配置信息 
		$extra_show=$extra_show.'url:\'{:U("Admin/Function/get_field_option")}&f_id='.$field_one['id'].'&r_type=json\',valueField:\'key\',textField:\'val\',';
	}elseif($extra['type']==3){//如果参数的值为3，是获取某一配置的参数
		$extra_option_arr = explode('|',$extra['option']);
		if($extra_option_arr[1]==''){
			$extra_option_arr[1]='type';
			$extra_option_arr[2]='value';
		}
		$extra_show=$extra_show.'url:\'{:U("Admin/Function/get_config")}&cname='.$extra['option'].'&r_type=json\','.$f_value.':\''.$extra_option_arr[1].'\','.$f_text.':\''.$extra_option_arr[2].'\',';
	}elseif($extra['type']==4){
		$extra_option_arr = explode('|',$extra['option']);
		$extra_show=$extra_show.'url:\'{:U("'.$extra_option_arr[0].'")}&r_type=json\','.$f_value.':\''.$extra_option_arr[1].'\','.$f_text.':\''.$extra_option_arr[2].'\',';
	}
}

if($extra['form_type']=='2' && $extra['type']=='4'){//如果为表单类型值为2，即树形菜单 且 参数的值为4，即获取方法返回的值
	$combo_type="combotree";//设置表单类型为 combotree
	$extra_option_arr = explode('|',$extra['option']);
	$extra_show=$extra_show.'url:\'{:U("'.$extra_option_arr[0].'")}&r_type=json\','.$f_value.':\''.$extra_option_arr[1].'\','.$f_text.':\''.$extra_option_arr[2].'\',';
}
$F_NAME=$field_one['name'];
if($extra['multiple']==1){//是否支持多选 1为支持
	$F_NAME=$F_NAME.'[]';
	$extra_show=$extra_show.'multiple:true,cascadeCheck:false,';
}else{
	$extra_show=$extra_show.'multiple:false,';
}

if($extra['required']==1){//是否必填 1为必填
	$extra_show=$extra_show.'required:true,';
}else{
	$extra_show=$extra_show.'required:false,';
}

if($extra['editable']=='true'){//是否允许手写输入 1为允许
	$extra_show=$extra_show.'editable:true';
}else{
	$extra_show=$extra_show.'editable:false';
}


if($G_from_type=='add'){
	$from_return = '<select name="'.$F_NAME.'" class="easyui-'.$combo_type.'" style="height:30px;" data-options="'.$extra_show.'">';
	if($extra['type']==1){//如果数据来源是固定的那么就直接分解数据写入表单
		$ops = model_field_attr($extra['option']);
		foreach ($ops as $opkey=>$opkeyval){
			$from_return=$from_return.'<option value="'.$opkey.'" >'.$opkeyval.'</option>';
		}
	}
	$from_return=$from_return.'</select>';
}elseif($G_from_type=='edit'){
	$from_return = '<select name="'.$F_NAME.'" class="easyui-'.$combo_type.'" style="height:30px;" data-options="'.$extra_show.'">';
	if($extra['type']==1){//如果数据来源是固定的那么就直接分解数据写入表单
		$ops = model_field_attr($extra['option']);
		foreach ($ops as $opkey=>$opkeyval){
			$from_return=$from_return.'<option value="'.$opkey.'" >'.$opkeyval.'</option>';
		}
	}
	$from_return=$from_return.'</select>';
}else{
	$from_return = '<select name="s_'.$F_NAME.'" class="easyui-'.$combo_type.'" style="height:30px;" data-options="'.$extra_show.'">';
	if($extra['type']==1){//如果数据来源是固定的那么就直接分解数据写入表单
		$ops = model_field_attr($extra['option']);
		$from_return=$from_return.'<option value="" >请选择一个选项</option>';
		foreach ($ops as $opkey=>$opkeyval){
			$from_return=$from_return.'<option value="'.$opkey.'" >'.$opkeyval.'</option>';
		}
	}
	$from_return=$from_return.'</select>';
}

return $from_return;