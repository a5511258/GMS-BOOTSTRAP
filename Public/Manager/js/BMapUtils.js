
var map;
var defaultCenter;
var defaultZoom = 13;
var myDis;

var mapCurrMarkers = [];

function addCurrMarkers(marker){
    mapCurrMarkers.push(marker);
    map.addOverlay(marker);
}

function clearCurrMarkers(){
    for(var k = 0; k < mapCurrMarkers.length; k++){
        map.removeOverlay(mapCurrMarkers[k]);
    }
    mapCurrMarkers = [];
}
// 定义一个控件类,即function ,用于显示测距工具
function DistanceControl(){
    // 默认停靠位置和偏移量
    this.defaultAnchor = BMAP_ANCHOR_TOP_LEFT;
    this.defaultOffset = new BMap.Size(20, 220);
}
// 定义一个控件类,即function 用于显示读秒倒计时
function TipControl(){
    // 默认停靠位置和偏移量
    this.defaultAnchor = BMAP_ANCHOR_TOP_LEFT;
    this.defaultOffset = new BMap.Size(65, 10);
}
// 用于地图初始化
function init(id,latlngs){
    if(latlngs){
        defaultCenter = new BMap.Point(latlngs[0], latlngs[1]);
        console.log("初始化用了latlngs");
    }else{
        defaultCenter = new BMap.Point(111.661932, 40.828756);
    }
    if(!id){
        id = "map";
    }
    map = new BMap.Map(id);          // 创建地图实例
    var point = defaultCenter;  // 创建点坐标
    map.centerAndZoom(point, defaultZoom);                 // 初始化地图，设置中心点坐标和地图级别
    map.addControl(new BMap.NavigationControl());           // 地图缩放及移动控件，默认在左上角
    map.addControl(new BMap.ScaleControl());                // 缩放控件
    map.addControl(new BMap.OverviewMapControl());          //  鹰眼视图，默认在地图右下角
    //map.addControl(new BMap.GeolocationControl());            // 用于显示定位控件，基于ip定位
    map.enableScrollWheelZoom();                                // 开启滑轮缩放地图
}
//添加测距工具
function addDistanceControl(){
    // 通过JavaScript的prototype属性继承于BMap.Control
    DistanceControl.prototype = new BMap.Control();

// 自定义控件必须实现自己的initialize方法,并且将控件的DOM元素返回
// 在本方法中创建个div元素作为控件的容器,并将其添加到地图容器中
    DistanceControl.prototype.initialize = function(map){
        // 创建一个DOM元素
        var div = document.createElement("div");
        // 添加文字说明
        //div.appendChild(document.createTextNode("测距"));
        // 设置样式
        div.innerHTML = "<image src='/CourtGms/Public/Manager/image/ruler.png' title='测距' style='width: 30px;height: 30px'></image>"
        div.style.cursor = "pointer";
        //div.style.border = "1px solid gray";
        //div.style.backgroundColor = "white";
        // 绑定事件,点击测距
        div.onclick = function(e){
            distanceMeasure();
        }
        // 添加DOM元素到地图中
        map.getContainer().appendChild(div);
        // 将DOM元素返回
        return div;
    }


    // 创建控件
    var myDistanceCtrl = new DistanceControl();
    // 添加到地图当中
    map.addControl(myDistanceCtrl);
}
// 添加提示
function addTipControl(){
    TipControl.prototype = new BMap.Control();

    TipControl.prototype.initialize = function(map){
        // 创建一个DOM元素
        var label = document.createElement("label");
        label.id = "map_tip";
        label.innerHTML = "还有<span id='map_tip_second' style='color: #F00;padding: 0px 5px'>--s</span>刷新";
        label.style.color = "#999";
        // 设置样式
        // 添加DOM元素到地图中
        map.getContainer().appendChild(label);
        // 将DOM元素返回
        return label;
    }
    var myTipCtrl = new TipControl();
    // 添加到地图当中
    map.addControl(myTipCtrl);
}

//显示右下角提示框
function showTipsMessage(msg){
    $.messager.show({
        title:'提示',
        msg:msg,
        timeout:1500,
        showType:'slide'
    });
}

//function myFun(result){
//    var cityName = result.name;
//    console.log("result",result);
//    map.setCenter(cityName);
//}
////定位，依据当前城市
//var myCity = new BMap.LocalCity();
//myCity.get(myFun);

// 清楚地图所有覆盖物
function clearAll() {
    for(var i = 0; i < overlays.length; i++){
        map.removeOverlay(overlays[i]);
    }
    overlays.length = 0
}

function distanceMeasure(){
    myDis = new BMapLib.DistanceTool(map);
    myDis.open();
}

//function addMarker(point, index){  // 创建图标对象
//    var myIcon = new BMap.Icon("markers.png", new BMap.Size(25, 25), {
//// 指定定位位置。
//// 当标注显示在地图上时，其所指向的地理位置距离图标左上
//// 角各偏移10像素和25像素。您可以看到在本例中该位置即是
//        // 图标中央下端的尖角位置。
//        offset: new BMap.Size(10, 25),
//        // 设置图片偏移。
//        // 当您需要从一幅较大的图片中截取某部分作为标注图标时，您
//        // 需要指定大图的偏移位置，此做法与css sprites技术类似。
//        imageOffset: new BMap.Size(0, 0 - index * 25)   // 设置图片偏移
//    });
//// 创建标注对象并添加到地图
//    var marker = new BMap.Marker(point);
//    marker.addEventListener("click", function(){
//        var opts = {
//            width : 250,     // 信息窗口宽度
//            height: 100,     // 信息窗口高度
//            title : "Hello"  // 信息窗口标题
//        }
//        var infoWindow = new BMap.InfoWindow("World", opts);  // 创建信息窗口对象
//        map.openInfoWindow(infoWindow, marker.point);
//        //alert("您点击了标注");
//    });
//    map.addOverlay(marker);
//}

