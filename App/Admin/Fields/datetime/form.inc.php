<?php

$from_return='';

$extra=unserialize($field_one['extra']);
$extra_show="";

if($extra['from_type']=='datebox'){
	$class='easyui-datebox';
	$date_Ymd='Y-m-d';
}else{
	$class='easyui-datetimebox';
	$date_Ymd='Y-m-d H:i:s';
}

if($extra['required']==1){//是否必填
	$extra_show=$extra_show.'required:true';
}else{
	$extra_show=$extra_show.'required:false';
}


if($G_from_type=='add'){
	$from_return='<input name="'.$field_one['name'].'" value="'.$field_one['value'].'" type="text" class="'.$class.'" style="height:30px;" data-options="'.$extra_show.'">';
}elseif($G_from_type=='edit'){
	$from_return='<input name="'.$field_one['name'].'" value="{$_info["'.$field_one['name'].'"]|date=\''.$date_Ymd.'\',###}" type="text" class="'.$class.'"style="height:30px;" data-options="'.$extra_show.'">';
}else{
	$from_return='<input name="s_'.$field_one['name'].'_min" type="text" class="'.$class.'" style="height:30px;"> - <input name="s_'.$field_one['name'].'_max" type="text" class="'.$class.'" style="height:30px;">';
}

return $from_return;