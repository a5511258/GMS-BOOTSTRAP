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

        .fitem {
            padding: 6px;
            margin: 3px;
            height: 30px;
            /*background-color: #EEEEEE;*/
        }

        .fitem:nth-child(2n) {
            /*background-color: #F2F8FB;*/
        }
    </style>

    <!-- 这里添加具体内容 -->
    <!-- 需要添加 easyui-layout -->
    <body class="easyui-layout">
    <!-- 需要移除 title属性-->
    <div data-options="region:'center',border:false" style="height: 100%;width: 100%">
        <table id="grid" class="easyui-treegrid" style="height: 100%;width: auto">
            <thead data-options="frozen:false">
            <tr>

                <th field="text" width="120" align="left">名称</th>

                <th field="parent_name" width="120" align="center">上级名称</th>

                <th field="limit_num" width="80" align="center">限制车辆数</th>

                <th field="group_type" width="80" align="center">类型</th>

                <th field="industry" width="120" align="center">所属行业</th>

                <th field="responsibility_people" width="120" align="center">负责人</th>

                <th field="tel_no" width="100" align="center">联系电话</th>

                <th field="address" width="150" align="center">地址</th>

                <th field="description" width="150" align="center">描述</th>

                <th field="Operate" formatter="window.group.operate" width="90" align="center">操作</th>
            </tr>
            </thead>
        </table>
        <div id="div_toolbar">
            <!--组织架构树:-->

            <a href="javascript:;" class="easyui-linkbutton" onclick="window.group.query()"
               data-options="iconCls:'icon-search',plain:true">查询</a>|&nbsp;
            <a href="javascript:;" class="easyui-linkbutton" onclick="window.group.add()"
               data-options="iconCls:'icon-add',plain:true,">添加</a>|&nbsp;
            <a href="javascript:;" class="easyui-linkbutton" onclick="window.group.exportTree()"
               data-options="iconCls:'icon-folder_export',plain:true,">导出组织架构图</a>|&nbsp;
            <a href="javascript:;" class="easyui-linkbutton" onclick="window.group.exportData()"
               data-options="iconCls:'icon-folder_export',plain:true">导出</a>|&nbsp;
            <a href="javascript:;" class="easyui-linkbutton" onclick="window.group.resetQuery()"
               data-options="iconCls:'icon-redo',plain:true">重置</a>
        </div>


        <div id="div_dialog" class="easyui-dialog" data-options="closed:true,buttons:'#div-buttons',modal:true"
             title="组织架构管理" style="padding: 10px 20px">

            <div id="div-buttons">
                <a href="javascript:void(0)" class="easyui-linkbutton" iconcls="icon-ok"
                   onclick="window.group.operateSure()">确定</a>
                <a href="javascript:void(0)" class="easyui-linkbutton" iconcls="icon-cancel"
                   onclick="window.group.operateCancel()">取消</a>
            </div>

        </div>


        <div id="div_dialog2" class="easyui-window" data-options="closed:true,iconCls:'icon-folder',modal:true,"
             title="组织架构管理" style="width: 600px; height: 460px; padding: 10px 20px">

            <div class="ftitle">导出组织架构图</div>
            <input id="ul_tree" style="width: 0px;height: 0px;display: none" type="hidden"/>

            <div id="div_org" style="width:100%; height:100px;"></div>
        </div>
        <div id="search_dialog" class="easyui-dialog"
             data-options="closed:true,buttons:'#seach-buttons',modal:true" title="组织架构"
             style="width: auto; height: auto; padding: 5px 10px;">
            <div id="seach-buttons">
                <a href="javascript:void(0)" class="easyui-linkbutton" iconcls="icon-search"
                   onclick="window.group.SearchSure()">查询</a>
            </div>
        </div>


        <div id="div_dialog_loading" class="easyui-dialog" data-options="closed:true,modal:true" title="正在操作请等待..."
             style="width:170px; text-align:center;">
            <img src="/TobaccoGms1/Public/Service/img/loading64.gif" alt=""/>
        </div>
    </div>


    <script>
        var grid_url = "<?php echo U('GroupInfo/getGroupInfoToList');?>";

        var group_search_url = "<?php echo U('search');?>";

        var del_url = "<?php echo U('GroupInfo/Delete');?>";
    </script>

    <script src="/TobaccoGms1/Public/Service/js/groupInfo.js" type="text/javascript"></script>
    <script src="/TobaccoGms1/Public/Service/js/jquery.jOrgChart.js" type="text/javascript"></script>


    </body>

</html>