<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <title> <?php echo C('SOFT_NAME');?>|Gms管理系统</title>
    <link href="/TobaccoGms1/Public/Admin/images/favicon.ico" mce_href="/TobaccoGms1/Public/Admin/images/favicon.ico" rel="bookmark" type="image/x-icon" /> 
    <link href="/TobaccoGms1/Public/Admin/images/favicon.ico" mce_href="/TobaccoGms1/Public/Admin/images/favicon.ico" rel="icon" type="image/x-icon" /> 
    <link href="/TobaccoGms1/Public/Admin/images/favicon.ico" mce_href="/TobaccoGms1/Public/Admin/images/favicon.ico" rel="shortcut icon" type="image/x-icon" />
    <link rel="stylesheet" type="text/css" href="/TobaccoGms1/Public/Static/Easyui/themes/metro-gms/easyui.css">
    <link rel="stylesheet" type="text/css" href="/TobaccoGms1/Public/Static/themes/default.css">
    <link rel="stylesheet" type="text/css" href="/TobaccoGms1/Public/Static/Font/iconfont.css">
    <link rel="stylesheet" type="text/css" href="/TobaccoGms1/Public/Static/Easyui/themes/icon.css">

    <link rel="stylesheet" type="text/css" href="/TobaccoGms1/Public/Static/kindeditor/themes/default/default.css" />
    <link rel="stylesheet" type="text/css" href="/TobaccoGms1/Public/Static/Easyui/themes/color.css">
    <link rel="stylesheet" href="/TobaccoGms1/Public/Admin/css/skin.css" />
    <script type="text/javascript" src="/TobaccoGms1/Public/Static/Jquery/jquery.min.js"></script>
    <script type="text/javascript" src="/TobaccoGms1/Public/Static/Easyui/jquery.easyui.min.js"></script>
    <script type="text/javascript" src="/TobaccoGms1/Public/Static/Easyui/locale/easyui-lang-zh_CN.js"></script>
    <script charset="utf-8" src="/TobaccoGms1/Public/Static/kindeditor/kindeditor-min.js"></script>
    <script charset="utf-8" src="/TobaccoGms1/Public/Static/kindeditor/lang/zh_CN.js"></script>
    <script charset="utf-8" src="/TobaccoGms1/Public/Static/Echarts/echarts.js"></script>
    <script charset="utf-8" src="/TobaccoGms1/Public/Admin/js/base.js" /></script><script>
	var ke_pasteType=2;
	var ke_fileManagerJson="<?php echo U('Admin/FilesUpdata/filemanager');?>";
	var ke_uploadJson="<?php echo U('Admin/FilesUpdata/upload');?>";
	var ke_Uid='<?php echo session(C("AUTH_KEY"));;?>';
	var Root='/TobaccoGms1';
	</script>
</head>
<body>
<div class="fixed-bar" id="AuthRule_Bar" style="height: auto">
	<div class="item-title">
		<h3>菜单</h3>
		<ul class="tab-base">
			<?php if(Is_Auth('Admin/AuthRule/index')): ?><li><a href="<?php echo U('Admin/AuthRule/index');?>"><span>列表</span></a></li><?php endif; ?>
			<?php if(Is_Auth('Admin/AuthRule/add')): ?><li><a href="<?php echo U('Admin/AuthRule/add');?>"><span>新增</span></a></li><?php endif; ?>
			<li><a class="current" href="#"><span>更新</span></a></li>
		</ul>
	</div>
</div>
<form id="AuthRule_Form" method="post">
<table class="table tb-type2 nobdb">
	<tbody>
	<tr>
			<td colspan="2" class="required"><label for="for_name">上级菜单:</label></td>
		</tr>
		<tr class="noborder">
			<td class="vatop rowform"><select name="pid" class="easyui-combotree" style="height:30px;" data-options="value:'<?php echo ($_info["pid"]); ?>',url:'<?php echo U('Admin/Function/get_auth_rule');?>&pid=-1&r_type=json',multiple:false,required:false,editable:false"></select></td>
			<td class="vatop tips"></td>
		</tr><tr>
			<td colspan="2" class="required"><label for="for_name">标题:</label></td>
		</tr>
		<tr class="noborder">
			<td class="vatop rowform"><input name="title" type="text" class="easyui-textbox" style="height:30px;" value="<?php echo ($_info["title"]); ?>" data-options="required:false"></td>
			<td class="vatop tips"></td>
		</tr><tr>
			<td colspan="2" class="required"><label for="for_name">节点:</label></td>
		</tr>
		<tr class="noborder">
			<td class="vatop rowform"><input name="name" type="text" class="easyui-textbox" style="height:30px;" value="<?php echo ($_info["name"]); ?>" data-options="required:false"></td>
			<td class="vatop tips"></td>
		</tr><tr>
			<td colspan="2" class="required"><label for="for_name">图标:</label></td>
		</tr>
		<tr class="noborder">
			<td class="vatop rowform"><select name="icon" id="for_icon" class="easyui-combobox" style="height:30px;" data-options="value:'<?php echo ($_info["icon"]); ?>',url:'<?php echo U("Admin/Function/get_icon");?>&r_type=json',valueField:'id',textField:'text',multiple:false,required:false,editable:false,    
        formatter: function(row){
            var opts = $(this).combobox('options');
            return '<i class=\'iconfont '+row[opts.textField]+'\'></i> '+row[opts.textField];
        }"></select></td><td class="vatop tips"></td>
		</tr><tr>
			<td colspan="2" class="required"><label for="for_name">菜单类型:</label></td>
		</tr>
		<tr class="noborder">
			<td class="vatop rowform"><select name="type" class="easyui-combobox" style="height:30px;" data-options="value:'<?php echo ($_info["type"]); ?>',multiple:false,required:false,editable:false"><option value="1" >节点</option><option value="2" >菜单</option><option value="3" >外链</option></select></td>
			<td class="vatop tips"></td>
		</tr><tr>
			<td colspan="2" class="required"><label for="for_name">隐藏:</label></td>
		</tr>
		<tr class="noborder">
			<td class="vatop rowform"><select name="hide" class="easyui-combobox" style="height:30px;" data-options="value:'<?php echo ($_info["hide"]); ?>',multiple:false,required:false,editable:false"><option value="0" >否</option><option value="1" >是</option></select></td>
			<td class="vatop tips"></td>
		</tr><tr>
			<td colspan="2" class="required"><label for="for_name">状态:</label></td>
		</tr>
		<tr class="noborder">
			<td class="vatop rowform"><select name="status" class="easyui-combobox" style="height:30px;" data-options="value:'<?php echo ($_info["status"]); ?>',multiple:false,required:false,editable:false"><option value="0" >禁用</option><option value="1" >启用</option></select></td>
			<td class="vatop tips"></td>
		</tr><tr>
			<td colspan="2" class="required"><label for="for_name">排序:</label></td>
		</tr>
		<tr class="noborder">
			<td class="vatop rowform"><input name="sort" value="<?php echo ($_info["sort"]); ?>" type="text" class="easyui-numberbox" style="height:30px;" data-options="precision:'0',decimalSeparator:'.',groupSeparator:',',required:false"></td>
			<td class="vatop tips">同级菜单有效</td>
		</tr><tr>
			<td colspan="2" class="required"><label for="for_name">附加规则:</label></td>
		</tr>
		<tr class="noborder">
			<td class="vatop rowform"><input name="condition" value="<?php echo ($_info["condition"]); ?>" type="text" class="easyui-textbox" data-options="required:false,multiline:true" style="width:300px; height:80px;"></td>
			<td class="vatop tips"></td>
		</tr>	</tbody>
	<tfoot>
		<tr class="tfoot">
			<td colspan="2"><a class="easyui-linkbutton" href="JavaScript:void(0);" onclick="$('#AuthRule_Form').submit();" data-options="iconCls:'iconfont icon-edit'"><span style="font-size: 14px; font-weight: 600;">提交</span></a></td>
		</tr>
	 </tfoot>
</table>
<input name="id" type="hidden" value="<?php echo I('get.id');?>" />
</form>
</body>
</html>