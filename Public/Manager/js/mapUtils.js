/**
 * Created by lgq on 16/7/26.
 */

var map, layer,url = "http://test.51zsqc.com:8090/iserver/services/map-neimeng/rest/maps/Map_1";

var drawLine,lineLayer;
var defaultCenter =new SuperMap.LonLat(111.732715, 40.799398);
var defaultZoom = 4;

//图形绘制图层
var vectorLayer;
var labelLayer;
//绘制矩形选区
var drawRectangleHandle;
//绘制圆形选区
var drawRingHandle;
//绘制线
var drawLineHandle;
//绘制点
var drawPointHandle;
var markerlayer;

var markerLabels = {};

//用于绘制当前车辆
var carslayer;


//当前鼠标所在的位置
var currMouseX;
var currMouseY;
var currMouseLonLat;

function init(){
    lineLayer = new SuperMap.Layer.Vector("绘线图层");
    lineLayer.style = {
        strokeWidth:2,
        strokeColor: "#304DBE",
        pointerEvents:"visiblePainted",
        fillColor:"#304DBE",
        fillOpacity: 0.8
    };
    drawLine = new SuperMap.Control.DrawFeature(lineLayer,SuperMap.Handler.Path,{multi: true});
    vectorLayer = new SuperMap.Layer.Vector("要素图层");

    drawRectangleHandle = new SuperMap.Control.DrawFeature(vectorLayer,SuperMap.Handler.Box);
    drawRectangleHandle.events.on({"featureadded":drawRectangleCompleted});

    drawRingHandle = new SuperMap.Control.DrawFeature(vectorLayer, SuperMap.Handler.RegularPolygon,{handlerOptions:{sides:50}});
    drawRingHandle.events.on({"featureadded": drawRingCompleted});

    drawLineHandle = new SuperMap.Control.DrawFeature(vectorLayer,SuperMap.Handler.Path,{multi: true});
    drawLineHandle.events.on({"featureadded": drawLineCompleted});


    drawPointHandle = new SuperMap.Control.DrawFeature(vectorLayer,SuperMap.Handler.Point,{multi: true});
    drawPointHandle.events.on({"featureadded": drawPointCompleted});

    markerlayer = new SuperMap.Layer.Markers("图标图层");

    labelLayer = new SuperMap.Layer.Vector("标签图层");

    /*
     注册featureadded事件,触发drawCompleted()方法
     例如注册"loadstart"事件的单独监听
     events.on({ "loadstart": loadStartListener });
     */
    drawLine.events.on({"featureadded":drawCompleted});

//初始化地图
    map = new SuperMap.Map("map",{controls:[
        new SuperMap.Control.ScaleLine(),//比例尺
        new SuperMap.Control.LayerSwitcher(),//图层切换工具
        new SuperMap.Control.Zoom(),// 缩放工具
        new SuperMap.Control.OverviewMap(),//预览小窗口
        new SuperMap.Control.Navigation({dragPanOptions:{enableKinetic:true}}),
    ]});
    map.addControl(new SuperMap.Control.MousePosition());//显示经纬度
//初始化图层
    layer = new SuperMap.Layer.TiledDynamicRESTLayer("底图", url, null,{maxResolution:"auto"});
//监听图层信息加载完成事件
    layer.events.on({"layerInitialized":addLayer});

    map.addControl(drawLine);
    map.addControl(drawRectangleHandle);
    map.addControl(drawRingHandle);
    map.addControl(drawLineHandle);
    map.addControl(drawPointHandle);
    map.events.on({"mousemove":getMousePositionPx});

}


//异步加载图层
function addLayer(){
    //map.addLayer(layer);
    map.addLayers([layer, lineLayer,vectorLayer,markerlayer,labelLayer]);

    //显示地图范围
    map.setCenter(defaultCenter, 4);
}




//--------------测距功能----------------------
//绘完触发事件
function drawCompleted(drawGeometryArgs) {
//停止画面控制
    drawLine.deactivate();
//获得图层几何对象
    var geometry = drawGeometryArgs.feature.geometry,
        measureParam = new SuperMap.REST.MeasureParameters(geometry), /* MeasureParameters：量算参数类。 客户端要量算的地物间的距离或某个区域的面积*/
        myMeasuerService = new SuperMap.REST.MeasureService(url); //量算服务类，该类负责将量算参数传递到服务端，并获取服务端返回的量算结果
    myMeasuerService.events.on({ "processCompleted": measureCompleted });

//对MeasureService类型进行判断和赋值，当判断出是LineString时设置MeasureMode.DISTANCE，否则是MeasureMode.AREA

    myMeasuerService.measureMode = SuperMap.REST.MeasureMode.DISTANCE;

    myMeasuerService.processAsync(measureParam); //processAsync负责将客户端的量算参数传递到服务端。

}

function distanceMeasure(){
    clearFeatures();
    drawLine.activate();
}

//测量结束调用事件
function measureCompleted(measureEventArgs) {
    var distance = measureEventArgs.result.distance;
    var unit = measureEventArgs.result.unit;
    alert("量算结果:"+distance + "米");
    clearFeatures();
}

//移除图层要素
function clearFeatures(){
    lineLayer.removeAllFeatures();
}
//--------------测距功能----------------------\\

//--------------设置及回到默认视眼----------------------
function saveDefaultCenter(){
    defaultCenter = map.getCenter();
    defaultZoom = map.getZoom();
    showTipsMessage("设置默认视野成功");
}

function goToDefaultCenter(){
    map.panTo(defaultCenter);
    map.zoomTo(defaultZoom);

}
//--------------设置及回到默认视眼----------------------\\

//表示当前操作为 选择区域 true
var isSelected = true;
var currSelectedDatas = new Array();
var currFenceDatas = null ;

//--------------矩形绘制----------------------

function beginDrawRectangle(selected){
    vectorLayer.removeAllFeatures();
    drawRectangleHandle.activate();
    isSelected = selected;


}
//--------------矩形绘制完成----------------------
function drawRectangleCompleted(obj){
    drawRectangleHandle.deactivate();
    var bounds = obj.feature.geometry.bounds;
    console.log("绘制完成");
    console.log(bounds);

    if(!isSelected){
        processFenceRectangle(bounds);
    }else{
        processSelectedRectangle(bounds);
    }
}
//处理矩形绘制电子围栏
function processFenceRectangle(bounds){
    isSelected = true;
    cleanSelected();
    //alert("绘制矩形地理围栏，接下来要显示编辑内容window，加载其他的页面");
    var url = baseAddFenceUrl+"Rect&windowId=setting";
    showFenceWin('添加矩形区域',url,bounds);
}

//处理矩形绘制选区
function processSelectedRectangle(bounds){
    isSelected = false;
    //将所有在bounds的marker显示，
    //1.找出所有选中的项，
    //2.循环比较，看是否在范围内，
    //3.将范围内的数据以对话框的形式显示
    currSelectedDatas = new Array();

    for(var key in cars) {
        var car = cars[key];
        if (car.isChecked) {
            //对选中项进行范围比较
            var info = new Object();
            if (car.realinfo) {
                info =car.realinfo;
            } else if (car.history) {
                info =car.history;
            } else {
                //无有效数据
                continue;
            }
            if (info.lat >= bounds.bottom && info.lat <= bounds.top && info.lng >= bounds.left && info.lng <= bounds.right) {

                var status = info["status"];
                var value1 = (((1<<0) & status )>>0)+"";
                var value2 = (((1<<1) & status )>>1)+"";
                var state = "ACC"+rule["0"][value1]+";"+rule["1"][value2];

                var d = {
                    "car_license":car.car_license,
                    "group_name": car.group_name,
                    "lng":info.lng,
                    "lat":info.lat,
                    "speed":info.speed,
                    "state":state,
                    "online":car.isOnline?"在线":"下线"
                }
                currSelectedDatas.push(d);
                console.log("范围内数据", d);

            }
        }
    }

    if(currSelectedDatas.length > 0){
        showSelectedWin();
    }
}



//--------------圆形绘制----------------------\\

function beginDrawRing(selected){
    vectorLayer.removeAllFeatures();
    drawRingHandle.activate();
    isSelected = selected;


}
//--------------圆形绘制完成----------------------\\
function drawRingCompleted(obj){
    drawRingHandle.deactivate();
    var geometry = obj.feature.geometry;


    console.log("绘制完成");
    console.log(geometry);

    if(!isSelected){
        processFenceRing(geometry);
    }else{
        processSelectedRing(geometry);
    }
}

//处理圆形形绘制电子围栏
function processFenceRing(geometry){
    isSelected = true;
    cleanSelected();
    //alert("绘制圆形地理围栏，接下来要显示编辑内容window，加载其他的页面");
    var url = baseAddFenceUrl+"Ring&windowId=setting";

    showFenceWin('添加圆形区域',url,geometry);
}

//处理圆形绘制选区
function processSelectedRing(geometry){
    var bounds = geometry.bounds;
    isSelected = false;
    //将所有在bounds的marker显示，
    //1.找出所有选中的项，
    //2.循环比较，看是否在范围内，
    //3.将范围内的数据以对话框的形式显示
    currSelectedDatas = new Array();

    for(var key in cars) {
        var car = cars[key];
        if (car.isChecked) {
            //对选中项进行范围比较
            var info = new Object();
            if (car.realinfo) {
                info =car.realinfo;
            } else if (car.history) {
                info =car.history;
            } else {
                //无有效数据
                continue;
            }

            //判断是否在圆形区域中

            if (info.lat >= bounds.bottom && info.lat <= bounds.top && info.lng >= bounds.left && info.lng <= bounds.right) {

                var status = info["status"];
                var value1 = (((1<<0) & status )>>0)+"";
                var value2 = (((1<<1) & status )>>1)+"";
                var state = "ACC"+rule["0"][value1]+";"+rule["1"][value2];

                var d = {
                    "car_license":car.car_license,
                    "group_name": car.group_name,
                    "lng":info.lng,
                    "lat":info.lat,
                    "speed":info.speed,
                    "state":state,
                    "online":car.isOnline?"在线":"下线"
                }
                currSelectedDatas.push(d);
                console.log("范围内数据", d);

            }
        }
    }

    if(currSelectedDatas.length > 0){
        showSelectedWin();
    }

    //alert("绘制圆形选区，直接呈现内容");

}

function showFenceWin(title,url,datas){
    console.log(title,url,datas);
    currFenceDatas = datas;
    $('#setting').window('refresh', url);
    $('#setting').window({
            title: title,
            width: 600,
            height: ($(window).height() - 300),
            left:($(window).width() - 600) * 0.5,
            top:80
        });
    $('#setting').window('open');
}

//展示选区结果
function showSelectedWin(){

    $('#setting').window('refresh', showSelectedDatasUrl);
    $('#setting').window({
        title: "车辆列表",
        width: 800,
        height: ($(window).height() - 200),
        left:($(window).width() - 800) * 0.5,
        top:50,
        onClose:function(){
            currSelectedDatas = new Array();
            cleanSelected();
            console.log("关闭车辆列表，清空数据")
        },
        onDestroy: function(){
            currSelectedDatas = new Array();
            cleanSelected();
            console.log("关闭车辆列表，清空数据");
        }
    }
    );
    $('#setting').window('open');

}

function beginDrawLine(selected){
    vectorLayer.removeAllFeatures();
    drawLineHandle.activate();
    isSelected = selected;

}
function drawLineCompleted(obj){
    drawLineHandle.deactivate();
    var geometry = obj.feature.geometry;
    console.log("绘制线完成，数据为");
    console.log( geometry.components[0].components);
    if(!isSelected){
        processFenceLine(geometry.components[0].components);
    }else{
        processSelectedLine(geometry.components[0].components);
    }

}

//处理圆形形绘制电子围栏
function processFenceLine(points){
    isSelected = true;
    //alert("绘制线路地理围栏，接下来要显示编辑内容window，加载其他的页面");
    cleanSelected();
    var url = baseAddFenceUrl+"Line&windowId=setting";
    showFenceWin('添加线路',url,points);
}

//处理圆形绘制选区
function processSelectedLine(points){
    isSelected = false;
    alert("绘制线路选区，直接呈现内容");


}


function beginDrawPoint(){
    vectorLayer.removeAllFeatures();
    drawPointHandle.activate();

}
function drawPointCompleted(obj){
    drawPointHandle.deactivate();
    var geometry = obj.feature.geometry;
    console.log("绘制线完成，数据为");
    console.log( geometry);
    showAddMarkerWindows(geometry);

}


function showAddMarkerWindows(geometry){
//    弹出文本编辑对话框
    $('#setting').window('close');

    $('#setting').window('refresh', baseAddMarkerUrl+"&lat="+geometry.components[0].y+"&lng="+geometry.components[0].x);
    $('#setting').window({
        title:"添加标注"
    });
    $('#setting').window('open');
}



//清除选区
function cleanSelected(){
    vectorLayer.removeAllFeatures();
    //MARK-TODO ： 待移除
    //send();
}


//绘制选中的车辆
function addMarkers(){
    cleanMarkers();
    for (var car_id in cars){
        if (cars[car_id].isChecked){
            addMarkerToMap(car_id);
        }
    }
}


//是否显示
function isMarkerShow(car_id){
    for (i=0;i<markerlayer.markers.length;i++){
        var m = markerlayer.markers[i];
        if (m.vehicle_id == car_id){
            return true
        }
    }
    return false;
}

// 添加marker
function addMarkerToMap(car_id){
    cleanMarker(car_id);
    var host =  window.location.host;
    console.log("host:",host);
    if(cars[car_id]){
        var car = cars[car_id];
        var realinfo = car.realinfo;
        if (realinfo){
            console.log("addMarkerToMap 找到了",realinfo);

            var lat = realinfo.lat;
            var lng = realinfo.lng;

            var size = new SuperMap.Size(30,30);
            var offset = new SuperMap.Pixel(-(size.w/2), -size.h);

            var direction = parseInt( realinfo.direction / 45);
            console.log("方向是：",direction);
            var iconUrl = car.isOnline? '../TobaccoGms/Public/Manager/image/1/'+direction+'.gif':'../TobaccoGms/Public/Manager/image/0/'+direction+'.gif';
            console.log("iconUrl",iconUrl);
            var icon = new SuperMap.Icon(iconUrl, size, offset);
            marker = new SuperMap.Marker(new SuperMap.LonLat(lng,lat),icon) ;
            marker.vehicle_id = car_id;
            marker.events.on({
                "click":openInfoWin,
                "scope":marker
            });
            markerlayer.addMarker(marker);
            //    todo:
            var geometry = new SuperMap.Geometry.Point(lng,lat);
            var pointFeature = new SuperMap.Feature.Vector(geometry);
            var styleTest = {
                label:cars[car_id].car_license,
                fontColor:"#0000ff",
                fontOpacity:"1",
                fontFamily:"隶书",
                fontSize:"1em",
                fontWeight:"bold",
                fontStyle:"italic",
                labelSelect:"true",
                labelYOffset:-5
            }
            pointFeature.style = styleTest;
            labelLayer.addFeatures([pointFeature]);
            markerLabels[car_id] = pointFeature;

        }else if (car.history){
            var lat = car.history.lat;
            var lng = car.history.lng;
            var size = new SuperMap.Size(30,30);
            var offset = new SuperMap.Pixel(-(size.w/2), -size.h);

            var direction = parseInt( car.history.direction / 45);
            console.log("方向是：",direction);

            var iconUrl = '../TobaccoGms/Public/Manager/image/0/'+direction+'.gif';
            var icon = new SuperMap.Icon(iconUrl, size, offset);
            marker = new SuperMap.Marker(new SuperMap.LonLat(lng,lat),icon) ;
            marker.vehicle_id = car_id;
            marker.events.on({
                "click":openInfoWinByHistory,
                "scope":marker
            });
            markerlayer.addMarker(marker);
        //    todo:
            var geometry = new SuperMap.Geometry.Point(lng,lat);
            var pointFeature = new SuperMap.Feature.Vector(geometry);
            var styleTest = {

                label:cars[car_id].car_license,
                fill:true,
                fillColor:"#eeffee",
                fontColor:"#0000ff",
                fontOpacity:"1",
                fontFamily:"隶书",
                fontSize:"1em",
                fontWeight:"bold",
                fontStyle:"italic",
                labelSelect:"true",
                labelYOffset:-5
            }
            pointFeature.style = styleTest;
            labelLayer.addFeatures([pointFeature]);
            markerLabels[car_id] = pointFeature;
        }
    }
}

var infowin = null;
function openInfoWin(){
    closeInfoWin();
    var marker = this;
    var lonlat = marker.getLonLat();
    var size = new SuperMap.Size(0, 33);
    var offset = new SuperMap.Pixel(11, -30);
    var icon = new SuperMap.Icon("../theme/images/marker.png", size, offset);

    //获取car的信息，并呈现在popup中
    car= cars[marker.vehicle_id];
    //TODO 待测试
    var directionDesc = ["正北","东北","正东","东南","正南","西南","正西","西北"];

    var direction = parseInt( car.realinfo.direction / 45);
    var div = '<div>'+
        '<p style="text-align: center"><h3>'+car.carinfo.car_license+'</h3></p><hr><br>'+
        '<p><span width = "50px">车牌颜色:</span> <span style="padding-left: 20px">'+car.carinfo.plate_color+'</span></p>'+
        '<p><span width = "50px">车组名称:</span> <span style="padding-left: 20px">'+car.carinfo.group_name+'</span></p>'+
        '<p><span width = "50px">GPS时间:</span> <span style="padding-left: 20px">'+car.realinfo.time+'</span></p>'+
        '<p><span width = "50px">经____度:</span> <span style="padding-left: 20px">'+car.realinfo.lng+'</span></p>'+
        '<p><span width = "50px">纬____度:</span> <span style="padding-left: 20px">'+car.realinfo.lat+'</span></p>'+
        '<p><span width = "50px">高____度:</span> <span style="padding-left: 20px">'+car.realinfo.altitude+'</span></p>'+
        '<p><span width = "50px">方____向:</span> <span style="padding-left: 20px">'+directionDesc[direction]+'</span></p>'+
        '<p><span width = "50px">GPS速度:</span> <span style="padding-left: 20px">'+car.realinfo.speed+'</span></p>'+
        '</div>';
    var popup = new SuperMap.Popup.FramedCloud("popwin",
        new SuperMap.LonLat(lonlat.lon,lonlat.lat),
        null,
        div,
        icon,
        true);
    infowin = popup;
    map.addPopup(popup);
}
function openInfoWinByHistory(){
    closeInfoWin();
    var marker = this;
    var lonlat = marker.getLonLat();
    var size = new SuperMap.Size(0, 33);
    var offset = new SuperMap.Pixel(11, -30);
    var icon = new SuperMap.Icon("../theme/images/marker.png", size, offset);

    //获取car的信息，并呈现在popup中
    car= cars[marker.vehicle_id];
    //TODO 待测试
    var directionDesc = ["正北","东北","正东","东南","正南","西南","正西","西北"];
    var direction = parseInt( car.history.direction / 45);
    var div = '<div>'+
        '<p style="text-align: center"><h3>'+car.carinfo.car_license+'</h3></p><hr><br>'+
        '<p><span width = "50px">车牌颜色:</span> <span style="padding-left: 20px">'+car.carinfo.plate_color+'</span></p>'+
        '<p><span width = "50px">车组名称:</span> <span style="padding-left: 20px">'+car.carinfo.group_name+'</span></p>'+
        '<p><span width = "50px">GPS时间:</span> <span style="padding-left: 20px">'+car.history.time+'</span></p>'+
        '<p><span width = "50px">经____度:</span> <span style="padding-left: 20px">'+car.history.lng+'</span></p>'+
        '<p><span width = "50px">纬____度:</span> <span style="padding-left: 20px">'+car.history.lat+'</span></p>'+
        '<p><span width = "50px">高____度:</span> <span style="padding-left: 20px">'+car.history.altitude+'</span></p>'+
        '<p><span width = "50px">方____向:</span> <span style="padding-left: 20px">'+directionDesc[direction]+'</span></p>'+
        '<p><span width = "50px">GPS速度:</span> <span style="padding-left: 20px">'+car.history.speed+'</span></p>'+
        '</div>';
    var popup = new SuperMap.Popup.FramedCloud("popwin",
        new SuperMap.LonLat(lonlat.lon,lonlat.lat),
        null,
        div,
        icon,
        true);
    infowin = popup;
    map.addPopup(popup);
}


//关闭信息框
function closeInfoWin() {
    if (infowin) {
        try {
            infowin.hide();
            infowin.destroy();
        }
        catch (e) {
        }
    }
}

function cleanMarkers(){
    console.log("cleanMarkers");
    markerlayer.clearMarkers();
    markerLabels = {};
    labelLayer.removeAllFeatures();
}
function cleanMarker(car_id){
    for (i=0;i<markerlayer.markers.length;i++){
        var m = markerlayer.markers[i];
        if (m.vehicle_id == car_id){
            markerlayer.removeMarker(m);

            if(markerLabels[car_id]){
                labelLayer.removeFeatures(markerLabels[car_id]);
                markerLabels[car_id] = null;
            }
        }
    }
}

// 地图视眼移动到该中心点
function moveToCar(){
    var car_id = currNode.id % 1000000000;
    if(cars[car_id]){
        var car = cars[car_id];
        var realinfo = car.realinfo;
        if (realinfo){
            console.log("找到了",realinfo);
            var lat = realinfo.lat;
            var lng = realinfo.lng;
            map.panTo(new SuperMap.LonLat(lng,lat));
        }else if (car.history){
            console.log("使用历史记录",car.history);
            var lat = car.history.lat;
            var lng = car.history.lng;
            map.panTo(new SuperMap.LonLat(lng,lat));
        }else {
            showTipsMessage("未找到有效数据");
        }
    }
}

//监听鼠标位置
function getMousePositionPx(e)
{
    currMouseLonLat= map.getLonLatFromPixel(new SuperMap.Pixel(e.xy.x,e.xy.y));
    currMouseX = Math.floor(e.clientX);
    currMouseY = Math.floor(e.clientY);
}
//{"start_time":"2016-08-26 16:27:27","alarm_source":2,"alarm_id":0,
// "vehicle_id":0,"speed":0,"status":262147,
// "lat":40.799558,"lng":111.732488,"direction":258,"id":153}
function updateCarWithAlarmStatus(alarmInfo){


    var car_id = alarmInfo.vehicle_id;
    if (!isMarkerShow(car_id)){
        return;
    }
    if(cars[car_id]){
        var lat = alarmInfo.lat;
        var lng = alarmInfo.lng;

        var size = new SuperMap.Size(30,30);
        var offset = new SuperMap.Pixel(-(size.w/2), -size.h);

        var direction = parseInt( alarmInfo.direction / 45);
        console.log("方向是：",direction);
        var iconUrl = '../TobaccoGms/Public/Manager/image/2/'+direction+'.gif';
        var icon = new SuperMap.Icon(iconUrl, size, offset);
        marker = new SuperMap.Marker(new SuperMap.LonLat(lng,lat),icon) ;
        marker.vehicle_id = car_id;
        marker.alarmInfo = alarmInfo;
        marker.events.on({
            "click":openInfoWin,
            "scope":marker
        });
        var geometry = new SuperMap.Geometry.Point(lng,lat);
        var pointFeature = new SuperMap.Feature.Vector(geometry);
        var styleTest = {
            label:cars[car_id].car_license,
            fontColor:"#0000ff",
            fontOpacity:"1",
            fontFamily:"隶书",
            fontSize:"1em",
            fontWeight:"bold",
            fontStyle:"italic",
            labelSelect:"true",
            labelYOffset:-5
        }
        pointFeature.style = styleTest;
        labelLayer.addFeatures([pointFeature]);
        markerLabels[car_id] = pointFeature;

        markerlayer.addMarker(marker);
    }
}

function openAlarmInfo(){
    closeInfoWin();
    var marker = this;
    var lonlat = marker.getLonLat();
    var size = new SuperMap.Size(0, 33);
    var offset = new SuperMap.Pixel(11, -30);
    var icon = new SuperMap.Icon("../theme/images/marker.png", size, offset);

    //获取car的信息，并呈现在popup中
    var alarmInfo = marker.alarmInfo;
    var car= cars[marker.vehicle_id];

    if (alarmInfo && car){
        //TODO 待测试
        var directionDesc = ["正北","东北","正东","东南","正南","西南","正西","西北"];
        var direction = parseInt( alarmInfo.direction / 45);
        var div = '<div>'+
            '<p style="text-align: center"><h3>'+car.carinfo.car_license+'</h3></p><hr><br>'+
            '<p><span width = "50px">车牌颜色:</span> <span style="padding-left: 20px">'+car.carinfo.plate_color+'</span></p>'+
            '<p><span width = "50px">车组名称:</span> <span style="padding-left: 20px">'+car.carinfo.group_name+'</span></p>'+
            '<p><span width = "50px">GPS时间:</span> <span style="padding-left: 20px">'+alarmInfo.time+'</span></p>'+
            '<p><span width = "50px">经____度:</span> <span style="padding-left: 20px">'+alarmInfo.lng+'</span></p>'+
            '<p><span width = "50px">纬____度:</span> <span style="padding-left: 20px">'+alarmInfo.lat+'</span></p>'+
            '<p><span width = "50px">高____度:</span> <span style="padding-left: 20px">'+alarmInfo.altitude+'</span></p>'+
            '<p><span width = "50px">方____向:</span> <span style="padding-left: 20px">'+directionDesc[direction]+'</span></p>'+
            '<p><span width = "50px">GPS速度:</span> <span style="padding-left: 20px">'+alarmInfo.speed+'</span></p>'+
            '</div>';
        var popup = new SuperMap.Popup.FramedCloud("popwin",
            new SuperMap.LonLat(lonlat.lon,lonlat.lat),
            null,
            div,
            icon,
            true);
        infowin = popup;
        map.addPopup(popup);
    }else {
        return;
    }

}

// 测试获取地址
//function getAddress(){
//
//    var point = new SuperMap.Geometry.Point(111.732715, 40.799398);
//
//    var myQueryByDistParameters = new SuperMap.REST.QueryByDistanceParameters({
//        queryParams: new Array(new SuperMap.REST.FilterParameter({name: "乡镇村道L@neimeng.1"})),
//        geometry:point,
//        returnContent: true,
//        //isNeast:true,
//        distance:10,
//    });
//    ///iserver/services/map-world/rest/maps/World
//    var url2 = 'http://test.51zsqc.com:8090/iserver/services/map-neimeng/rest/maps/Map_1';
//    var myQueryByDistService = new SuperMap.REST.QueryByDistanceService(url2, {
//        eventListeners: {
//            "processCompleted": processCompleted,
//            "processFailed": processFailed
//        }
//    });
//    myQueryByDistService.processAsync(myQueryByDistParameters);
//
//}
//
//function processCompleted(queryEventArgs) {
//    console.log("查询最近点",queryEventArgs);
//    alert(queryEventArgs);
//
//
//}
//function processFailed(e) {
//    console.log("查询最近点失败",e);
//
//    alert(e.error.errorMsg);
//}
//getAddress();

function measureDistance(point1,point2,onsucess){
    var points = [point1,point2];
    var geometry = new SuperMap.Geometry.LineString(points);
    var measureParam = new SuperMap.REST.MeasureParameters(geometry); /* MeasureParameters：量算参数类。 客户端要量算的地物间的距离或某个区域的面积*/
    var myMeasuerService = new SuperMap.REST.MeasureService(url);
    myMeasuerService.events.on({ "processCompleted": onsucess });
    myMeasuerService.measureMode = SuperMap.REST.MeasureMode.DISTANCE;
    myMeasuerService.processAsync(measureParam); //processAsync负责将客户端的量算参数传递到服务端。

}