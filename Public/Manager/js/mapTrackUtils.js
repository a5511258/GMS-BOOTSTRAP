/**
 * Created by lgq on 16/8/15.
 */

var map, layer,url = "http://test.51zsqc.com:8090/iserver/services/map-neimeng/rest/maps/Map_1";

//线路图层
var drawLine,lineLayer;
var defaultCenter =new SuperMap.LonLat(111.732715, 40.799398);
var defaultZoom = 8;
//标注图层
var markerlayer;

var markerCar = null;


function init(){
    lineLayer = new SuperMap.Layer.Vector("lineLayer");
    lineLayer.style = {
        strokeWidth:2,
        strokeColor: "#0000ff",
        pointerEvents:"visiblePainted",
        fillColor:"#0000ff",
        fillOpacity: 0.8
    };
    drawLine = new SuperMap.Control.DrawFeature(lineLayer,SuperMap.Handler.Path,{multi: true});
    vectorLayer = new SuperMap.Layer.Vector("vectorLayer");

    markerlayer = new SuperMap.Layer.Markers("markerLayer");

    drawLine.events.on({"featureadded":drawCompleted});

//初始化地图
    map = new SuperMap.Map("map",{controls:[
        new SuperMap.Control.ScaleLine(),//比例尺
        new SuperMap.Control.LayerSwitcher(),//图层切换工具
        new SuperMap.Control.Zoom(),// 缩放工具
        //new SuperMap.Control.OverviewMap(),//预览小窗口
        new SuperMap.Control.Navigation({dragPanOptions:{enableKinetic:true}}),
    ]});
    map.addControl(new SuperMap.Control.MousePosition());//显示经纬度
//初始化图层
    layer = new SuperMap.Layer.TiledDynamicRESTLayer("aaa", url, null,{maxResolution:"auto"});
//监听图层信息加载完成事件
    layer.events.on({"layerInitialized":addLayer});
    map.addControl(drawLine);

}

//异步加载图层
function addLayer(){
    //map.addLayer(layer);
    map.addLayers([layer, lineLayer,vectorLayer,markerlayer]);
    //显示地图范围
    map.setCenter(defaultCenter, defaultZoom);
}

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

function addCarMarker(info){
    if(markerCar){
        markerlayer.removeMarker(markerCar);
        markerCar = null;
    }
    //需添加移除marker操作

    var lat = info.lat;
    var lng = info.lng;

    var size = new SuperMap.Size(30,30);
    var offset = new SuperMap.Pixel(-(size.w/2), -size.h);

    var direction = parseInt( info.direction / 45);
    console.log("方向是：",direction);
    var lnglat = new SuperMap.LonLat(lng,lat)
    map.panTo(lnglat);
    var iconUrl = info.speed != 0 ? '../TobaccoGms/Public/Manager/image/1/'+direction+'.gif':'../TobaccoGms/Public/Manager/image/0/'+direction+'.gif';
    var icon = new SuperMap.Icon(iconUrl, size, 0);
    markerCar = new SuperMap.Marker(lnglat,icon) ;
    markerCar.vehicle_id = info.vehicle_id;
    markerCar.info = info;
    markerCar.events.on({
        "click":openInfoWin,
        "scope":markerCar
    });
    markerlayer.addMarker(markerCar);
}


function showInfos(info){

    //TODO 待测试
    var directionDesc = ["正北","东北","正东","东南","正南","西南","正西","西北"];

    var direction = parseInt( info.direction / 45);
    var div = '<div>'+
        '<p><span width = "50px">GPS时间:</span> <span style="padding-left: 20px">'+info.time+'</span></p>'+
        '<p><span width = "50px">经____度:</span> <span style="padding-left: 20px">'+info.lng+'</span></p>'+
        '<p><span width = "50px">纬____度:</span> <span style="padding-left: 20px">'+info.lat+'</span></p>'+
        '<p><span width = "50px">高____度:</span> <span style="padding-left: 20px">'+info.altitude+'</span></p>'+
        '<p><span width = "50px">方____向:</span> <span style="padding-left: 20px">'+directionDesc[direction]+'</span></p>'+
        '<p><span width = "50px">GPS速度:</span> <span style="padding-left: 20px">'+info.speed+'</span></p>'+
        '</div>';
    return div;
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
    info = marker.info;
    //TODO 待测试
    var directionDesc = ["正北","东北","正东","东南","正南","西南","正西","西北"];

    var direction = parseInt( info.direction / 45);
    var div = '<div>'+
        '<p style="text-align: center"><h3>'+info.car_license+'</h3></p><hr><br>'+
        '<p><span width = "50px">车牌颜色:</span> <span style="padding-left: 20px">'+info.plate_color+'</span></p>'+
        '<p><span width = "50px">车组名称:</span> <span style="padding-left: 20px">'+info.group_name+'</span></p>'+
        '<p><span width = "50px">GPS时间:</span> <span style="padding-left: 20px">'+info.realinfo.time+'</span></p>'+
        '<p><span width = "50px">经____度:</span> <span style="padding-left: 20px">'+info.lng+'</span></p>'+
        '<p><span width = "50px">纬____度:</span> <span style="padding-left: 20px">'+info.lat+'</span></p>'+
        '<p><span width = "50px">高____度:</span> <span style="padding-left: 20px">'+info.altitude+'</span></p>'+
        '<p><span width = "50px">方____向:</span> <span style="padding-left: 20px">'+directionDesc[direction]+'</span></p>'+
        '<p><span width = "50px">GPS速度:</span> <span style="padding-left: 20px">'+info.speed+'</span></p>'+
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
function moveCarMarker(info){
    //markerlayer.removeMarker(markerCar);
    //markerCar = null;
    addCarMarker(info);
}

