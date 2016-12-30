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
    <link rel="stylesheet" type="text/css" href="/GMS_Base/Public/Static/Bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="/GMS_Base/Public/Static/Font/iconfont.css">
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="/GMS_Base/Public/Static/Bootstrap/js/html5shiv.min.js"></script>
    <script src="/GMS_Base/Public/Static/Bootstrap/js/respond.min.js"></script>
    <![endif]-->
    <script src="/GMS_Base/Public/Static/Jquery/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="/GMS_Base/Public/Static/Bootstrap/js/bootstrap.min.js"></script>
</head>
<body>

    <?php $bjsj=rand(1,4); ?>
    <link rel="stylesheet" type="text/css" href="Public/Admin/css/login.css" />
    <!--随机背景图片-->
    <style>
        .bg {
            background-image: url(./Public/Admin/images/Login/bg_<?php echo ($bjsj); ?>.jpg);
        }
    </style>
    <body class="bg">
    <div class="login_box">
        <div class="login_title">
            <div class="logo"><?php echo C('SOFT_NAME');?></div>
            <!--<div class="info">—— V <?php echo C('SOFT_VERSION');?></div>-->
        </div>
        <div class="login_form">
            <form class="form-horizontal" role="form" method="post" onsubmit="visibleForm()">
                <div class="form-group">
                    <label for="inpUser" class="col-sm-2 control-label">用户名</label>
                    <div class="col-sm-10">
                        <input id="userTip" type="text" class="form-control"
                               id="inpUser" placeholder="用户名" data-toggle="tooltip"
                               title="adsfasdfs" data-placement="bottom"
                               data-animation="hover:'focus'">
                        <!-- HTML to write -->
                        <!--<a  href="#" >-->

                        <!--</a>-->
                    </div>
                </div>
                <div class="form-group">
                    <label for="inpPwd" class="col-sm-2 control-label">密码&nbsp</label>
                    <div class="col-sm-10">
                        <input type="password" class="form-control" id="inpPwd"
                               placeholder="密码">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-3 col-sm-10">
                        <button type="button" id="loginButton" class="btn btn-primary btn-lg btn-block">登录</button>
                    </div>

                </div>
            </form>
        </div>
    </div>
    <script type="text/javascript" src="Public/Admin/js/login.js"></script>
    </body>

</body>
</html>