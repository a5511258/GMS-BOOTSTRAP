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
			text-align: left;
			padding: 6px;
			margin: 3px;
			height: 30px;
			/*background-color: #EEEEEE;*/
		}
		.fitem:nth-child(2n){
			/*background-color: #F2F8FB;*/
		}
	</style>

	<div class="fixed-bar" id="AuthGroup_Bar" style="height: auto">

		<div class="item-title">
			<div>
				<form class="search_from">
					<table>
						<tr>
							<td>角色名称 : </td>
							<td><input id="s_title" name="s_title" type="text" class="easyui-combobox" style="height:25px;"></td>
							<td><a href="javascript:;" class="easyui-linkbutton" onclick="click_search()"
								   data-options="iconCls:'icon-search',plain:true">查询</a></td>
							<td><?php if(Is_Auth('Admin/AuthGroup/add')): ?><a href="javascript:;" class="easyui-linkbutton" onclick="click_add()"
								   data-options="iconCls:'icon-add',plain:true">添加</a><?php endif; ?></td>
							<td><a href="javascript:;" class="easyui-linkbutton" onclick="click_redo()"
								   data-options="iconCls:'icon-redo',plain:true">重置</a></td>
						</tr>
					</table>
				</form>
			</div>

			<!--<h3>行为日志</h3>-->
			<!--<ul class="tab-base">-->
			<!--<li><a class="current" href="JavaScript:void(0);" onclick="Data_Reload('ActionLog_Data_List');"><span>列表</span></a></li>-->
			<!--<li><a href="JavaScript:void(0);" onclick="Data_Search('ActionLog_Search_From','ActionLog_Data_List');"><span>搜索</span></a></li>-->
			<!--</ul>-->
		</div>



	<!--<div class="item-title">-->
		<!--<h3>角色</h3>-->
		<!--<ul class="tab-base">-->
			<!--<li><a class="current" href="JavaScript:void(0);" onclick="Data_Reload('AuthGroup_Data_List');"><span>列表</span></a></li>-->
			<!--<li><a href="JavaScript:void(0);" onclick="Data_Search('AuthGroup_Search_From','AuthGroup_Data_List');"><span>搜索</span></a></li>-->
			<!--<?php if(Is_Auth('Admin/AuthGroup/add')): ?>-->
			<!--<li><a href="<?php echo U('Admin/AuthGroup/add');?>"><span>新增</span></a></li>-->
			<!--<?php endif; ?>-->
		<!--</ul>-->
	<!--</div>-->
</div>


	<div id="div_dialog1" class="easyui-dialog" data-options="closed:true,buttons:'#div-buttons',iconCls:'icon-user_gray',modal:true" title="角色管理"
		 style="width: 400px; height: auto; padding: 5px 10px;">
		<div id="div_ftitle" class="ftitle"></div>
		<form id="auth_group_from" method="post">
				<div class="fitem">
					<label>角色名称：</label>
					<input id="inp_id" name="id" type="hidden"/>
					<input id="inp_title" name="title" class="easyui-textbox" style="height:30px;width: 180px" data-options="required:true"/>
					<span style="color: #CC0000;margin-left: 5px;line-height: inherit;text-align:center;font-weight: bold;">*</span>
				</div>
				<div class="fitem">
					<label>角色权限：</label>
					<input id="inp_rules" type="text" name="rules[]" class="easyui-combotree" style="height:30px;width: 180px;" data-options="value:'',url:'<?php echo U("Admin/Function/get_auth_rule");?>&r_type=json',valueField:'id',textField:'text',multiple:true,cascadeCheck:false,required:true,editable:false"/>
					<span style="color: #CC0000;margin-left: 5px;line-height: inherit;text-align:center;font-weight: bold;">*</span>
				</div>
				<div class="fitem">
					<label>角色状态：</label>
					<input id="inp_status" name="status" class="easyui-combobox" style="height:30px;width: 180px" data-options="value:'',url:'<?php echo U("Function/get_status_option");?>',valueField:'id',textField:'text',multiple:false,required:true,editable:false,panelHeight:'auto'"/>
					<span style="color: #CC0000;margin-left: 5px;line-height: inherit;text-align:center;font-weight: bold;">*</span>
				</div>
				<div class="fitem">
					<label>排序：</label>
					<input id="inp_sort" name="sort" class="easyui-numberbox" style="height:30px;width: 180px;" data-options="precision:'0',decimalSeparator:'.',groupSeparator:',',required:false"/>
				</div>
		</form>
		<div id="div-buttons">
			<a href="javascript:void(0)" class="easyui-linkbutton" iconcls="icon-ok" onclick="operateSure()">确定</a>
			<a href="javascript:void(0)" class="easyui-linkbutton" iconcls="icon-cancel" onclick="operateCancel()">取消</a>
		</div>
	</div>



<table id="AuthGroup_Data_List"></table>

<script type="text/javascript">

	var operateFlag = undefined;

	$(function() {

		$("#s_title").combobox({
			url:"<?php echo U('Function/get_auth_group');?>"+"&r_type=json",
			editable: true,
			multiple:false,
			valueField: 'text',
			textField: 'text',
			panelHeight:100
		});

		$("#AuthGroup_Data_List").datagrid({
			url : "<?php echo U('AuthGroup/index');?>",
			fit : true,
			striped : true,
			border : false,
			pagination : true,
			pageSize : 20,
			pageList : [ 10, 20, 50 ],
			pageNumber : 1,
			sortName : 'id',
			sortOrder : 'desc',
			toolbar : '#AuthGroup_Bar',
			singleSelect : true,
			columns : [[
				{field : 'id',title : 'ID',width : 40,sortable:true},
				{field : "title",title : "角色名称",width :200,sortable:true},
				{field : "status",title : "角色状态",width :100,sortable:true,
					formatter: function (value, row, index) {
						var op_status=new Array()
						op_status["0"]="禁用"
						op_status["1"]="启用"
						op_status["2"]="审核中"
						return op_status[value];
						}},
				{field : "operate",title : "操作",width : 200,
					formatter: function (value, row, index) {
						operate_menu='';

						if(row.editable == 1){


							<?php if(Is_Auth('Admin/AuthGroup/edit')): ?>operate_menu = operate_menu+"<a href='#' onclick='click_edit(\""+row.id+"\")' ><img style='border-style:none;width: 18px;vertical-align: bottom' src='/CourtGms/Public/Static/Easyui/themes/icons/edit_add3.png'>&nbsp;编辑&nbsp;</a>";<?php endif; ?>

						}


						if(row.editable == 1){

							<?php if(Is_Auth('Admin/AuthGroup/del')): ?>operate_menu = operate_menu+" | <a href='#' onclick=\"Data_Remove('<?php echo U('del'); ?>&id="+row.id+"','AuthGroup_Data_List');\"><img style='border-style:none; width: 18px;vertical-align: bottom' src='/CourtGms/Public/Static/Easyui/themes/icons/delete2.png'>&nbsp;删除&nbsp;</a>";<?php endif; ?>

						}

						return operate_menu;

				}}
			]]
		});
	})


	function click_add() {

		$("#auth_group_from").form('clear');
		operateFlag = 'add';
		$("#div_ftitle").text("角色添加");

		$('#inp_status').combobox('setValue','请选择');
		window.setTimeout(function(){$("#inp_title").focus();},500);
		$("#div_dialog1").dialog('center');
		$("#div_dialog1").dialog('open');
	}
	function fillForm(obj) {

//		console.log(obj);
		if (obj) {

			$("#inp_id").val(obj.id);

			$('#inp_title').textbox('setValue',obj.title);

			$('#inp_rules').combotree('setValues',obj.rules);

			$('#inp_status').combobox('setValue',obj.status);

			$('#inp_sort').numberbox('setValue',obj.sort);

		}
	}

	function click_edit(row_id) {
		$("#auth_group_from").form('clear');

		operateFlag = 'edit';
		$("#div_ftitle").text("角色编辑");
		if(row_id){

			console.log(row_id);
			$.ajax({
				url: "<?php echo U('AuthGroup/edit');?>",
				type: 'get',
				dataType: 'Json',
				data: {
					id: row_id
				}
			}).done(function (result) {
				if (result.Code == 200) {
					fillForm(result.Result);
				} else {
					$.messager.alert('错误','数据错误','error');
				}
			});
		}
		window.setTimeout(function(){$("#inp_title").focus();},500);
		$("#div_dialog1").dialog('center');
		$("#div_dialog1").dialog('open');
	}

	function operateSure() {


		var action = operateFlag == "add" ? "add" : "edit";

		$("#auth_group_from").form('submit', {
			url: "<?php echo U('AuthGroup');?>&a="+action,
			onSubmit: function () {
				var flag = true;
				flag = flag && $("#inp_title").textbox('isValid');
				if(!flag){
					$.messager.alert('提示',"角色名称为必填内容,请填写", 'info');
					return flag;
				}
				flag = flag && $("#inp_rules").combotree('isValid');
				if(!flag){
					$.messager.alert('提示',"角色权限为必填内容,请填写", 'info');
					return flag;
				}
				flag = flag && $("#inp_status").combotree('isValid');
				if(!flag){
					$.messager.alert('提示',"角色状态为必填内容,请填写", 'info');
					return flag;
				}
				var str =  $("#inp_status").combotree('getValue');
				if('请选择' == str){
					$.messager.alert('提示',"角色状态为必填内容,请填写", 'info');
					return false;
				}

			},
			success: function (data) {
				var jsonResult = $.parseJSON(data);
				if (jsonResult.Code == 200) {
					if (jsonResult.Result) {
						$("#AuthGroup_Data_List").datagrid('reload');
						$("#div_dialog1").dialog('close');
						$.messager.show({
							title:'消息提示',
							msg:jsonResult.Msg,
							timeout:3000,
							showType:'slide'
						});
					} else {
						$.messager.alert("提示", jsonResult.Msg, 'error');
					}
				}
				else {
					$.messager.alert("提示", jsonResult.Msg, 'error');
				}
			}
		});
	}

	function operateCancel() {
		$("#auth_group_from").form('clear');

		$("#div_dialog1").dialog('close');
	}


	function click_search() {
		var queryParams = $('#AuthGroup_Data_List').datagrid('options').queryParams;

		var s_title = $('#s_title').combobox('getValue');

		console.log('s_title =>' + s_title);
		queryParams['s_title'] = s_title.toString();
		console.log('queryParams[s_title] =>' + queryParams['s_title']);



		$('#AuthGroup_Data_List').datagrid('reload');
	}


	function click_redo() {

		$('#s_title').combobox('setValue','');

		$('#s_status').combobox('setValue',1);

		$('#AuthGroup_Data_List').datagrid({queryParams: {}});
	}


</script>




</body>
</html>