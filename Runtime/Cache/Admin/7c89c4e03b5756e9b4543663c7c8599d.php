<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width">
<title><?php echo C('WEB_SITE_TITLE');?> | <?php echo C('SOFT_NAME');?></title>
<link href="/TobaccoGms1/Public/Admin/images/favicon.ico" mce_href="/TobaccoGms1/Public/Admin/images/favicon.ico" rel="bookmark" type="image/x-icon" /> 
<link href="/TobaccoGms1/Public/Admin/images/favicon.ico" mce_href="/TobaccoGms1/Public/Admin/images/favicon.ico" rel="icon" type="image/x-icon" /> 
<link href="/TobaccoGms1/Public/Admin/images/favicon.ico" mce_href="/TobaccoGms1/Public/Admin/images/favicon.ico" rel="shortcut icon" type="image/x-icon" /> 
<link rel="stylesheet" type="text/css" href="/TobaccoGms1/Public/Static/Easyui/themes/metro-gms/easyui.css">
<link rel="stylesheet" type="text/css" href="/TobaccoGms1/Public/Static/Easyui/themes/icon.css">
<link rel="stylesheet" type="text/css" href="/TobaccoGms1/Public/Static/Easyui/themes/color.css">
<link rel="stylesheet" type="text/css" href="/TobaccoGms1/Public/Admin/css/login.css">
<style>
  .bg{position: absolute;right: 0px;top: 0px;bottom: 0px;left: 0px;-moz-background-size: 100% 100%;-o-background-size: 100% 100%;-webkit-background-size: 100% 100%;-ms-background-size: 100% 100%;background-size: 100% 100%;filter: "progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod='scale')";background-image:url(./Public/Admin/images/Login/bg_3.jpg);background-size: cover}
</style>
<script type="text/javascript" src="/TobaccoGms1/Public/Static/Jquery/jquery.min.js"></script>
<script type="text/javascript" src="/TobaccoGms1/Public/Static/Easyui/jquery.easyui.min.js"></script>
<script type="text/javascript" src="/TobaccoGms1/Public/Static/Easyui/locale/easyui-lang-zh_CN.js"></script>
</head>
<body>
<div class="bg"></div>
<div class="login_box">
  <div class="login_title">
    <div class="logo"><?php echo C('SOFT_NAME');?></div>
    <div class="info">—— V <?php echo C('SOFT_VERSION');?></div>
  </div>
  <form id="login_form">
    <div class="form">
      <div class="inputs">
        <div><span>用户名：</span>
          <input id="username" name="username" class="easyui-textbox" data-options="prompt:'请输入用户名';" style="height:33px;" type="text">
        </div>
        <div><span>密码：</span>
          <input id="password" name="password" class="easyui-textbox" style="height:33px;" type="password">
        </div>
      </div>
      <div style="text-align: center;padding-top: 6px">
          <a id="submit" style="width: 150px;height: 36px;background-color: #6FA5DB;color: white" class="easyui-linkbutton" onclick="login()"
             data-options="plain:true,">登 录</a>
          <!--<input type="checkbox" class="checkbox" name="rember_password" id="rm" checked="checked">-->
          <!--<label for="rm" style="color:#999">记住密码</label>-->
      </div>

      <!--<div class="msg"></div>-->
      <!--<div style="clear:both;"></div>-->
      <!--<div class="extend"> <a href="http://wpa.qq.com/msgrd?v=3&uin=<?php echo C('ADMIN_QQ');?>&site=qq&menu=yes" target="_blank">联系管理员</a></div>-->
      
    </div>
  </form>
</div>
<div class="common_footer">Powered by <a href="<?php echo C('SOFT_SITE');?>" style="color:#FFF"><?php echo C('SOFT_NAME');?> v <?php echo C('SOFT_VERSION');?></a> | Copyright © <a href="<?php echo C('SOFT_AUTHOR_SITE');?>" style="color:#FFF"><?php echo C('SOFT_AUTHOR');?></a> All rights reserved.</div>
<script type="text/javascript">

  <!-- 添加，密码输入回车后自动登录-->
  $('#password').bind('keypress',function(event){

    if(event.key == "Enter")
    {
      if($('#username').val().length!=0){

        if($('#password').val().length==0){
          $.messager.alert('提示','密码为必填内容,请填写','warning');

        }
        else{

          login();

        }
      }
      else{
        $.messager.alert('提示','用户名为必填内容,请填写','warning');
      }
    }
  });

  function login(){
    var flag = false;
    if($('#username').val().length==0){
//      alert("用户名为必填内容,请填写");

      $.messager.alert('提示','用户名为必填内容,请填写','warning');
    }
    else{
      if($('#password').val().length==0){
//        alert("密码为必填内容,请填写");
        $.messager.alert('提示','密码为必填内容,请填写','warning');
        flag = false;
      }
      else{
        flag = true;
      }
    }
    if(flag) {
      $.ajax({
        type: "post",
        url: "<?php echo U('login');?>",      // 这里是提交到什么地方的url
        data: {
          username: $('#username').val(),
          password: $('#password').val()
        },            // 这里把表单里面的数据放在这里传到后台
        dataType: "json",
        success: function (data) {
          // 调用回调函数
          if(200 == data.Code){


            $.messager.show({
              title:'消息',
              msg:'登录成功',
              timeout:3000,
              showType:'slide'
            });


            document.getElementById('username').value="";
            document.getElementById('password').value="";

            document.location = data.Url;
          }
          else if(300 == data.Code){
            $.messager.alert('登录失败','用户名或密码不能为空','warning');


            document.getElementById('username').value="";
            document.getElementById('password').value="";

          }
          else if(500 == data.Code){

            $.messager.alert('登录失败','用户被禁用,请联系管理员','warning');

            document.getElementById('username').value="";
            document.getElementById('password').value="";

          }
          else{
            $.messager.alert('登录失败','用户名或密码错误','warning');

            document.getElementById('username').value="";
            document.getElementById('password').value="";

          }
        }
      });
    }
  }
</script>
</body>
</html>