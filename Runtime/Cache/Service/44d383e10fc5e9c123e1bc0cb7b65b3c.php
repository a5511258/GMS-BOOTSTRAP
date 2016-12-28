<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <title> <?php echo C('SOFT_NAME');?>|Gms管理系统</title>
    <link rel="stylesheet" type="text/css" href="/TobaccoGms1/Public/Static/Easyui/themes/metro-gms/easyui.css">
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


    <style>
        .ftitle {
            font-size: 14px;
            font-weight: bold;
            padding: 5px 0;
            margin-bottom: 10px;
            border-bottom: 1px solid #ccc;
        }

        .fitem label {
            display: inline-block;
            width: 100px;
            text-align: right;
        }

    </style>
    <body class="easyui-layout">

    <table id="grid">
        <thead data-options="frozen:false">
        <tr>
            <th field="vehicle_id" hidden="hidden"></th>
            <th field="vehicle_license" width="100" align="center">车牌号</th>
            <th field="group_name" width="120" align="center">所属车队</th>
            <th field="vehicle_brand" width="80" align="center">车辆品牌</th>
            <th field="vehicle_type" width="80" align="center">车辆类型</th>
            <th field="vehicle_license_color" width="100" align="center">车牌颜色</th>
            <th field="vehicle_rack_number" width="160" align="center">机架号</th>
            <th field="vehicle_engine_number" width="160" align="center">发动机号</th>

            <th field="province" width="120" align="center">省份</th>
            <th field="city" width="120" align="center">城市</th>

            <th field="vehicle_explain" width="240" align="center">备注</th>

            <th field="Operate" formatter="window.vehicleInfo.operate" width="160" align="center">操作</th>
        </tr>
        </thead>
    </table>

    <div id="div_toolbar">

        <?php if(Is_Auth('Service/VehicleInfo/Create')): ?><a href="javascript:;" class="easyui-linkbutton" onclick="window.vehicleInfo.add()"
               data-options="iconCls:'icon-add',plain:true">添加</a>&nbsp;&nbsp;|<?php endif; ?>

        <?php if(Is_Auth('Service/VehicleInfo/UpdateGroupId')): ?><a href="javascript:;" class="easyui-linkbutton" onclick="moveGroup()"
               data-options="iconCls:'icon-information',plain:true">转组</a>&nbsp;&nbsp;|<?php endif; ?>

        <a href="javascript:;" class="easyui-linkbutton" onclick="window.vehicleInfo.query()"
           data-options="iconCls:'icon-search',plain:true">查询</a>&nbsp;&nbsp;|

        <a href="javascript:;" class="easyui-linkbutton" onclick="window.vehicleInfo.resetQuery()"
           data-options="iconCls:'icon-redo',plain:true">重置</a>&nbsp;&nbsp;|

        <a href="javascript:;" class="easyui-linkbutton" onclick="window.vehicleInfo.exportData()"
           data-options="iconCls:'icon-folder_export',plain:true">导出</a>

    </div>

    <div id="div_dialog" class="easyui-dialog"
         data-options="closed:true,buttons:'#div-buttons',modal:true" title="车辆信息管理"
         style="width: 702px; height: auto; padding: 5px 10px;">

        <div id="div-buttons">
            <a href="javascript:void(0)" class="easyui-linkbutton" iconcls="icon-ok"
               onclick="window.vehicleInfo.operateSure()">确定</a>
            <a href="javascript:void(0)" class="easyui-linkbutton" iconcls="icon-cancel"
               onclick="window.vehicleInfo.operateCancel()">取消</a>
        </div>
    </div>

    <div id="div_dialog4" class="easyui-dialog"
         data-options="closed:true,iconCls:'icon-car',modal:true" title="车辆详情"
         style="width: 702px; height: auto; padding: 5px">
        <!--<img id="photo_view" alt="车辆图片预览" style="display:block;height:100%;width: 100%">-->


    </div>

    <div id="div_dialog6" class="easyui-dialog" data-options="closed:true,modal:true" title="正在操作请等待..."
         style="width:auto;height: auto; text-align:center;">
        <img src="/TobaccoGms1/Public/Service/img/loading64.gif" alt=""/>
    </div>

    <div id="div_dialog_changeGroup" class="easyui-dialog"
         data-options="closed:true,buttons:'#changegroup-buttons',modal:true" title="转组">
        <div id="changegroup-buttons">
            <a href="javascript:void(0)" class="easyui-linkbutton" iconcls="icon-ok"
               onclick="window.vehicleInfo.save()">确定</a>
            <a href="javascript:void(0)" class="easyui-linkbutton" iconcls="icon-cancel"
               onclick="window.vehicleInfo.close()">关闭</a>
        </div>
    </div>

    <div id="search_dialog" class="easyui-dialog"
         data-options="closed:true,buttons:'#seach-buttons',modal:true" title="组织架构"
         style="width: auto; height: auto; padding: 5px 10px;">
        <div id="seach-buttons">
            <a href="javascript:void(0)" class="easyui-linkbutton" iconcls="icon-search"
               onclick="window.vehicleInfo.SearchSure()">查询</a>
        </div>
    </div>

    <script src="/TobaccoGms1/Public/Service/js/vehicleInfo.js" type="text/javascript"></script>
    <script src="/TobaccoGms1/Public/Service/js/mvsp_vehicleinfo.js" type="text/javascript"></script>
    <script>
        var ul_tree_url = "<?php echo U('GroupInfo/getGroupInfosToTree');?>";
        var ul_list_url = "<?php echo U('VehicleInfo/getVehicleInfoToList');?>";
        var vehicle_type_url = "<?php echo U('WordBook/getWordBook?typeId=1');?>";
        var vehicle_from_url = "<?php echo U('WordBook/getWordBook?typeId=21');?>";
        var vehicle_fromK_device_url = "<?php echo U('VehicleInfo/getDeviceName');?>";

        var vehicle_search_url = "<?php echo U('search');?>";
        var result_edit = "<?=$edit?>";
        var result_del = "<?=$del?>";

        function moveGroup() {
            $('#div_dialog_changeGroup').window('refresh', "<?php echo U('changeGroup');?>");
            $('#div_dialog_changeGroup').window({
                title: "转组",
                width: 350,
                height: 260,
                top: (screen.height) / 6,
                left: (screen.width - 400) / 2,
                resizable: false,
                collapsible: false,
                minimizable: false,
                maximizable: false
            });
            $('#div_dialog_changeGroup').window('open');
        }
    </script>
    </body>

</html>