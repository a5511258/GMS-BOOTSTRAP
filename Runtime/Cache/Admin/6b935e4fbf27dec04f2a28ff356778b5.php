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

	<!--<div class="fixed-bar" id="User_Bar" style="height: auto">-->
	<!--<div class="item-title">-->
		<!--<h3>个人资料</h3>-->
		<!--<ul class="tab-base">-->
			<!--<?php if(Is_Auth('Admin/User/updatepassword')): ?>-->
			<!--<li><a href="<?php echo U('Admin/User/updatepassword');?>"><span>修改密码</span></a></li>-->
			<!--<?php endif; ?>-->
		<!--</ul>-->
	<!--</div>-->
<!--</div>-->
<!--<form id="User_Form" method="post">-->
<!--<table class="table tb-type2 nobdb">-->
	<!--<tbody>-->
	<!--<tr>-->
		<!--<td colspan="2" class="required"><label for="for_name">用户名:</label></td>-->
		<!--&lt;!&ndash;<td colspan="2" class="required"><label for="for_name">密码:</label></td>&ndash;&gt;-->
	<!--</tr>-->
	<!--<tr class="noborder">-->
		<!--<td class="vatop rowform" colspan="2"><input name="username" type="text" class="easyui-textbox" style="height:30px;" value="" data-options="value:'<?php echo ($_info["username"]); ?>',required:false,editable:false"></td>-->
		<!--&lt;!&ndash;<td class="vatop rowform" colspan="2"><input name="text" type="password" class="easyui-textbox" style="height:30px;" value="" data-options="required:false"></td>&ndash;&gt;-->
		<!--<td class="vatop tips"></td>-->
	<!--</tr>-->
	<!--<tr>-->
		<!--<td colspan="2" class="required"><label for="for_name">电话:</label></td>-->
	<!--</tr>-->
	<!--<tr class="noborder">-->
		<!--<td colspan="2" class="vatop rowform"><input name="phone" type="text" class="easyui-textbox" style="height:30px;" value="" data-options="value:'<?php echo ($_info["phone"]); ?>',required:false,editable:false"></td>-->
		<!--<td class="vatop tips"></td>-->
	<!--</tr>-->
	<!--<tr>-->
		<!--<td colspan="2" class="required"><label for="for_name">用户组:</label></td>-->
	<!--</tr>-->
	<!--<tr class="noborder">-->
		<!--<td class="vatop rowform"><input name="service_group_id" class="easyui-combotree" style="height:30px;" data-options="value:'<?php echo ($_info["service_group_id"]); ?>',url:'<?php echo U("Admin/Function/getGroupInfosToTree");?>',valueField:'id',textField:'text',required:false,editable:false,disabled:true"/></td>-->
		<!--<td class="vatop tips"></td>-->
	<!--</tr>-->
	<!--<tr>-->
		<!--<td colspan="2" class="required"><label for="for_name">状态:</label></td>-->
	<!--</tr>-->
	<!--<tr class="noborder">-->
		<!--<td class="vatop rowform"><select name="status" class="easyui-combobox" style="height:30px;" data-options="value:'1',url:'<?php echo U("Admin/Function/get_field_option");?>&f_id=83&r_type=json',valueField:'key',textField:'val',multiple:false,required:false,editable:false,disabled:true">-->
			<!--</select></td>-->
		<!--<td class="vatop tips"></td>-->
	<!--</tr>-->
	<!--<tr>-->
		<!--<td colspan="2" class="required"><label for="for_name">备注:</label></td>-->
	<!--</tr>-->
	<!--<tr class="noborder">-->
		<!--<td class="vatop rowform"><input name="remark" value="" type="text" class="easyui-textbox" data-options="value:'<?php echo ($_info["remark"]); ?>',required:false,multiline:true,editable:false" style="width:300px; height:80px;"></td>-->
		<!--<td class="vatop tips"></td>-->
	<!--</tr>-->
	<!--</tbody>-->
<!--</table>-->
<!--</form>-->
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


		<div class="easyui-tabs" data-options="fit:true,border:false" style="padding: 5px 10px;">
			<div title="用户信息" style="padding:10px;">
				<form id="edit_user_info" method="post" enctype="multipart/form-data">
					<div style="width:50%;float:left;">
						<div class="fitem">
							<label>用户名称：</label>
							<input id="edit_id" name="id" type="hidden" value="<?php echo ($_info['id']); ?>"/>
							<input name="username" type="text" class="easyui-textbox" style="height:30px;width: 180px" data-options="value:'<?php echo ($_info["username"]); ?>',editable:false,readonly:true"/>
						</div>
						<div class="fitem">
							<label>组织名称：</label>
							<input class="easyui-textbox" style="height:30px;width: 180px;" data-options="value:'<?php echo ($_info["group_name"]); ?>',disabled:true,editable:false"/>
						</div>
						<div class="fitem">
							<label>角色名称：</label>
							<input class="easyui-textbox" style="height:30px;width: 180px;" data-options="value:'<?php echo ($_info["auth_name"]); ?>',disabled:true,editable:false"/>
						</div>
						<div class="fitem">
							<label>邮箱：</label>
							<input id="inp_email" type="text" name="email" class="easyui-textbox" style="height:30px;width: 180px;" data-options="value:'<?php echo ($_info["email"]); ?>',validType:'email'"/>
						</div>
						<div class="fitem">
							<label>电话：</label>
							<input id="inp_phone" type="text" name="phone" class="easyui-numberbox" style="height:30px;width: 180px;" data-options="value:'<?php echo ($_info["phone"]); ?>',validType:'phone'"/>
						</div>
						<div class="fitem">
							<label>状态：</label>
							<input class="easyui-combobox" style="height:30px;width: 180px;" data-options="value:'<?php echo ($_info["status"]); ?>',url:'<?php echo U("Admin/Function/get_status_option");?>',valueField:'id',textField:'text',editable:false,disabled:true"/>
						</div>
					</div>
					<div style="width:50%;float:left;">
						<div>
							<label style="margin-left:20px;">用户头像：</label>
							<img id="edit_photo" alt="头像预览" style=" width:180px; height:170px;"/>
							<input id="edit_head_img"
								   style="margin-top: 10px;margin-left:10px;border-radius:10px;-moz-border-radius:10px;
                            -ms-border-radius:10px;-o-border-radius:10px;-webkit-border-radius:10px;border:1px solid #ccc;
                           font-size:10pt;padding:5px 10px;width: 270px;height: 26px" class="uploadbutton" type="file" name="user_photo"
								   accept="image/*" onchange=fileSelected() />
						</div>

						<div class="fitem">
							<label>备注：</label>
							<input id="inp_remark" type="text" name="remark" class="easyui-textbox" style="height:30px;width: 180px;" data-options="value:'<?php echo ($_info["remark"]); ?>'"/>
						</div>
					</div>

					<div class="fitem" style="text-align: right;background-color:#fff;">
						<a href="javascript:void(0)" class="easyui-linkbutton" iconcls="icon-ok"
						   onclick="operatsure('edit')">确定</a>
					</div>
				</form>
			</div>
			<div title="修改密码"style="padding:10px;">
				<form id="Updatepassword" method="post">
					<div style="width:50%;float:left;">
						<div class="fitem">
							<label>原密码：</label>
							<input id="inp_user_id" name="id" type="hidden"/>
							<input id="inp_old_password" name="old_password" type="password" class="easyui-textbox" style="height:30px;width: 180px" data-options="required:true,validType:'minLength[5]'"/>
							<span style="color: #CC0000;margin-left: 5px;line-height: inherit;text-align:center;font-weight: bold;">*</span>
						</div>
						<div class="fitem">
							<label>新密码：</label>
							<input id="inp_new_password" name="new_password" type="password" class="easyui-textbox" style="height:30px;width: 180px;"  data-options="required:true,validType:['minLength[5]','same[\'#inp_old_password\']']"/>
							<span style="color: #CC0000;margin-left: 5px;line-height: inherit;text-align:center;font-weight: bold;">*</span>
						</div>
						<div class="fitem">
							<label>确认密码：</label>
							<input id="inp_eque_password" name="eque_password" type="password" class="easyui-textbox" style="height:30px;width: 180px;" required="required" validType="equals['#inp_new_password']" />
							<span style="color: #CC0000;margin-left: 5px;line-height: inherit;text-align:center;font-weight: bold;">*</span>
						</div>
						<div class="fitem" style="text-align: right;background-color:#fff;">
							<a href="javascript:void(0)" class="easyui-linkbutton" iconcls="icon-ok"
							   onclick="operatsure('updatepassword')">确定</a>
						</div>
					</div>
				</form>
			</div>

		</div>





	<script type="text/javascript">

		var head_img = "<?=$_info['head_img']?>";

		var uploadCarImgDir = '/CourtGms/Uploads/'; // 定义上传图片目录



		$(function() {

			if(head_img != 'null' && head_img != null && head_img.length > 0){
				$('#edit_photo').attr('src',uploadCarImgDir + head_img);
			}
			else{
				$('#edit_photo').attr('src',"/CourtGms/Public/Service/img/people.png");
			}
			$('#inp_user_id').val("<?=$_info['id']?>");
			////数据正确性校验
			$.extend($.fn.validatebox.defaults.rules, {
				minLength: {
					validator: function (value, param) {
						var reg = /^[\s\u4E00-\u9FA5A-Za-z0-9-_]*$/;
						value = $.trim(value);
						return reg.test(value) && value.length >= param;
					},
					message: '请至少输入{0}个有效字符'
				},
				equals: {
					validator: function(value,param){
						return value == $(param[0]).val();
					},
					message: '两次输入不一样,请修改'
				},
				same: {
					validator: function(value,param){

						if(value == $(param[0]).val()){
							return false;
						}
						else{
							return true;
						}
					},
					message: '新密码与旧密码相同,请修改'
				},
				phone:{
					validator: function (value, param) {
						var rex=/^1[3-8]+\d{9}$/;
						//var rex=/^(([0\+]\d{2,3}-)?(0\d{2,3})-)(\d{7,8})(-(\d{3,}))?$/;
						//区号：前面一个0，后面跟2-3位数字 ： 0\d{2,3}
						//电话号码：7-8位数字： \d{7,8
						//分机号：一般都是3位数字： \d{3,}
						//这样连接起来就是验证电话的正则表达式了：/^((0\d{2,3})-)(\d{7,8})(-(\d{3,}))?$/
						var rex2=/^((0\d{2,3})-)(\d{7,8})(-(\d{3,}))?$/;
						value = $.trim(value);
						return rex.test(value)||rex2.test(value);
					},
					message: '手机号码有误，请重填'
				}
			});

		});



		function fileSelected() {


			var fileinfo1 = document.getElementById('edit_head_img').files[0];



			var url = window.URL.createObjectURL(fileinfo1);

			$('#edit_photo').attr('src',url);



//		var url1 = window.URL.createObjectURL(fileinfo1);
//
//		$('#edit_photo').attr('src',url1);
		}

		function operatsure(flag) {

			if('edit' == flag){

				$('#edit_user_info').form('submit', {
					url: "<?php echo U('User');?>&a="+flag,
					onSubmit: function () {
						var flag = true;
						flag = flag && $("#inp_old_password").textbox('isValid');
					},
					success: function (data) {
						var jsonResult = $.parseJSON(data);
						if (jsonResult.Code == 200) {
							if (jsonResult.Result) {
								$("#div_dialog_set").dialog('close');
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
			else{
				$("#Updatepassword").form('submit', {
					url: "<?php echo U('User');?>&a=updatePassword",
					onSubmit: function () {
						var flag = true;
						flag = flag && $("#inp_old_password").textbox('isValid');
						if(!flag){
							$.messager.alert('提示',"旧密码为必填内容,请填写", 'info');
							return flag;
						}
						flag = flag && $("#inp_new_password").textbox('isValid');
						if(!flag){
							$.messager.alert('提示',"新密码为必填内容或新密码与旧密码重复,请修改", 'info');
							return flag;
						}

						flag = flag && $("#inp_eque_password").textbox('isValid');
						if(!flag){
							$.messager.alert('提示',"确认密码为必填内容或与新密码不同,请修改", 'info');
							return flag;
						}
					},
					success: function (data) {
						var jsonResult = $.parseJSON(data);
						if (jsonResult.Code == 200) {
							if (jsonResult.Result) {
								$("#div_dialog_set").dialog('close');
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
		}






	</script>

































</body>
</html>