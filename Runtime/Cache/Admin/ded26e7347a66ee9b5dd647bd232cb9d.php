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

		<style>
			.ftitle {
				font-size: 14px;
				font-weight: bold;
				padding: 5px 0;
				margin-bottom: 10px;
				border-bottom: 1px solid #ccc;
			}
			.fitem {
				margin-bottom: 10px;
			}
			.fitem label {
				display: inline-block;
				width: 100px;
				text-align:right;
			}
			.fitem {
				padding: 6px;
				margin: 3px;
				height: 30px;
				/*background-color: #EEEEEE;*/
			}
			.fitem:nth-child(2n){
				/*background-color: #F2F8FB;*/
			}
		</style>
		<div class="fixed-bar" id="ActionLog_Bar" style="height: auto">
			<div class="item-title">
				<a href="javascript:;" class="easyui-linkbutton" onclick="click_search()"
				   data-options="iconCls:'icon-search',plain:true">查询</a>

				<a href="javascript:;" class="easyui-linkbutton" onclick="click_redo('ActionLog_Data_List')"
				   data-options="iconCls:'icon-redo',plain:true">重置</a>

		<!--<h3>行为日志</h3>-->
		<!--<ul class="tab-base">-->
			<!--<li><a class="current" href="JavaScript:void(0);" onclick="Data_Reload('ActionLog_Data_List');"><span>列表</span></a></li>-->
			<!--<li><a href="JavaScript:void(0);" onclick="Data_Search('ActionLog_Search_From','ActionLog_Data_List');"><span>搜索</span></a></li>-->
		<!--</ul>-->
	</div>
		</div>
		<!--<form id="ActionLog_Form" class="update_from" style="width:600px; height:320px;"></form>-->
  <!--<form id="ActionLog_Search_From" class="search_from">-->
	<!--<table border="0" cellpadding="0" cellspacing="0" style="width:100%">-->
    	<!--<tr>-->
			<!--<th>行为名称 : </th>-->
			<!--<td><input name="s_action_id" type="text" class="easyui-textbox" style="height:30px;"></td>-->

			<!--<th>执行用户 : </th>-->
			<!--<td><input name="s_user_id" type="text" class="easyui-textbox" style="height:30px;"></td>-->
		<!--</tr>-->
	<!--</table>-->
  <!--</form>-->
		<table id="ActionLog_Data_List"></table>


		<div id="seach_dialog" class="easyui-dialog"
			 data-options="closed:true,buttons:'#seach-buttons',modal:true" title="日志查询"
			 style="width: auto; height: auto; padding: 5px 10px;">
			<form id="seach_form" method="post">

				<div style="width: 351px;float:left;">

					<div class="fitem">
						<label>行为名称:</label>
						<input id="s_action_id" name="s_action_id" class="easyui-combobox" style="height: 30px;width: 180px;"/>
					</div>
					<div class="fitem">
						<label>执行用户:</label>
						<input id="s_user_id" name="s_user_id" class="easyui-combobox" style="height: 30px;width: 180px;"/>
					</div>
				</div>

			</form>
			<div id="seach-buttons">
				<a href="javascript:void(0)" class="easyui-linkbutton" iconcls="icon-search"
				   onclick="SearchSure()">查询</a>
			</div>
		</div>


		<div id="view_dialog" class="easyui-dialog"
			 data-options="closed:true,modal:true" title="日志详情"
			 style="width:400px; height: auto;padding: 5px 10px;">
		</div>

		<script type="text/javascript">
			var log_info_url = "<?php echo U('edit');?>&id=";

			$(function() {
				$("#ActionLog_Data_List").datagrid({
					url : "<?php echo U('ActionLog/index');?>",
					fit : true,
					striped : true,
					border : false,
					pagination : true,
					pageSize : 20,
					pageList : [ 10, 20, 50 ],
					pageNumber : 1,
					sortName : 'id',
					sortOrder : 'desc',
					toolbar : '#ActionLog_Bar',
					singleSelect : true,
					columns : [[
						{field : 'id',title : 'ID',width : 40,sortable:true},
						{field : "action_id_show",title : "行为名称",width :100,sortable:true},
						{field : "remark",title : "行为内容",width :270,sortable:true},
						{field : "user_id_show",title : "执行用户",width :100,sortable:true},
						{field : "action_ip",title : "执行IP地址:",width :120,sortable:true},
						{field : "create_time",title : "执行时间",width :150,sortable:true,formatter: function (value, row, index) {
						return u_to_ymdhis(value)
					}},
						{field : "operate",title : "操作",width : 200,formatter: function (value, row, index) {
							operate_menu='';

//							<?php if(Is_Auth('Admin/ActionLog/edit')): ?>//							operate_menu = operate_menu+"<a href='#' onclick='viewinfo(\""+row.id+"\")'>&nbsp;&nbsp;<img style='border-style:none;width: 52px;' src='/CourtGms/Public/Static/Easyui/themes/icons/detail.png'></a>";
//<?php endif; ?>

							<?php if(Is_Auth('Admin/ActionLog/del')): ?>operate_menu = operate_menu+"<a href='#' onclick=\"Data_Remove('<?php echo U('del'); ?>&id="+row.id+"','ActionLog_Data_List');\">&nbsp;&nbsp;<img style='width: 52px;' src='/CourtGms/Public/Static/Easyui/themes/icons/delete1.png'></a>";<?php endif; ?>

							return operate_menu;
						}}
					]]
				});
			})

	function click_search() {

		$("#s_action_id").combobox({
			url:"<?php echo U('Function/searchAction');?>",
			editable: false,
			multiple:false,
			valueField: 'word_id',
			textField: 'word',
			panelHeight:'auto'
		});
		$("#s_user_id").combobox({
			url:"<?php echo U('Function/searchUser');?>",
			multiple:false,
			valueField: 'word_id',
			textField: 'word',
			panelHeight:100
		});

		$('#seach_dialog').dialog('center');
		$('#seach_dialog').dialog('open');

	}

	function SearchSure() {


		var queryParams = $('#ActionLog_Data_List').datagrid('options').queryParams;

		var action_ids = $('#s_action_id').combobox('getValue');

		var user_ids = $('#s_user_id').combobox('getValue');


		if(action_ids.length != 0){

			console.log('s_action_id =>' + action_ids);
			queryParams['s_action_id'] = action_ids.toString();
			console.log('queryParams[s_action_id] =>' + queryParams['s_action_id']);

		}

		if(user_ids.length != 0){

			console.log('s_user_id =>' + user_ids);

			queryParams['s_user_id'] = user_ids.toString();

			console.log('queryParams[s_user_id] =>' + queryParams['s_user_id']);
		}

		$('#ActionLog_Data_List').datagrid('reload');
	}


	function viewinfo(id) {
		$('#view_dialog').window('refresh',log_info_url+id);
		$('#view_dialog').window({
			top:(screen.height-750)/2,
			left:(screen.width-700)/2,
			resizable:false,
			collapsible:false,
			minimizable:false,
			maximizable:false
		});
		$('#view_dialog').window('open');
	}

	function click_redo(Datagrid_data) {

		$('#s_action_id').combobox('setValue','');

		$('#s_user_id').combobox('setValue','');

		$('#ActionLog_Data_List').datagrid({queryParams: {}});
	}
</script>
	
</body>
</html>