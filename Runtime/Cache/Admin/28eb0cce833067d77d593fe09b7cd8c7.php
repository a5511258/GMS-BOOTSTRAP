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
				<li><a class="current" href="JavaScript:void(0);" onclick="Data_Reload('AuthRule_Data_List');"><span>列表</span></a></li>
				<?php if(Is_Auth('Admin/AuthRule/add')): ?><li><a href="<?php echo U('Admin/AuthRule/add');?>"><span>新增</span></a></li><?php endif; ?>
			</ul>
		</div>
	</div>
	<table id="AuthRule_Data_List"></table>
	<style>
		.tree-icon{ display:none}
	</style>
<script type="text/javascript">
$(function() {
	$("#AuthRule_Data_List").treegrid({
		url : "<?php echo U('AuthRule/index');?>",
		fit : true,
		striped : true,
		border : false,
		idField:'id',
		treeField:'title',
		pagination : false,
		toolbar : '#AuthRule_Bar',
		singleSelect : true,
		columns : [[
            {field : 'id',title : 'ID',width : 40,sortable:true},
{field : "title",title : "标题",width :180,sortable:true,formatter: function (value, row, index) {
			return "<i class='icon "+row['icon']+"'></i> "+value;
			}},
{field : "name",title : "节点",width :220,sortable:true},
{field : "type",title : "菜单类型",width :50,sortable:true,formatter: function (value, row, index) {
			var op_type=new Array()
			op_type["1"]="节点"
			op_type["2"]="菜单"
			op_type["3"]="外链"
			
			return op_type[value];
			}},
			{field : "hide",title : "隐藏",width :50,sortable:true,formatter: function (value, row, index) {
			var op_hide=new Array()
			op_hide["0"]="否"
			op_hide["1"]="是"
			
			return op_hide[value];
			}},
			{field : "status",title : "状态",width :50,sortable:true,formatter: function (value, row, index) {
			var op_status=new Array()
			op_status["0"]="禁用"
			op_status["1"]="启用"
			
			return op_status[value];
			}},
						{field : "operate",title : "操作",width : 200,formatter: function (value, row, index) {
				operate_menu='';
				
				<?php if(Is_Auth('Admin/AuthRule/add')): ?>operate_menu = operate_menu+"<a href='<?php echo U('add'); ?>&pid="+row.id+"' >新增子节点</a>";<?php endif; ?>
				
				<?php if(Is_Auth('Admin/AuthRule/edit')): ?>operate_menu = operate_menu+" | <a href='<?php echo U('edit'); ?>&id="+row.id+"' >编辑</a>";<?php endif; ?>

				<?php if(Is_Auth('Admin/AuthRule/del')): ?>operate_menu = operate_menu+" | <a href='#' onclick=\"Data_Remove('<?php echo U('del'); ?>&id="+row.id+"','AuthRule_Data_List');\">删除</a>";<?php endif; ?>

				return operate_menu;
			}}
		]]
	});
})
</script>

</body>
</html>