<?php

$from_return='';

$extra=unserialize($field_one['extra']);
$extra_show="";

if($extra['min']!=''){//最小值
	if($extra['unsifned']!=''){//是否有符号
		if($extra['min']<0){
			$extra['min']=0;
		}
		$extra_show=$extra_show.'min:\''.$extra['min'].'\',';
	}else{
		$extra_show=$extra_show.'min:\''.$extra['min'].'\',';
	}
}

if($extra['max']!=''){//最大值
	if($extra['unsifned']!=''){//是否有符号
		if($extra['max']>0){
			$extra_show=$extra_show.'max:\''.$extra['max'].'\',';
		}
	}else{
		$extra_show=$extra_show.'max:\''.$extra['max'].'\',';
	}
}

if($extra['precision']!=''){//最大精度（只有数据库有小数时才显示最大精度）
	$extra_show=$extra_show.'precision:\''.$extra['precision'].'\',';
}

if($extra['decimalSeparator']!=''){//小数分隔符
	$extra_show=$extra_show.'decimalSeparator:\''.$extra['decimalSeparator'].'\',';
}
if($extra['groupSeparator']!=''){//千位分隔符符号
	$extra_show=$extra_show.'groupSeparator:\''.$extra['groupSeparator'].'\',';
}
if($extra['prefix']!=''){//前缀字符串
	$extra_show=$extra_show.'prefix:\''.$extra['prefix'].'\',';
}
if($extra['suffix']!=''){//后缀字符串
	$extra_show=$extra_show.'suffix:\''.$extra['suffix'].'\',';
}
if($G_from_type!='search'){
	if($extra['required']==1){//是否必填
		$extra_show=$extra_show.'required:true';
	}else{
		$extra_show=$extra_show.'required:false';
	}
}

if($G_from_type=='add'){
	$from_return='<input name="'.$field_one['name'].'" value="'.$field_one['value'].'" type="text" class="easyui-numberbox" style="height:30px;" data-options="'.$extra_show.'">';
}elseif($G_from_type=='edit'){
	$from_return='<input name="'.$field_one['name'].'" value="{$_info["'.$field_one['name'].'"]}" type="text" class="easyui-numberbox" style="height:30px;" data-options="'.$extra_show.'">';
}else{
	$from_return='<input name="s_'.$field_one['name'].'" type="text" class="easyui-numberbox" style="height:30px;" data-options="'.$extra_show.'required:false">';
}
return $from_return;