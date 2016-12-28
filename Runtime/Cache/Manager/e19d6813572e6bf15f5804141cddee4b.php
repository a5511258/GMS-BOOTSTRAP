<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8">
    <title>实时监控</title>
    <script type="text/javascript" src="/TobaccoGms1/Public/Static/Jquery/jquery.min.js"></script>
    <script type="text/javascript" src="/TobaccoGms1/Public/Static/Jquery/jquery.form.min.js"></script>
    <script type="text/javascript" src="/TobaccoGms1/Public/Static/Easyui/jquery.easyui.min.js"></script>
    <script type="text/javascript" src="/TobaccoGms1/Public/Static/Easyui/locale/easyui-lang-zh_CN.js"></script>

    <script type="text/javascript" src="/TobaccoGms1/Public/Manager/js/main.js"></script>

    <link rel="stylesheet" type="text/css" href="/TobaccoGms1/Public/Static/Easyui/themes/metro-gms/easyui.css">
    <link rel="stylesheet" type="text/css" href="/TobaccoGms1/Public/Static/Easyui/themes/icon.css">
    <!--<link rel="stylesheet" href="/TobaccoGms1/Public/Static/Font/iconfont.css">-->
    <link rel="stylesheet" type="text/css" href="/TobaccoGms1/Public/Static/Easyui/themes/color.css">


    <script type="text/javascript" src="/TobaccoGms1/Public/Static/BaiduMap/Lib/DistanceTool_min.js"></script>
    <script type="text/javascript" src="/TobaccoGms1/Public/Static/BaiduMap/baidu_offline_api.js"></script>

    <script src="/TobaccoGms1/Public/Manager/js/BMapUtils.js"></script>


    <link rel="stylesheet" type="text/css" href="/TobaccoGms1/Public/Admin/css/main.css">
    <!--<script type="text/javascript" src="/TobaccoGms1/Public/Admin/js/main.js"></script>-->



    <style>
        body {
            padding: 0px;
            margin: 0px;
            width: 100%;
            height: 100%;
        }
        .tree-header div {
            /*设置文本颜色*/
            color: #000000;
        }
        .car_info_title {
            padding-right: 16px;
            display:-moz-inline-box;
            display:inline-block;
            width: 50%;
        }
        .car_info_value {
            width: 20%;
            padding-right: 16px;
        }
        .car_info p{
            padding-left: 8px;
            padding-top: 0px;
            padding-bottom: 0px;
        }
        .car_detail p{
            padding-left: 8px;
        }
        .car_status p {
            padding-left: 8px;
        }
        .tree-node {
            height: 24px;
            font-size: large;
        }
        .tree-title {
            font-size: 16px;
            line-height: normal;
        }
    </style>
</head>
<body class="easyui-layout">

<div data-options="region:'west',collapsible:true,headerCls:'tree-header',split:true" title="车辆列表"  style="width: 25%;height:100%;">


        <div style="height: 30px;text-align: center;" data-options="split:true">
            <input style="width: 100%;" class="easyui-searchbox"
                   data-options="prompt:'请输入检索内容',menu:'#mm1',searcher:doSearch"/>
        </div>

        <div>
            <ul id="tt" checkbox="true" data-options="split:true">
            </ul>
        </div>

</div>


<div data-options="region:'center'" style="width: 75%;height: 100%; background-color: #919191;">

    <div id="mapContent"  style="width: 100%;height: 100%; background-color: #919191;">
        <div id="map" style="width: 100%;height: 100%;">
        </div>
        <div id="realVideo" class="easyui-window" title="视频监控" closed="true">
        </div>
    </div>

</div>

<div id="mm" class="easyui-menu" style="width:auto;">


    <?php if(hasAuthForService('Manager/Index/track')): ?><div onclick="track()" >跟踪</div><?php endif; ?>

    <div onclick="moveToCar()">定位</div>
    <div onclick="showRealVideo()" data-options="disabled:true">实时视频</div>
    <!--<div onclick="test()">实时视频测试</div>-->

    <div class="menu-sep"></div>
    <!--<div onclick="" >单车控制</div>-->
    <?php if(hasAuthForService('Manager/Track/history')): ?><div onclick="showHistory()" >历史查询</div><?php endif; ?>

    <?php if(hasAuthForService('Manager/Track/index')): ?><div onclick="showTrack()" >路径回放</div><?php endif; ?>
    <div class="menu-sep"></div>

    <div>关闭</div>
</div>
<div id="mm_single_channe" class="easyui-menu" style="width:auto;">
    <?php if(hasAuthForService('Manager/Index/track')): ?><div onclick="track()" >跟踪</div><?php endif; ?>

    <div onclick="moveToCar()">定位</div>
    <div onclick="showRealVideo()">实时视频</div>

    <!--<div onclick="showRealVideo()"></div>-->

    <div class="menu-sep"></div>
    <!--<div onclick="" >单车控制</div>-->
    <?php if(hasAuthForService('Manager/Track/history')): ?><div onclick="showHistory()" >历史查询</div><?php endif; ?>

    <?php if(hasAuthForService('Manager/Track/index')): ?><div onclick="showTrack()" >路径回放</div><?php endif; ?>
    <div class="menu-sep"></div>

    <div>关闭</div>
</div>

<div id="mm_camera" class="easyui-menu" style="width:auto;">
    <div onclick="showRealVideo()">实时视频</div>
    <div class="menu-sep"></div>
    <div>关闭</div>
</div>

<div id="mm1">
    <div data-options="name:'group_name'">单位</div>
    <div data-options="name:'vehicle_license'">车牌号</div>
</div>

<script>

    var setting_online = '<?php echo ($Setting["online"]); ?>';
    var setting_online_stop = '<?php echo ($Setting["online_stop"]); ?>';
    var setting_offline = '<?php echo ($Setting["offline"]); ?>';
    var setting_refresh_time = '<?php echo ($Setting["refresh_time"]); ?>';
    var history_icon = '<?php echo ($historyIcon); ?>';
    var realvideo_icon = '<?php echo ($realVideoIcon); ?>';
    var latlng_str = '<?php echo ($latlng); ?>';

    var latlngs = [];
    if(latlng_str != ''){
        latlngs = latlng_str.split(',');
    }
    var directionDesc = ["正北","东北","正东","东南","正南","西南","正西","西北"];

    //    用于存放车辆信息
    var cars ;
//    用于存放选中车辆id；
    var carGpsInfoArr = new Array();

    var rootGroupId;
    function showUserSetting() {
//        alert('111');
        $('#setting').window('refresh', "<?php echo U('Admin/User/userinfo');?>");
        $('#setting').window('open');
    }


    function showCarInfosDetail(){
        var vehicleID = currNode.id%1000000000;

        $('#setting').window('refresh', "<?php echo U('Manager/Index/showCarInfosDetail?windowId=setting&vehicle_id=');?>"+vehicleID);
        $('#setting').window({
            title:"查看车辆详细信息【"+cars[vehicleID].carinfo.car_license+"("+cars[vehicleID].carinfo.plate_color+")】",
            width: 720,
            height: 720
        });
        $('#setting').window('open');
    }
    var treeParams = {};

    function doSearch(value,name) {
//        alert('You input: ' + value+'('+name+')');
        var tempurl = "<?php echo U('Service/VehicleInterface/getVehicleTree');?>";
        var params ={};
        if (value == '') {
            treeParams = {};
        } else {
            tempurl = "<?php echo U('Service/VehicleInterface/searchToTree');?>";
            treeParams = {value:value,columnName:name};
        }
//      重新拉取数据
        $.ajax({
            url: tempurl,
            type: 'Post',
            dataType: 'Json',
            data: treeParams,
            success: function (result) {
                if(result){
                    console.log(result);
                    treeCheckedState = {};
                    treeState = {};
                    $('#tt').tree('loadData',result);
                }
            }
        });
//        treeParams = '&columnName='+name+'&value='+value;
//        reloadTree()
//        1.清空地图点
//        cleanMarkers();


    }
    var interval;

    var car_ids = [];
    //  当前操作的节点
    var currNode;

    var treeState = new Object();
    var treeCheckedState = new Object();
//    标识：用于区分当前是否正在重新加载，重新加载过程中会忽略check的行为
    var reloading = false;
    // 设置树的右键事件
    // right click node and then display the context menu
//    1.拉取组织架构，
//    2.获取最新数据
//    3.开启定时刷新功能
    $('#tt').tree({
        url: "<?php echo U('Service/VehicleInterface/getVehicleTree');?>",
        onLoadSuccess: function (node, data) {
//            每次加载要重置
            cars = new Object();
            var childrens = $("#tt").tree('getChildren');
//            car_ids = [];

            reloading = true;
            for (i = 0; i < childrens.length; i++) {
                var child = childrens[i];
                if(treeState[child.id] && treeState[child.id] != child.state){
//                        更新当前的展开状态
                    if(treeState[child.id] == 'open'){
                        $('#tt').tree('expand',child.target);
                    }else{
                        $('#tt').tree('collapse',child.target);
                    }
                }

                if (child.attributes.type == "car"){
                    var car_id = child.id % 1000000000;
                    cars[""+car_id] = new Object();
                    cars[""+car_id] = child;
                }
                if(treeCheckedState[child.id]){
//                    $('#tt').tree('check',child.target);
                    $('#tt').tree('update', {
                        target: child.target,
                        checked: true
                    });
                }
            }
            reloading = false;

//            getLastGps();
//            当前根节点id
            var root = $("#tt").tree('getRoot');
            rootGroupId = root.id;
//            SM_get_online_vehicles();
            if(interval){
                clearInterval(interval);
            }
            interval = setInterval("refresh()",1000);
            addMarkers();
        },
        onSelect: function(node){
            //选中一辆车
            if(node.attributes.type == "car"){
//                console.log(node+"选中一个车的节点");
//                选中后，下方显示车辆具体信息
                currNode = node;
            }
        },
        onCheck: function(node, checked){

            if(reloading){
                return;
            }
            console.log("选择了",node);

            for(var key in cars){
                cars[key].isChecked = false;
            }
//          添加marker到地图

            addMarkers();
        },
        onDblClick:function(node){
            if(node.attributes.type == "car"){
//                console.log(node+"选中一个车的节点");
//                选中后，下方显示车辆具体信息
                currNode = node;

                $("#tt").tree('check',node.target);
                moveToCar();
            }
        },
        onContextMenu: function (e, node) {
            e.preventDefault();

            if(node.attributes.type == "car" )
            {
                currNode = node;
                $('#setting').window('close')
                // select the node
                $('#tt').tree('select', node.target);
                // display context menu
//                TODO: 这里也需要修改
                if(currNode.attributes.carinfo.camera_count == 1){
                    $('#mm_single_channe').menu('show', {
                        left: e.pageX,
                        top: e.pageY
                    });
                }else{
                    $('#mm').menu('show', {
                        left: e.pageX,
                        top: e.pageY
                    });
                }

            }else if(node.attributes.type == "car_camera"){
                currNode = node;
                $('#setting').window('close')
                // select the node
                $('#tt').tree('select', node.target);

                $('#mm_camera').menu('show', {
                    left: e.pageX,
                    top: e.pageY
                });
            }
        }
    });
//
    function moveToCar(){
        $("#tt").tree('check',currNode.target);
        var gpsinfo = currNode.attributes.gps_info;
        if(gpsinfo){
            var point = new BMap.Point(gpsinfo.lng, gpsinfo.lat);
            map.panTo(point);
        }else{
            showTipsMessage('该车暂无GPS信息！');
        }
    }

//    显示实时视频，这里需获取当前选中的信息。
    function showRealVideo(){

        var vehicle_id = '';
        var channeIndex = '';
        if(currNode.attributes.type == 'car'){
        //直接看默认通道
            vehicle_id = currNode.attributes.carinfo.vehicle_id;
            channeIndex = currNode.camera_info[0].attributes.index;
        }else if((currNode.attributes.type == 'car_camera')) {
        // 看指定通道
            vehicle_id = currNode.attributes.vehicle_id;
            channeIndex = currNode.attributes.index;
        }

        addRealVideoTab(vehicle_id,channeIndex);
//        window.parent.UpdateTabs('Manager/Track/history','历史数据',"index.php?m=Manager&c=Track&a=history&&vehicle_id="+(currNode.id % 1000000000),history_icon);


//        var URL = "<?php echo U('RealVideo/videoForOne');?>"+"&vehicle_id="+vehicle_id+"&channe_index="+channeIndex;
//        window.open(URL);
//        $('#realVideo').window('refresh',"<?php echo U('RealVideo/videoForOne');?>");
//        $('#realVideo').window({
//            title:"查询",
//            width:600,
//            height:640,
//            top:0,
//            left:(screen.width-600)/2,
//            resizable:true,
//            collapsible:false,
//            minimizable:false,
//            maximizable:false
//        });
//        $('#realVideo').window('open');

    }

    function addRealVideoTab(vehicle_id,channeIndex){
        console.log('Manager/RealVideo/videoForOne'+"/"+vehicle_id+"_"+channeIndex);
        window.parent.UpdateTabs('Manager/RealVideo_'+vehicle_id+"_"+channeIndex+'/videoForOne','实时视频',"index.php?m=Manager&c=RealVideo&a=videoForOne"+"&vehicle_id="+vehicle_id+"&channe_index="+channeIndex,realvideo_icon);
    }

    var loopSecond = 10;

    function refresh(){
        loopSecond -=1;
        if (loopSecond<=0){
            loopSecond = 10;
            if(setting_refresh_time){
                loopSecond = setting_refresh_time;
            }
//            TODO: 执行一次数据刷新请求,上传一组车辆id，返回车辆实时信息，暂停计时，
//            console.log(car_ids);
            $('#map_tip').html("正在请求数据...");
            removeInterval();
//            getLastGps();
            reloadTree();
        }else{
            $('#map_tip').html("还有<span id='map_tip_second' style='color: #F00;padding: 0px 5px'>--s</span>刷新");
            $('#map_tip_second').html(loopSecond+"s");
        }
    }

//    重新加载树
    function reloadTree(){
//        0.存储当前组展开与收起的状态，当前只关心组织
        var childrens = $("#tt").tree('getChildren');
        for (i = 0; i < childrens.length; i++) {
            var child = childrens[i];
            if(child.attributes.type != 'car_camera'){
                treeState[child.id] = child.state
            }
            treeCheckedState[child.id] = child.checked;
        }
        var tempurl = "<?php echo U('Service/VehicleInterface/getVehicleTree');?>";
//      重新拉取数据
        $.ajax({
            url: tempurl,
            type: 'Post',
            dataType: 'Json',
            data: treeParams,
            success: function (result) {
                if(result){
                    console.log(result);
                    $('#tt').tree('loadData',result);
                }
            }
        });
//        $('#tt').tree('reload');
//      2.设置树参数
//      3.reloadTree
    }

    function removeInterval(){
        if(interval){
            clearInterval(interval);
        }
    }



    //  屏蔽右键点击
    $(document).on("contextmenu", function (event) {
        event.preventDefault();
    });

    function formatDate() {

        var now = new Date();

        var year=now.getYear();
        year = year < 1900 ? 1900+year : year;
        var month=now.getMonth()+1;
        var date=now.getDate();
        var hour=now.getHours();
        var minute=now.getMinutes();
        var second=now.getSeconds();
        return year+"-"+month+"-"+date+" "+hour+":"+minute+":"+second;
    }
//    路径回放
    function showTrack(){

        var URL = "<?php echo U('Manager/Track/index');?>"+"&vehicle_id="+(currNode.id % 1000000000);
        window.open(URL);

//        window.location.href=URL;

    }
    function showHistory(){
        window.parent.UpdateTabs('Manager/Track/history','历史数据',"index.php?m=Manager&c=Track&a=history&&vehicle_id="+(currNode.id % 1000000000),history_icon);
    }
//    跟踪，新窗口打开
    function track(){

        var URL = "<?php echo U('Manager/Index/track');?>"+"&vehicle_id="+(currNode.id % 1000000000);
        window.open(URL);
//        window.parent.UpdateTabs('Manager/Index/track'+currNode.id,'历史数据',"index.php?m=Manager&c=Index&a=track&&vehicle_id="+(currNode.id % 1000000000),'iconfont icon-history')
    }


    //    初始化地图
    init("map",latlngs);
//    添加上方提示
    addTipControl();
//    添加测距工具
    addDistanceControl();

//    添加标注到地图
    function addMarkers(){

        clearCurrMarkers();
        var childrens = $("#tt").tree('getChecked');
        for (i = 0; i < childrens.length; i++) {
            var child = childrens[i];

            if(child.attributes.type == "car"){
                var carinfo = child.attributes.carinfo;
                var gpsinfo = child.attributes.gps_info;
                if(gpsinfo){
                    var point = new BMap.Point(gpsinfo.lng, gpsinfo.lat);
                    console.log(point,"point");
                    var direction = parseInt(gpsinfo.direction/45);
                    var key = 'online';
                    if(carinfo.is_online){
                        if(gpsinfo.speed>0){
                            key = 'online';
                        }else{
                            key = 'online_stop';
                        }
                    }else{
                        key = 'offline';
                    }

                    var iconPath = "<?php echo U('Manager/Index/getCarImageUrl');?>"+"&key="+key+"&direction="+direction;

                    var myIcon = new BMap.Icon(iconPath, new BMap.Size(30, 30), {
                        // 图标中央下端的尖角位置。
                        anchor: new BMap.Size(16, 16),
                        imageSize: new BMap.Size(32, 32),
                        // 设置图片偏移。
                        // 当您需要从一幅较大的图片中截取某部分作为标注图标时，您
                        // 需要指定大图的偏移位置，此做法与css sprites技术类似。
                        imageOffset: new BMap.Size(0, 0)   // 设置图片偏移
                    });

// 创建标注对象并添加到地图


                    var marker = new BMap.Marker(point);
                    var label = new BMap.Label(carinfo.vehicle_license);
                    label.setOffset(new BMap.Size(32,20));

                    marker.setLabel(label);
                    marker.setIcon(myIcon);
                    var shadowIcon = new BMap.Icon(iconPath,new BMap.Size(0,0));
                    marker.setShadow(shadowIcon);
                    var lat = Number(gpsinfo.lat).toFixed(6);
                    var lng =  Number(gpsinfo.lng).toFixed(6);

                    var camera_div = "";


                    if(child.attributes.carinfo.camera_count>0){
                        camera_div = "<br><hr><p><span>视频通道：</span>";

                        for(var j=0;j < child.camera_info.length;j++){
                            var camera = child.camera_info[j];
//                            var URL = "<?php echo U('RealVideo/videoForOne');?>"+"&vehicle_id="+camera.attributes.vehicle_id+"&channe_index="+camera.attributes.index;

                            var camera_p = "<a href='#' onclick='addRealVideoTab("+camera.attributes.vehicle_id+","+camera.attributes.index+")'>"+camera.text;

                            if(j != child.camera_info.length-1){
                                camera_p += "<span style='padding-left: 4px;padding-right: 4px'>|</span>";
                            }
                            camera_div += camera_p+"</a>";
                        }
                        camera_div += "</p>";
//                        marker.addContextMenu(markerMenu);
                    }
                    console.log('carinfo',carinfo);

                    var div =
                            '<div>'+
                            '<h3 style="text-align: center;padding-bottom: 2px">'+carinfo.vehicle_license+'</h3><hr>'+
                            '<p style="padding-top: 8px"><span class="car_info_title">经度：</span><span id="lng">'+lng+'</span></p>'+
                            '<p><span class="car_info_title">纬度：</span><span id="lat">'+lat+'</span></p>'+
                            '<p><span class="car_info_title">速度：</span><span id="speed">'+gpsinfo.speed+'km/h</span></p>'+
                            '<p><span class="car_info_title">驾驶员：</span><span>'+carinfo.vehicle_driver+'</span></p>'+
                            '<p><span class="car_info_title">上报时间：</span><span id="time">'+gpsinfo.time+'</span></p>'+
                            camera_div+
                            '</div>';
                    console.log("div",div);
                    marker.div = div;

                    marker.addEventListener("click", function(){
                        var opts = {
                            width : 300,     // 信息窗口宽度
//                        height: 120,     // 信息窗口高度
//                        title : car.carinfo.vehicle_license // 信息窗口标题
                        }
                        console.log("this is :",this);
                        var infoWindow = new BMap.InfoWindow(this.div,opts);  // 创建信息窗口对象
                        this.openInfoWindow(infoWindow, marker.point);
                        //alert("您点击了标注");
                    });
//                    var markerMenu=new BMap.ContextMenu();
//
//                    markerMenu.addItem(new BMap.MenuItem('删除',alert('delete')));
//
//                    marker.addContextMenu(markerMenu);


                    addCurrMarkers(marker);
                }
            }
        }

    }

</script>


</body>
</html>