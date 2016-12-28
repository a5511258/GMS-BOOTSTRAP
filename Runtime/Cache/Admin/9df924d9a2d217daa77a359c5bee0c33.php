<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>管理控制台 | <?php echo C('SOFT_NAME');?></title>
<link rel="stylesheet" type="text/css" href="/TobaccoGms1/Public/Static/Easyui/themes/metro-gms/easyui.css">
    <link rel="stylesheet" type="text/css" href="/TobaccoGms1/Public/Static/Themes/default.css">
<link rel="stylesheet" type="text/css" href="/TobaccoGms1/Public/Static/Easyui/themes/icon.css">
<link rel="stylesheet" href="/TobaccoGms1/Public/Static/Font/iconfont.css">
<link rel="stylesheet" type="text/css" href="/TobaccoGms1/Public/Static/Easyui/themes/color.css">
<link rel="stylesheet" type="text/css" href="/TobaccoGms1/Public/Admin/css/main.css">
<script type="text/javascript" src="/TobaccoGms1/Public/Static/Jquery/jquery.min.js"></script>
<script type="text/javascript" src="/TobaccoGms1/Public/Static/Easyui/jquery.easyui.min.js"></script>
<script type="text/javascript" src="/TobaccoGms1/Public/Static/Easyui/locale/easyui-lang-zh_CN.js"></script>
<script type="text/javascript" src="/TobaccoGms1/Public/Admin/js/main.js"></script>
</head>
<body class="easyui-layout" id="Main_Layout_Box">
<div id="Main_Layout_North" data-options="region:'north',split:false">
    <!--此处修改管理平台后面V字符-->
    <div id="header_logo"> <a href="<?php echo U('Admin/Index/index');?>"><img src="/TobaccoGms1/Public/Admin/images/Main/courtLogo.png" height="36px" width="36px" style="vertical-align: middle;padding-right: 6px" alt="logo"/><?php echo C('SOFT_NAME');?></a> <i class="opacity-80">v<?php echo C('SOFT_VERSION');?></i> <!--<a class="justify" href="javascript:void(0);"></a>--></div>
    <ul id="header_nav" class="header_nav">
    <?php if(is_array($AdminMenu)): $i = 0; $__LIST__ = $AdminMenu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><a href="JavaScript:void(0);" id="nav_<?php echo ($v['id']); ?>" menuType ="<?php echo ($v['menu_type']); ?>" menuUrl ="<?php echo U($v['name']);?>" menuMC ="<?php echo ($v['name']); ?>" menuTitle ="<?php echo ($v['title']); ?>" menuIcon="<?php echo ($v['icon']); ?>"><li><?php echo ($v['title']); ?></li></a><?php endforeach; endif; else: echo "" ;endif; ?>
    <!--<a href="<?php echo U('Manager/Index/index');?>" target="_self" ><li>实时监控</li></a>-->
  </ul>
    <ul class="header_nav" style="float:right">
        <a href="JavaScript:void(0);" id="userinfo"><li><?php echo ($UserInfo['group_title']); ?>[<?php echo ($UserInfo['username']); ?>]</li></a>
        <!--<a href="JavaScript:void(0);" onclick="UpdateTabs('Admin/User/userinfo','用户资料','<?php echo U('User/userinfo');?>','iconfont icon-user')"><li><?php echo ($UserInfo['group_title']); ?>[<?php echo ($UserInfo['username']); ?>]</li></a>-->
        <!--<?php if(Is_Auth('Admin/Index/cache')): ?>-->
            <!--<a href="JavaScript:void(0);" onclick="UpdateTabs('Admin/Index/cache','缓存更新','<?php echo U('Index/cache');?>','iconfont icon-shezhi')"><li>缓存更新</li></a>-->
        <!--<?php endif; ?>-->
        <?php if(Is_Auth_Check('Manager/Setting/setting')): ?><a id="setting" href="JavaScript:void(0);"><li>设置</li></a><?php endif; ?>
        <?php if(Is_Auth_Check('Manager/Setting/generateLogs')): ?><a href="<?php echo U('Gps/Index/generateLogs');?>" target="_blank"><li>导出日志</li></a><?php endif; ?>

        <a href="./Public/Manager/keda/bin/BS-Setup.exe" target="_blank"><li>插件下载</li></a>
        <a href="<?php echo U('Public/logout');?>"><li>退出</li></a>
  </ul>
</div>
<div id="Main_Layout_West" data-options="region:'west',split:false,cls:'Main_Layout_West_box'">
<?php if(is_array($AdminMenu)): $i = 0; $__LIST__ = $AdminMenu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><div id="nav_<?php echo ($v['id']); ?>_box" class="nav_box">
        <div class="right_box">
          <?php if(is_array($v['children'])): $i = 0; $__LIST__ = $v['children'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><dl id="right_<?php echo ($vo['id']); ?>_box_menu">
              <dd>
                <ul>
                  <?php if(is_array($vo['children'])): $i = 0; $__LIST__ = $vo['children'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo2): $mod = ($i % 2 );++$i;?><a href="JavaScript:void(0);" onclick="UpdateTabs('<?php echo ($vo2['name']); ?>','<?php echo ($vo2['title']); ?>','<?php echo ($vo2['url']); ?>','<?php echo ($vo2['icon']); ?>')"><li><i class="icon iconfont <?php echo ($vo2['icon']); ?>"  style="margin: 5px"></i><?php echo ($vo2['title']); ?></li></a><?php endforeach; endif; else: echo "" ;endif; ?>
                </ul>
              </dd>
            </dl><?php endforeach; endif; else: echo "" ;endif; ?>
        </div>
        <ul class="left_box">
            <?php if(is_array($v['children'])): $i = 0; $__LIST__ = $v['children'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><a href="JavaScript:void(0);" id="left_<?php echo ($vo['id']); ?>_nav_menu" class="left_nav" onclick="LeftMenu('<?php echo ($vo['id']); ?>')"><li><i class="icon iconfont <?php echo ($vo['icon']); ?>"></i><div><?php echo ($vo['title']); ?></div></li></a><?php endforeach; endif; else: echo "" ;endif; ?>
        </ul>
    </div><?php endforeach; endif; else: echo "" ;endif; ?>
</div>
<div id="Main_Layout_Center" data-options="region:'center',split:false" style="margin-bottom: 14px">
  <div id="MainTabs" class="easyui-tabs" data-options="fit:true,border:false">
  </div>
</div>

<div id="div_dialog_set" class="easyui-dialog" data-options="closed:true,modal:true" style="padding: 5px 10px;"></div>


<script>
    var user_info_url = "<?php echo U('User/userinfo');?>";
    var setting_url = "<?php echo U('Manager/index/setting');?>";


</script>



</body>
</html>