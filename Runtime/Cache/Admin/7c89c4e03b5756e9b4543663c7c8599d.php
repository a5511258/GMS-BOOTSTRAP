<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title> <?php echo C('SOFT_NAME');?>|Gms管理系统</title>
    <link href="__IMG__/favicon.ico" mce_href="__IMG__/favicon.ico" rel="bookmark" type="image/x-icon" /> 
    <link href="__IMG__/favicon.ico" mce_href="__IMG__/favicon.ico" rel="icon" type="image/x-icon" /> 
    <link href="__IMG__/favicon.ico" mce_href="__IMG__/favicon.ico" rel="shortcut icon" type="image/x-icon" />
    <link rel="stylesheet" type="text/css" href="/GMS-BOOTSTRAP/Public/Static/Bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="/GMS-BOOTSTRAP/Public/Static/Font/iconfont.css">
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="/GMS-BOOTSTRAP/Public/Static/Bootstrap/js/html5shiv.min.js"></script>
    <script src="/GMS-BOOTSTRAP/Public/Static/Bootstrap/js/respond.min.js"></script>
    <![endif]-->

    <script src="/GMS-BOOTSTRAP/Public/Static/Jquery/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="/GMS-BOOTSTRAP/Public/Static/Bootstrap/js/bootstrap.min.js"></script>
</head>
<body>

    <link rel="stylesheet" type="text/css" href="Public/Admin/css/login.css" />
    <style>
        .bg{position: absolute;right: 0px;top: 0px;bottom: 0px;left: 0px;-moz-background-size: 100% 100%;-o-background-size: 100% 100%;-webkit-background-size: 100% 100%;-ms-background-size: 100% 100%;background-size: 100% 100%;filter: "progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod='scale')";background-image:url(./Public/Admin/images/Login/bg_3.jpg);background-size: cover}
    </style>
    <body class="bg">
    <div class="login_box">
        <div class="login_title">
            <div class="logo"><?php echo C('SOFT_NAME');?></div>
            <div class="info">—— V <?php echo C('SOFT_VERSION');?></div>
        </div>
        <div class="login_form" style="">
            <form class="form-horizontal" role="form">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">用户名</label>
                    <div class="col-sm-10">
                        <input type="email" style="height:33px;width: 280px" class="form-control" id="inputEmail3" placeholder="用户名">
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputPassword3" class="col-sm-2 control-label">密码&nbsp</label>
                    <div class="col-sm-10">
                        <input type="password" style="height:33px;width: 280px" class="form-control" id="inputPassword3" placeholder="密码">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="submit" class="btn btn-default">登录</button>
                    </div>
                </div>
            </form>
        </div>

        <!--<form id="login_form">-->
            <!--<div class="form">-->
                <!--<div class="inputs">-->
                    <!--<div><span>用户名：</span>-->
                        <!--<input id="username" name="username" class="easyui-textbox" data-options="prompt:'请输入用户名';" style="height:33px;" type="text">-->
                    <!--</div>-->
                    <!--<div><span>密码：</span>-->
                        <!--<input id="password" name="password" class="easyui-textbox" style="height:33px;" type="password">-->
                    <!--</div>-->
                <!--</div>-->
                <!--<div style="text-align: center;padding-top: 6px">-->
                    <!--<a id="submit" style="width: 150px;height: 36px;background-color: #6FA5DB;color: white" class="easyui-linkbutton" onclick="login()"-->
                       <!--data-options="plain:true,">登 录</a>-->
                    <!--&lt;!&ndash;<input type="checkbox" class="checkbox" name="rember_password" id="rm" checked="checked">&ndash;&gt;-->
                    <!--&lt;!&ndash;<label for="rm" style="color:#999">记住密码</label>&ndash;&gt;-->
                <!--</div>-->

                <!--&lt;!&ndash;<div class="msg"></div>&ndash;&gt;-->
                <!--&lt;!&ndash;<div style="clear:both;"></div>&ndash;&gt;-->
                <!--&lt;!&ndash;<div class="extend"> <a href="http://wpa.qq.com/msgrd?v=3&uin=<?php echo C('ADMIN_QQ');?>&site=qq&menu=yes" target="_blank">联系管理员</a></div>&ndash;&gt;-->

            <!--</div>-->
        <!--</form>-->
    </div>
    </body>

</body>
</html>