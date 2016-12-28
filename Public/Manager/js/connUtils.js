/**
 * Created by lgq on 16/8/1.
 */




function createSocket() {
    var socket = new WebSocket('ws://218.202.105.118:6002');
    socket.onopen = function(event) {
        console.log("socket is opened")
        // 关闭Socket....
        //socket.close()
    };
    // 监听消息
    socket.onmessage = function(event) {
        processReceive(event.data);
    };
    // 监听Socket的关闭
    socket.onclose = function(event) {
        console.log('Client notified socket has closed',event);
    };
    return socket;
}

var socket = createSocket();

setInterval(function(){
    if (socket.readyState != WebSocket.OPEN){
        socket = createSocket();
    }
},10000);


function send(msg){
    console.log('send Msg：',msg);
    socket.send(msg);
}



//车辆右键定制菜单
// 文本信息下发，弹出对话框，加载 baseConnectionUrl 对应内容，该页面的内容完成发生消息

function sendText(){
//    弹出文本编辑对话框
    $('#setting').window('refresh', baseConnectionUrl);
    $('#setting').window({
        title:"文本信息下发"
    });
    $('#setting').window('open');
}

function takePhoto(){
    //    弹出文本编辑对话框
    var vehicleID = currNode.id%1000000000;

    $('#setting').window('refresh', takePhotoUrl+"&vehicle_id="+vehicleID);
    $('#setting').window({
        title:"拍照"
    });
    $('#setting').window('open');
}

//{"operate":"模块唯一标识","data":{[{"车辆信息":"待处理信息"},{}]},"time":1470884256,"state":"0,1,....","clientMsgs":["由客户端上传msg1","由客户端上传msg2"]}
function processReceive(response){
    console.log("processReceive",response);
    var data = JSON.parse(response);


    if(data.operate == "vehicle_online"){
        //车辆上线
        RM_vehicle_online(data.data,data.time);
    }else if(data.operate == "vehicle_offline"){
        //车辆下线
        RM_vehicle_offline(data.data,data.time);
    }else if (data.operate == "location_changed"){
        //车辆位置变化
        RM_locationChanged(data.data,data.time);
    }else if (data.operate == "vehicle_alarm"){
        //车辆位置变化
        RM_vehicle_alarm(data.data,data.time);
    }else if (data.operate == "get_online_vehicle_list"){
        //获取在线车辆列表
        RM_get_online_vehicles(data.data);
    }else if (data.operate == "check_location"){
        var msg = data.client_msg[data.confirm_state];
        if(msg){
            //点名
            RM_check_location(msg);
        }
    }else if (data.operate == "text_message"){
        //文本信息下发消息确认
        var msg = data.client_msg[data.confirm_state];
        if(msg){
            //点名
            RM_text_msg(msg);
        }
    }else if(data.operate == "take_photo"){
        var msg = data.client_msg[data.confirm_state];
        if(msg){
            //点名
            RM_quickTakePhoto(msg);
        }
    }

}

function updateOperateLog(msg){
    var date = new Date();

    var log = {
        "timeStr": date.toLocaleString(),
        "content": msg
    }
    operateLogArr.unshift(log);
    console.log(log);
    refreshOperateLogGrid();
}

function updateOperateLogForDB(operator_id,msg,vehicle_id){
    $.ajax({
        url: updateOperateInfosUrl,
        type: 'POST',
        dataType: 'Json',
        data: {operator_id:operator_id,content:msg,vehicle_id:vehicle_id}
    });
}

//获取在线车辆列表
function SM_get_online_vehicles(){

    var groupIds = new Array();
    var carIds = new Array();
    var childrens = $("#tt").tree('getChildren');
    for (i = 0; i < childrens.length; i++) {
        var child = childrens[i];
        if (child.attributes.type != "car"){
            groupIds.push(child.id);
        }else {
            var carId = child.id % 1000000000;
            carIds.push(carId);
        }
    }
    var d =
    {
        "operate": "get_online_vehicle_list",
        "data": {
            "group_id": groupIds
        }
    };

    SM_get_latest_history_info(carIds);
    send(JSON.stringify(d));

}

function SM_get_latest_history_info(groupIds){
    $.ajax({
        url: getLatestInfosUrl,
        type: 'Post',
        dataType: 'Json',
        data: {vehicleIds:groupIds},
        success: function (result) {
            if(result){
                for(var vehicleId in result){
                    if(cars[vehicleId]){
                        cars[vehicleId].history = result[vehicleId];
                    }
                }
            }
        }
    });
}
//收到车辆在线列表信息
function RM_get_online_vehicles(data){
    console.log("收到这些数据",data);
    for (i = 0;i<data.length;i++ ){
        var vehicleId = data[i].vehicle_id;
        cars[""+vehicleId].isOnline = true;
        var groupId = data[i].group_id;

        updateCarTreeIcon(vehicleId,'icon-car_online');

        //var car_id = groupId * 1000000000 + vehicleId;
        //var childrens = $("#tt").tree('getChildren');
        //for (i = 0; i < childrens.length; i++) {
        //    var child = childrens[i];
        //    if (child.id == car_id) {
        //        $('#tt').tree('update', {
        //            target: child.target,
        //            iconCls: 'icon-car_online'
        //        });
        //        break;
        //    }
        //}
    }

}

//车辆上线
//"data":{"vehicle_id":2,"group_id":30}"time":1470281054
//RM: Receive Message
function RM_vehicle_online(data,time){
//    1.改变车辆上下线状态
//    2.改变marker样式
//    3.修改车辆信息
    console.log("vehicle_online");
    var car =  cars[""+data.vehicle_id];
    car.isOnline = true;

    if (car.isChecked){
        addMarkerToMap(data.vehicle_id);
    }else {
        cleanMarker(data.vehicle_id);
    }

    updateCarTreeIcon(data.vehicle_id,'icon-car_online');

    //var car_id = data.group_id * 1000000000 + data.vehicle_id;
    //var childrens = $("#tt").tree('getChildren');
    //for (i = 0; i < childrens.length; i++) {
    //    var child = childrens[i];
    //    if (child.id == car_id) {
    //        $('#tt').tree('update', {
    //            target: child.target,
    //            iconCls: 'icon-car_online'
    //        });
    //        break;
    //    }
    //}
    //    TODO：待确认是否要写入操作日志
    updateOperateLog("【"+cars[data.vehicle_id].carinfo.car_license+"】 上线");
}



//onmessage {"operate":"vehicle_offline","data":{"vehicle_id":2,"group_id":30},"time":1470281054}
// 车辆下线 RM: Receive Message
function RM_vehicle_offline(data,time){
    console.log("vehicle_offline");
    var car =  cars[""+data.vehicle_id];
    car.isOnline = true;
    if (car.isChecked){
        addMarkerToMap(data.vehicle_id);
    }else {
        cleanMarker(data.vehicle_id)
    }
    updateCarTreeIcon(data.vehicle_id,'icon-car_offline');

    //var car_id = data.group_id * 1000000000 + data.vehicle_id;
    //var childrens = $("#tt").tree('getChildren');
    //for (i = 0; i < childrens.length; i++) {
    //    var child = childrens[i];
    //    if (child.id == car_id) {
    //        $('#tt').tree('update', {
    //            target: child.target,
    //            iconCls: 'icon-car_offline'
    //        });
    //        break;
    //    }
    //}
    //    TODO：待确认是否要写入操作日志
    updateOperateLog("【"+cars[data.vehicle_id].carinfo.car_license+"】 下线");

}
// 点名，SM: send Message
//{"operate":"check_location","vehicle_id":[2],"data":{}}
function SM_check_location(){

    var returnMsg = ["【"+currNode.attributes.carinfo.car_license+"】返回点名数据",
        "【"+currNode.attributes.carinfo.car_license+"】车辆不在线"];
    //showTipsMessage("【"+currNode.attributes.carinfo.car_license+"】点名指令下发成功");
    var d =
    {
        "operate": "check_location",
        "vehicle_id": [currNode.attributes.carinfo.vehicle_id],
        "data": {},
        "client_msg": returnMsg
    };
    //以json格式发出
    send(JSON.stringify(d));
    //    TODO：待确认是否要写入操作日志
    var msg = "【"+currNode.attributes.carinfo.car_license+"】下发点名指令";
    updateOperateLogForDB(1,msg, currNode.attributes.carinfo.vehicle_id);
    updateOperateLog(msg);
    showTipsMessage(msg);




}
//收到点名反馈
function RM_check_location(msg){
    //showTipsMessage("【"+currNode.attributes.carinfo.car_license+"】返回点名数据");
    updateOperateLog(msg);
    //    TODO：待确认是否要写入操作日志

}
//收到点名反馈
function RM_text_message(msg){


    updateOperateLog(msg);
    //    TODO：待确认是否要写入操作日志

}

function SM_takePhoto(params){
    var returnMsg = ["【"+currNode.attributes.carinfo.car_license+"】拍照成功",
        "【"+currNode.attributes.carinfo.car_license+"】车辆不在线"];
    var msg = "【"+currNode.attributes.carinfo.car_license+"】拍照指令下发成功";
    var d =
    {
        "operate": "take_photo",
        "vehicle_id": [currNode.attributes.carinfo.vehicle_id],
        "data": params,
        "client_msg": returnMsg
    };
    //以json格式发出
    send(JSON.stringify(d));
    updateOperateLogForDB(2,msg, currNode.attributes.carinfo.vehicle_id);
    updateOperateLog(msg);
    showTipsMessage(msg);
}
// 发送快速拍照指令
function SM_quickTakePhoto(){

    //{"operate":"take_photo","vehicle_id":[2],"data":{"passage":1,"command":"1","interval":"1","save_flag":0,"resolution":1,"quality":1,"brightness":1,"contrast":1,"saturation":1,"chroma":1}}

    var param = {
        "passage":1,//通道id ： 快速拍照使用1，
        "command":1,//拍摄命令 ： 0，表示停止拍照，0xFFFF表示录像，其余表示拍照数量
        "interval":0,// 拍照间隔/录像时间 秒,0 表示按最小间隔拍照或一直录像
        "save_flag":0,//保存标志,1:保存; 0:实时上传
        "resolution":1,//分辨率 0x01:320*240; 0x02:640*480; 0x03:800*600; 0x04:1024*768; 0x05:176*144;[Qcif]; 0x06:352*288;[Cif]; 0x07:704*288;[HALF D1]; 0x08:704*576;[D1];
        "quality":1,//图像/视频质量:1-10,1 代表质量损失最小,10 表示压缩比最大
        "brightness":0,//亮度 0-255
        "contrast":0,//对比度 0-127
        "saturation":0,//饱和度 0-127
        "chroma":0 //色度 0-255
    };
    var returnMsg = ["【"+currNode.attributes.carinfo.car_license+"】快速拍照成功",
        "【"+currNode.attributes.carinfo.car_license+"】车辆不在线"];

    var msg = "【"+currNode.attributes.carinfo.car_license+"】快速拍照指令下发成功";
    var d =
    {
        "operate": "take_photo",
        "vehicle_id": [currNode.attributes.carinfo.vehicle_id],
        "data": param,
        "client_msg": returnMsg
    };
    //以json格式发出
    send(JSON.stringify(d));
    updateOperateLog(msg);
    showTipsMessage(msg);
    updateOperateLogForDB(3,msg, currNode.attributes.carinfo.vehicle_id);
}
function RM_quickTakePhoto(msg){
    updateOperateLog(msg);

}
//车辆位置发生变化
// {"alarm":0,"status":262147,"lat":40.79942,"lng":111.732641,"altitude":1087,"speed":0,"direction":304,"time":"2016-08-08 09:23:57","receive_time":"2016-08-08 09:23:59","vehicle_id":2}
function RM_locationChanged(data,time){

    var car_id = data.vehicle_id;
    if (cars[""+car_id]){
        cars[""+car_id].realinfo = data;

        cars[""+car_id].isOnline = true;
        updateCarTreeIcon(data.vehicle_id,'icon-car_online');
        if (isMarkerShow(car_id)){
            addMarkerToMap(car_id);
        }
        for(i=0;i<carGpsInfoArr.length;i++){
            var carInfo = carGpsInfoArr[i];
            if(carInfo.vehicle_id == car_id){
                console.log("修改前",carInfo);
                var status = data["status"];
                var value1 = (((1<<0) & status )>>0)+"";
                var value2 = (((1<<1) & status )>>1)+"";
                var state = "ACC"+rule["0"][value1]+";"+rule["1"][value2];
                var direction  = parseInt( data.direction / 45);;

                carInfo["state"] = state;
                carInfo["directionDesc"] = directionDesc[direction];
                carInfo.time = data.time;
                carInfo['speed'] = data.speed;

                carInfo['mileage'] = data.mileage;

                carInfo.lat = data.lat;

                carInfo.lng = data.lng;
                console.log("修改值",data);

                $('#dg_carGpsInfo').datagrid('updateRow',{
                    index:i,
                    row:{
                        'time':carInfo.time,
                        "state":carInfo.state,
                        "speed":carInfo.speed,
                        "directionDesc":carInfo.directionDesc,
                        "mileage":carInfo.mileage,
                        "lat":carInfo.lat,
                        "lng":carInfo.lng
                    }
                });
                $('#dg_carGpsInfo').datagrid('refreshRow',i);
                console.log("修改后",carInfo);
                break;
            }
        }
    }
}

function updateCarTreeIcon(car_id,iconName) {
    var childrens = $("#tt").tree('getChildren');
    for (i = 0; i < childrens.length; i++) {
        var child = childrens[i];
        if(child.id< 1000000000){
            continue;
        }else {
            var temp = child.id % 1000000000;
            if (temp == car_id) {

                $('#tt').tree('update', {
                    target: child.target,
                    iconCls: iconName
                });
                break;
            }
        }

    }

}


//{"operate":"vehicle_alarm","data":{"start_time":"2016-08-26 16:27:27","alarm_source":2,"alarm_id":0,"vehicle_id":0,"speed":0,"status":262147,"lat":40.799558,"lng":111.732488,"direction":258,"id":153},"time":1472200048}
function RM_vehicle_alarm(data,time){
    var car_id = data.vehicle_id;
    if (cars[car_id]){
        $.ajax({
            url: getAlarmInfosUrl,
            type: 'Post',
            dataType: 'Json',
            data: {id:data.id},
            success: function (result) {
                if(result){

                    console.log("得到的报警数据：",result);


                    var status = result["status"];
                    var value1 = (((1<<0) & status )>>0)+"";
                    var value2 = (((1<<1) & status )>>1)+"";
                    var state = "ACC"+rule["0"][value1]+";"+rule["1"][value2];
                    result['state'] = state;
                    //   TODO: 3.修改车标，
                    updateCarTreeIcon(result.vehicle_id,'icon-car_alarm');
                    // 修改地图图标。
                    updateCarWithAlarmStatus(result);

                    for (key in alarmInfoArr){
                        if (alarmInfoArr[key].id == result.id){
                            return;
                        }
                    }
                    alarmInfoArr.push(result);
                    refreshAlarmInfosGrid();
                    var msg = "【"+result.car_license+"】"+result.alarm_define;
                    showTipsMessage(msg);

                }
            }
        });
    }
}

//收到消息下发回复
function RM_text_msg(msg){
    updateOperateLog(msg);
}
//查询终端属性 暂时不做
function getDeviceDetail(){
//    修改操作日志表，，刷新操作日志控件。

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


