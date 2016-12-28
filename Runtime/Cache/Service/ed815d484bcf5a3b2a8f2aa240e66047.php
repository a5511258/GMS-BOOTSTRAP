<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <title> <?php echo C('SOFT_NAME');?>|Gms管理系统</title>
    <link rel="stylesheet" type="text/css" href="/TobaccoGms1/Public/Static/Easyui/themes/metro-gms/easyui.css">
    <link rel="stylesheet" type="text/css" href="/TobaccoGms1/Public/Static/themes/default.css">
    <link rel="stylesheet" type="text/css" href="/TobaccoGms1/Public/Static/Easyui/themes/icon.css">
    <link rel="stylesheet" type="text/css" href="/TobaccoGms1/Public/Static/Easyui/themes/color.css">
    <link rel="stylesheet" type="text/css" href="/TobaccoGms1/Public/Service/css/jquery.jOrgChart.css">

    <link href="/TobaccoGms1/Public/Service/uploadify/uploadify.css" rel="stylesheet" type="text/css" />





    <!--<link rel="stylesheet" href="/TobaccoGms1/Public/Admin/css/skin.css" />-->
    <script type="text/javascript" src="/TobaccoGms1/Public/Static/Jquery/jquery.min.js"></script>
    <script type="text/javascript" src="/TobaccoGms1/Public/Static/Easyui/jquery.easyui.min.js"></script>
    <script type="text/javascript" src="/TobaccoGms1/Public/Static/Easyui/locale/easyui-lang-zh_CN.js"></script>
    <script type="text/javascript" src="/TobaccoGms1/Public/Service/js/jquery.jOrgChart.js"></script>
    <script src="/TobaccoGms1/Public/Service/uploadify/jquery.uploadify.min.js" type="text/javascript"></script>

    <script type="text/javascript" src="/CourtGms/Public/Static/BaiduMap/baidu_offline_api.js"></script>

    <!--<script type="text/javascript" src="/TobaccoGms1/Public/Static/BaiduMap/"></script>-->



    <script charset="utf-8" src="/TobaccoGms1/Public/Static/kindeditor/kindeditor-min.js"></script>
    <script charset="utf-8" src="/TobaccoGms1/Public/Static/kindeditor/lang/zh_CN.js"></script>
    <script charset="utf-8" src="/TobaccoGms1/Public/Static/Echarts/echarts.js"></script>

    <script charset="utf-8" src="/TobaccoGms1/Public/Admin/js/base.js" />
    <script>
        var ke_pasteType=2;
        var ke_fileManagerJson="<?php echo U('Admin/FilesUpdata/filemanager');?>";
        var ke_uploadJson="<?php echo U('Admin/FilesUpdata/upload');?>";
        var ke_Uid='<?php echo session(C("AUTH_KEY"));;?>';
        var Root='/TobaccoGms1';
        $(document).ready(function (){
            $(".tipped").tipper();
        });
	</script>
    <script type="text/javascript" src="/TobaccoGms1/Public/Static/Tipper/js/jquery.fs.tipper.js"></script>

    <style>
        .tipped {}
        .tipped:hover {}
    </style>
</head>


    <body class="easyui-layout">

    <table id="grid">
        <thead data-options="frozen:false">
        <tr>

            <th field="id" hidden="hidden"></th>

            <th field="device_no" width="100" align="center" sortable="true">终端编号</th>

            <th field="group_name" width="120" align="center" sortable="true">所属车队</th>

            <th field="vehicle_id" width="120" align="center" sortable="true">绑定车牌号码</th>

            <th field="vehicle_license_color" width="80" align="center" sortable="true">车牌颜色</th>

            <th field="simCard_no" width="80" align="center" sortable="true">绑定SIM卡号码</th>

            <th field="device_type" width="80" align="center" sortable="true">设备类型</th>

            <th field="channel_num" width="80" align="center" sortable="true">通道数量</th>

            <th field="is_video" width="60" align="center" sortable="true">是否视频</th>

            <th field="manufacturer" width="120" align="center" sortable="true">制造商</th>

            <th field="bar_code" width="120" align="center" sortable="true">条码</th>

            <th field="status" width="80" align="center" sortable="true">设备状态</th>

            <th field="remark" width="80" align="center" >备注</th>

            <th field="Operate" formatter="window.deviceInfo.operate" width="180" align="center">操作</th>
        </tr>
        </thead>
    </table>

    <div id="div_toolbar">

        <?php if(Is_Auth('Service/DeviceInfo/Create')): ?><a href="javascript:;" class="easyui-linkbutton" onclick="window.deviceInfo.add()"
               data-options="iconCls:'icon-add',plain:true">添加</a>&nbsp;&nbsp;|<?php endif; ?>

        <?php if(Is_Auth('Service/DeviceInfo/MoveGroup')): ?><a href="javascript:;" class="easyui-linkbutton" onclick="window.deviceInfo.moveGroup()"
               data-options="iconCls:'icon-information',plain:true">转组</a>&nbsp;&nbsp;|<?php endif; ?>

        <a href="javascript:;" class="easyui-linkbutton" onclick="window.deviceInfo.query()"
           data-options="iconCls:'icon-search',plain:true">查询</a>&nbsp;&nbsp;|


        <a href="javascript:;" class="easyui-linkbutton" onclick="window.deviceInfo.resetQuery()"
           data-options="iconCls:'icon-redo',plain:true">重置</a>&nbsp;&nbsp;|

        <a href="javascript:;" class="easyui-linkbutton" onclick="window.deviceInfo.exportData()"
           data-options="iconCls:'icon-folder_export',plain:true">导出</a>

    </div>

    <div id="div_dialog" class="easyui-dialog"
         data-options="closed:true,buttons:'#div-buttons',modal:true" title="设备信息管理"
         style="width: 702px; height: auto; padding: 5px 10px;">

        <div id="div-buttons">
            <a href="javascript:void(0)" class="easyui-linkbutton" iconcls="icon-ok"
               onclick="window.deviceInfo.operateSure()">确定</a>
            <a href="javascript:void(0)" class="easyui-linkbutton" iconcls="icon-cancel"
               onclick="window.deviceInfo.operateCancel()">取消</a>
        </div>
    </div>

    <div id="div_dialog6" class="easyui-dialog" data-options="closed:true,modal:true" title="正在操作请等待..."
         style="width:auto;height: auto; text-align:center;">
        <img src="/TobaccoGms1/Public/Service/img/loading64.gif" alt=""/>
    </div>

    <div id="div_dialog_changeGroup" class="easyui-dialog"
         data-options="closed:true,buttons:'#changegroup-buttons',modal:true" title="转组">
        <div id="changegroup-buttons">
            <a href="javascript:void(0)" class="easyui-linkbutton" iconcls="icon-ok"
               onclick="window.deviceInfo.moveGroupSure()">确定</a>
            <a href="javascript:void(0)" class="easyui-linkbutton" iconcls="icon-cancel"
               onclick="window.deviceInfo.moveGroupCancel()">关闭</a>
        </div>
    </div>

    <div id="search_dialog" class="easyui-dialog"
         data-options="closed:true,buttons:'#seach-buttons',modal:true" title="设备查询"
         style="width: auto; height: auto; padding: 5px 10px;">
        <div id="seach-buttons">
            <a href="javascript:void(0)" class="easyui-linkbutton" iconcls="icon-search"
               onclick="window.deviceInfo.SearchSure()">查询</a>
        </div>
    </div>

    <script src="/TobaccoGms1/Public/Service/js/deviceInfo.js" type="text/javascript"></script>

    <script>
        var ul_list_url = "<?php echo U('DeviceInfo/getDeviceInfoToList');?>";
        var vehicle_search_url = "<?php echo U('search');?>";
        var result_edit = "<?=$edit?>";
        var result_del = "<?=$del?>";
    </script>
    </body>

</html>