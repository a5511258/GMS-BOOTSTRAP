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

            <th field="simcard_no" width="100" align="center" sortable="true">SIM卡号码</th>

            <th field="group_name" width="120" align="center" sortable="true">所属车队</th>

            <th field="device_no" width="120" align="center" sortable="true">绑定设备编号</th>

            <th field="simcard_operator" width="60" align="center" sortable="true">运营商</th>

            <th field="simcard_network_type" width="80" align="center" sortable="true">网络类型</th>

            <th field="simcard_flow" width="80" align="center" sortable="true">流量套餐</th>

            <th field="simcard_open_time" width="80" align="center" sortable="true">开卡时间</th>

            <th field="simcard_imei" width="120" align="center" sortable="true">IMEI编号</th>

            <th field="status" width="80" align="center" sortable="true">SIM卡状态</th>

            <th field="remark" width="80" align="center">备注</th>

            <th field="Operate" formatter="window.simCardInfo.operate" width="180" align="center">操作</th>
        </tr>
        </thead>
    </table>

    <div id="div_toolbar">

        <?php if(Is_Auth('Service/SimCardInfo/Create')): ?><a href="javascript:;" class="easyui-linkbutton" onclick="window.simCardInfo.add()"
               data-options="iconCls:'icon-add',plain:true">添加</a>&nbsp;&nbsp;|<?php endif; ?>

        <?php if(Is_Auth('Service/SimCardInfo/MoveGroup')): ?><a href="javascript:;" class="easyui-linkbutton" onclick="window.simCardInfo.moveGroup()"
               data-options="iconCls:'icon-information',plain:true">转组</a>&nbsp;&nbsp;|<?php endif; ?>

        <a href="javascript:;" class="easyui-linkbutton" onclick="window.simCardInfo.query()"
           data-options="iconCls:'icon-search',plain:true">查询</a>&nbsp;&nbsp;|


        <a href="javascript:;" class="easyui-linkbutton" onclick="window.simCardInfo.resetQuery()"
           data-options="iconCls:'icon-redo',plain:true">重置</a>&nbsp;&nbsp;|

        <a href="javascript:;" class="easyui-linkbutton" onclick="window.simCardInfo.exportData()"
           data-options="iconCls:'icon-folder_export',plain:true">导出</a>

    </div>

    <div id="div_dialog" class="easyui-dialog"
         data-options="closed:true,buttons:'#div-buttons',modal:true" title="SIM卡信息管理"
         style="width: 702px; height: auto; padding: 5px 10px;">

        <div id="div-buttons">
            <a href="javascript:void(0)" class="easyui-linkbutton" iconcls="icon-ok"
               onclick="window.simCardInfo.operateSure()">确定</a>
            <a href="javascript:void(0)" class="easyui-linkbutton" iconcls="icon-cancel"
               onclick="window.simCardInfo.operateCancel()">取消</a>
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
               onclick="window.simCardInfo.save()">确定</a>
            <a href="javascript:void(0)" class="easyui-linkbutton" iconcls="icon-cancel"
               onclick="window.simCardInfo.close()">关闭</a>
        </div>
    </div>

    <div id="search_dialog" class="easyui-dialog"
         data-options="closed:true,buttons:'#seach-buttons',modal:true" title="SIM卡查询"
         style="width: auto; height: auto; padding: 5px 10px;">
        <div id="seach-buttons">
            <a href="javascript:void(0)" class="easyui-linkbutton" iconcls="icon-search"
               onclick="window.simCardInfo.SearchSure()">查询</a>
        </div>
    </div>

    <script src="/TobaccoGms1/Public/Service/js/simCardInfo.js" type="text/javascript"></script>

    <script>
        var ul_list_url = "<?php echo U('SimCardInfo/getSimCardInfoToList');?>";
        var vehicle_search_url = "<?php echo U('search');?>";
        var result_edit = "<?=$edit?>";
        var result_del = "<?=$del?>";
    </script>
    </body>

</html>