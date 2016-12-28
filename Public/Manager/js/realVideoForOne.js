/**
 * Created by lgq on 16/9/28.
 *
 * 实时视频 js
 */

var g_NeedVersion = 507152020;

var g_WebType = 0;	// 0:实时浏览	1:录像回放
//ocx句柄，
var objMcuOcx;

var objMcuOcx = 0;
var objMcuOcxRec = 0;
//实时视频是否初始化标识
var g_bInitMcuOcx = 0;
//视频回放是否初始化标识
var g_bInitMcuOcxRec = 0;

// 是否已登录
var g_bAlreadyLogin = false;

// 字符串常量定义
var g_testWndIndex = -1;
var g_testDevName = "";
var g_testDevDomainId = "";
var g_testDevId = "";
var g_testChan = 0;
var g_testManu = "kedacom";
var g_testUser = "admin";
var g_testPass = "admin123";
var g_testOnline = 1;
var g_realplayurl = "";

var g_bHigh = [1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1];
// 录像存储位置	1：平台	2：前端
var g_nRecPos = [1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1];

// 实时浏览，0 正在播放，1 停止播放
var g_bRealStop = [1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1];

// 实时浏览声音开关  0：静音 1：开启声音
var g_bWndSoundEnable = [1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1];

// 实时浏览对讲使用状态 0：停止，1：开启
var g_bWndVoiceUsable = 0;
// 对讲设备id
var g_bWndVoiceDeviceId = 0;
// 对讲设备的通道号
var g_bwndVoiceDeviceChanel = 0;
//
var g_bWndVoiceHandel = 0;


var g_devicename = ['', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''];
var g_devicepuid = ['', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''];
var g_devicedomainid = ['', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''];
var g_devicechan = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
var g_deviceManu = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];


// 码流交换句柄
var g_realplayhandle = [-1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1];
var g_recplayhandle = [-1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1];
// PTZ控制速度
var g_ptzspeed = 8;

// 录像回放
var g_queryhandle = [-1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1];
var g_queryrecyear = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
var g_queryrecmonth = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
var g_queryrecday = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];


var g_queryrecplayyear = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
var g_queryrecplaymonth = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
var g_queryrecplayday = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];

// 当前选中的窗口Index
var g_nCurWndIndex = 0;
var g_nSelectType = 1;	// 1:单画面	4：四画面
// 画面风格
var g_nScreenStyle = 4;	// 1：一画面	4：四画面
//传输方式 0:UDP 1:TCP
var g_transmode = 0;



var g_xmlTLoginInfo = "TLoginInfo";
var g_xmlstrUser = "strUser";
var g_xmlstrPWD = "strPWD";
var g_xmlstrIP = "strIP";
// TDEVCHN
var g_xmlTDEVCHN = "TDEVCHN";
var g_xmlstrDomainID = "strDomainID";
var g_xmlstrDeviceID = "strDeviceID";
var g_xmlstrChn = "strChn";
var g_xmlstrSrc = "strSrc";
// TStream_Param
var g_xmlTStream_Param = "TStream_Param";
var g_xmlstrServerIp = "strServerIp";
var g_xmlstrServerPort = "strServerPort";
var g_xmlstrManu = "strManu";
var g_xmlstrHighDefinition = "strHighDefinition";
var g_xmlstrPlayWndHandle = "strPlayWndHandle";
var g_xmlstrRecPlace = "strRecPlace";

// TPtzCmd
var g_xmlTPtzCmd = "TPtzCmd";
var g_xmlstrPtzCmd = "strPtzCmd";
var g_xmlstrPtzRange = "strPtzRange";
var g_xmlstrLevel = "strLevel";
var g_xmlstrHoldTime = "strHoldTime";


function GetRealHandleByWndIndex(nWndIndex) {
    return g_realplayhandle[nWndIndex];
}
function SetRealHandleByWndIndex(handle, nWndIndex) {
    g_realplayhandle[nWndIndex] = handle;
}
function GetDevNameByWndIndex(nWndIndex) {
    return g_devicename[nWndIndex];
}
function GetDomainIDByWndIndex(nWndIndex) {
    return g_devicedomainid[nWndIndex];
}
function GetPuidByWndIndex(nWndIndex) {
    return g_devicepuid[nWndIndex];
}
function GetChanByWndIndex(nWndIndex) {
    return g_devicechan[nWndIndex];
}
function GetManuByWndIndex(nWndIndex) {
    return g_deviceManu[nWndIndex];
}
function GetHighByWndIndex(nWndIndex) {
    return g_bHigh[nWndIndex];
}
function SetHighByWndIndex(high, nWndIndex) {
    g_bHigh[nWndIndex] = high;
}
function GetRecPosByWndIndex(nWndIndex) {
    return g_nRecPos[0];
}
function SetRecPosByWndIndex(recpos, nWndIndex) {
    g_nRecPos[0] = recpos;
}
function GetRealStopByWndIndex(nWndIndex) {
    return g_bRealStop[nWndIndex];
}
function SetRealStopByWndIndex(stop, nWndIndex) {
    g_bRealStop[nWndIndex] = stop;
}


function GetPlayYearByWndIndex(nWndIndex) {
    return g_queryrecplayyear[nWndIndex];
}
function GetPlayMonthByWndIndex(nWndIndex) {
    return g_queryrecplaymonth[nWndIndex];
}
function GetPlayDayByWndIndex(nWndIndex) {
    return g_queryrecplayday[nWndIndex];
}

function SetPlayInfoByWndIndex(name, domainid, puid, chan, Manu, nWndIndex) {
    g_devicename[nWndIndex] = name;
    g_devicepuid[nWndIndex] = puid;
    g_devicedomainid[nWndIndex] = domainid;
    g_devicechan[nWndIndex] = chan;
    g_deviceManu[nWndIndex] = Manu;
}

function CheckOcxReg()
{

    try
    {
        var ocxObj = document.getElementById("mcuocx");
        var bRet = false;
        if(ocxObj && typeof(ocxObj.GetVersion) != "undefined")
        {

            if(ocxObj.GetVersion() >= g_NeedVersion)
            {
                bRet = true;
                //document.getElementById("id_downloadocx").style.display="none";
                //document.getElementById("id_remuser").style.display="block";
                //document.getElementById("id_startwithadmin").style.display="none";
            }
        }
        if(bRet == false)
        {
            //document.getElementById("id_downloadocx").style.display="block";
            //document.getElementById("id_remuser").style.display="none";
            //document.getElementById("id_startwithadmin").style.display="none";
            $.messager.confirm('下载提示', '使用本功能需先安装ocx控件！点击确定进行下载。\n 如果已下载，请允许插件运行！', function(r){
                if (r){
                    // exit action;
                    window.open('Public/Manager/keda/bin/BS-Setup.exe');

                }
            });
            //showTipsMessage('');
            //alert("请先下载并更新插件");
        }
        //else if(ocxObj && typeof(ocxObj.IsHasPri) != "undefined")
        //{
        //    if(ocxObj.IsHasPri == 0) // 没有管理员权限，则提示用户以管理员身份运行浏览器
        //    {
        //        bRet = false;
        //        //document.getElementById("id_downloadocx").style.display="none";
        //        //document.getElementById("id_remuser").style.display="none";
        //        //document.getElementById("id_startwithadmin").style.display="block";
        //	alert("请以管理员身份运行浏览器");//chrome?zjc?
        //    }
        //    else
        //    {
        //        bRet = true;
        //    }
        //}
        return bRet;
    } catch (e)
    {
    }
}

function CheckBrowserVersion()
{
    var browserVer = getBrowserVersion();
    var nBrowserVersion = parseInt(browserVer.version,10);
    if( "chrome" == browserVer.browser )
    {
        //alert( "请使用IE内核的浏览器，且版本不低于IE9" );
        showDialogMessage("视频功能需要使用IE内核的浏览器，且版本不低于IE9" );
        return false;
    }
    if("IE"!= browserVer.browser || ("IE"== browserVer.browser && nBrowserVersion < 9))
    {
        showDialogMessage("视频功能需要使用IE内核的浏览器，且版本不低于IE9" );
        return false;
    }
    if(("IE"== browserVer.browser) &&
        ((nBrowserVersion == 8) || (nBrowserVersion == 9)) &&
        (window.navigator.platform == "Win64"))
    {
        showDialogMessage("暂不支持Win64位系统" );
        return false;
    }
    return true;
}
function getBrowserVersion()
{
    var userAgent = navigator.userAgent,
        rMsie = /(msie\s|trident.*rv:)([\w.]+)/,
        rFirefox = /(firefox)\/([\w.]+)/,
        rOpera = /(opera).+version\/([\w.]+)/,
        rChrome = /(chrome)\/([\w.]+)/,
        rSafari = /version\/([\w.]+).*(safari)/;
    var ua = userAgent.toLowerCase();
    function uaMatch(ua) {
        var match = rMsie.exec(ua);
        if (match != null) {
            return { browser : "IE", version : match[2] || "0" };
        }
        var match = rFirefox.exec(ua);
        if (match != null) {
            return { browser : match[1] || "", version : match[2] || "0" };
        }
        var match = rOpera.exec(ua);
        if (match != null) {
            return { browser : match[1] || "", version : match[2] || "0" };
        }
        var match = rChrome.exec(ua);
        if (match != null) {
            return { browser : match[1] || "", version : match[2] || "0" };
        }
        var match = rSafari.exec(ua);
        if (match != null) {
            return { browser : match[2] || "", version : match[1] || "0" };
        }
        if (match != null) {
            return { browser : "", version : "0" };
        }
    }
    return uaMatch(userAgent.toLowerCase());
}

//设置清晰与流畅的选中状态
function SetSelectHigh(high) {
    if (high == 0) {
        //document.getElementById("id_btnviewlow").style.background = "#222429";
        //document.getElementById("id_btnviewhigh").style.background = "#35373c";
        document.getElementById("id_btnviewlow").disabled = "disabled";
        document.getElementById("id_btnviewhigh").disabled = "";
    }
    else {
        //document.getElementById("id_btnviewlow").style.background = "#35373c";
        //document.getElementById("id_btnviewhigh").style.background = "#292b30";
        document.getElementById("id_btnviewlow").disabled = "";
        document.getElementById("id_btnviewhigh").disabled = "disabled";
    }
}
//设置当前设备信息
function SetCurDevInfo(nCurWndIndex) {
    var year = GetPlayYearByWndIndex(nCurWndIndex);
    var month = GetPlayMonthByWndIndex(nCurWndIndex);
    var day = GetPlayDayByWndIndex(nCurWndIndex);
    var chan = GetChanByWndIndex(nCurWndIndex);
    var name = GetDevNameByWndIndex(nCurWndIndex);
    if (nCurWndIndex != g_nCurWndIndex) {
        return;
    }
    if (name != "") {
        if (g_WebType == 0) {
            document.getElementById("id_curdevinfo").innerHTML = "设备：" + name;
        }
        else {
            if (year == 0 || month == 0 || day == 0) {
                document.getElementById("id_curdevinfo").innerHTML = "设备：" + name;
            }
            else {
                document.getElementById("id_curdevinfo").innerHTML = "设备：" + name
                    + "&nbsp;&nbsp;&nbsp;日期：" + year + "-" + month + "-" + day;
            }
        }
    }
    else {
        document.getElementById("id_curdevinfo").innerHTML = "";
    }
}


//初始化ocx控件--实时视频
function InitMcuOcx()
{
    objMcuOcx = document.getElementById("mcuocx");
    if(objMcuOcx && g_bInitMcuOcx == 0)
    {
        g_bInitMcuOcx = 1;
        //OCX 接口
        objMcuOcx.Init();
    }
}
//释放ocx控件--实时视频

function UnInitMcuOcx()
{
    objMcuOcx = document.getElementById("mcuocx");
    if(objMcuOcx && g_bInitMcuOcx == 1)
    {
        for(var nWndIndex = 0;nWndIndex < 16;nWndIndex++)
        {
            var handle = GetRealHandleByWndIndex(nWndIndex);
            StopRealPlay(handle,nWndIndex);
            SetRealHandleByWndIndex(-1,nWndIndex);
        }
        g_bInitMcuOcx = 0;
        //OCX 接口
        objMcuOcx.LogOut();
        //OCX 接口
        objMcuOcx.UnInit();
    }
}

//停止播放
function StopRealPlay(handle, nWndIndex) {
    InitMcuOcx();
    if (handle != -1) {
        objMcuOcx.StopRealPlay(handle, nWndIndex);
    }
    SetRealHandleByWndIndex(-1, nWndIndex);
    SetRealStopByWndIndex(1, nWndIndex);
    SetPlayInfoByWndIndex("", "", "", 0, "kedacom", nWndIndex);

    // 修改播放图标
    document.getElementById("id_ctrlplay").style.background = "url('Public/Manager/keda/images/realplay_one/play_normal.png')";

    //SetCurDevInfo(nWndIndex);
    //NotifyOCXWndUsing(nWndIndex, 0);
}


//进行播放
function ClickComfirmDeviceBtn() {
    //zjctest();
    //return;
//	if(g_WebType == 1)
//	{
//		OnCloseDeviceList();
//	}
    if (g_testDevDomainId == "" || g_testDevId == "") {
        return;
    }
    if (g_WebType == 0)	// 浏览
    {
        if (g_testOnline == 0) {
            showTipsMessage("设备不在线，无法操作");
            return;
        }
        SetCurDevInfo(g_nCurWndIndex);
        //设置到对应数组中，
        SetPlayInfoByWndIndex(g_testDevName, g_testDevDomainId, g_testDevId, g_testChan, g_testManu, g_nCurWndIndex);
        //不需要了
        //NotifyOCXWndUsing(g_nCurWndIndex, 1);

        //域id
        var tmpdomainid = GetDomainIDByWndIndex(g_nCurWndIndex);
        //设备id
        var tmppuid = GetPuidByWndIndex(g_nCurWndIndex);
        //通道号
        var tmpchan = GetChanByWndIndex(g_nCurWndIndex);
        //厂商
        var tmpmanu = GetManuByWndIndex(g_nCurWndIndex);
        //清晰度：0；清晰，1：流畅
        var tmphigh = GetHighByWndIndex(g_nCurWndIndex);

        StartRealPlay(tmpdomainid, tmppuid, tmpchan, tmpchan, tmpmanu, tmphigh, g_nCurWndIndex);
    }
}

//点击高清
function ClickViewHighBtn() {
    if (GetHighByWndIndex(g_nCurWndIndex) == 1) {
        return;
    }
    SetHighByWndIndex(1, g_nCurWndIndex);
    //document.getElementById("id_btnviewlow").style.background = "#35373c";//292C2E 35373C
    document.getElementById("id_btnviewlow").style.border = 'solid #008DC4 0px';

    //document.getElementById("id_btnviewhigh").style.background = "#292C2E";//292C2E 35373C
    document.getElementById("id_btnviewhigh").style.border = 'solid #008DC4 1px';

    document.getElementById("id_btnviewhigh").disabled = "disabled";
    document.getElementById("id_btnviewlow").disabled = "";
    OnChangeSelectHigh(1);
}
//点击流程
function ClickViewLowBtn() {
    if (GetHighByWndIndex(g_nCurWndIndex) == 0) {
        return;
    }
    SetHighByWndIndex(0, g_nCurWndIndex);
    //document.getElementById("id_btnviewlow").style.background = "#292C2E";
    document.getElementById("id_btnviewlow").style.border = 'solid #008DC4 1px';

    //document.getElementById("id_btnviewhigh").style.background = "#35373c";
    document.getElementById("id_btnviewhigh").style.border = 'solid #008DC4 0px';


    document.getElementById("id_btnviewlow").disabled = "disabled";
    document.getElementById("id_btnviewhigh").disabled = "";
    OnChangeSelectHigh(0);
}

// 解析网页url，判断是否需要立即播放
// puid,domainid,chan,src,Manu,High
function StartRealPlay(domainid, puid, chan, src, Manu, High, WndIndex) {
    showTipsMessage('正在连接设备，请稍后。。。');
    console.log('startRealPlay',WndIndex);
    SetCurDevInfo(g_nCurWndIndex);

    SetSelectHigh(High);
    //showTipsMessage('窗口'+(1+WndIndex)+'启动实时视频！');

    InitMcuOcx();
    if (domainid == "" || puid == "") {
        showTipsMessage("未选中设备");
        return;
    }
    //showTipsMessage('正在连接设备，请稍后。。。');
    var handle = GetRealHandleByWndIndex(g_nCurWndIndex);
    if (handle != -1) {
        var tmpname = GetDevNameByWndIndex(g_nCurWndIndex)
        var tmpdomainid = GetDomainIDByWndIndex(g_nCurWndIndex);
        var tmppuid = GetPuidByWndIndex(g_nCurWndIndex);
        var tmpchan = GetChanByWndIndex(g_nCurWndIndex);
        var tmpmanu = GetManuByWndIndex(g_nCurWndIndex);
        var tmphigh = GetHighByWndIndex(g_nCurWndIndex);
        StopRealPlay(handle, g_nCurWndIndex);
        SetPlayInfoByWndIndex(tmpname, tmpdomainid, tmppuid, tmpchan, tmpmanu, g_nCurWndIndex);
    }
    var mcuip = location.host;//document.URL;	// 获取当期url
    mcuip= "133.1.0.160";
    var recpos = GetRecPosByWndIndex(WndIndex);
    var logininfo =
        "<" + g_xmlTLoginInfo + ">" +
        "<" + g_xmlstrUser + ">" + g_testUser + "</" + g_xmlstrUser + ">" +
        "<" + g_xmlstrPWD + ">" + g_testPass + "</" + g_xmlstrPWD + ">" +
        "<" + g_xmlstrIP + ">" + mcuip + "</" + g_xmlstrIP + ">" +
        "</" + g_xmlTLoginInfo + ">";
    var chaninfo =
        "<" + g_xmlTDEVCHN + ">" +
        "<" + g_xmlstrDomainID + ">" + domainid + "</" + g_xmlstrDomainID + ">" +
        "<" + g_xmlstrDeviceID + ">" + puid + "</" + g_xmlstrDeviceID + ">" +
        "<" + g_xmlstrChn + ">" + chan + "</" + g_xmlstrChn + ">" +
        "<" + g_xmlstrSrc + ">" + src + "</" + g_xmlstrSrc + ">" +
        "</" + g_xmlTDEVCHN + ">";
    var streaminfo =
        "<" + g_xmlTStream_Param + ">" +
        "<" + g_xmlstrServerIp + ">" + "" + "</" + g_xmlstrServerIp + ">" +
        "<" + g_xmlstrServerPort + ">" + "" + "</" + g_xmlstrServerPort + ">" +
        "<" + g_xmlstrManu + ">" + Manu + "</" + g_xmlstrManu + ">" +
        "<" + g_xmlstrHighDefinition + ">" + High + "</" + g_xmlstrHighDefinition + ">" +
        "<" + g_xmlstrRecPlace + ">" + recpos + "</" + g_xmlstrRecPlace + ">" +
        "</" + g_xmlTStream_Param + ">";
    //showTipsMessage("信息:" + logininfo);
    //NotifyOCXWndUsing(WndIndex, 1);
    //        设置传输模式 0：TCP 1：UDP
    if (g_transmode == 0) {
        objMcuOcx.SetStreamPattern(1);
    } else {
        objMcuOcx.SetStreamPattern(0);
    }

    handle = objMcuOcx.StartRealPlay(logininfo, chaninfo, streaminfo, WndIndex);
    console.log("StartRealPlay handle",handle);
    if (handle == -1) {
        var nErrorCode = objMcuOcx.GetLastErrCode();
        if (nErrorCode == 40001)  //设备只有4K分辨率的情况就认为浏览失败
        {
            showTipsMessage("浏览失败(" + Manu + ") 原因：不支持浏览仅有4k分辨率的设备！");
        } else if(nErrorCode == 11500){
            showTipsMessage("浏览失败(" + Manu + ") 原因：设备不在线！");

        }
        else {
            showTipsMessage("浏览失败(" + Manu + ") 结果：" + handle + "\r\n错误码：" + nErrorCode);
        }
    }
    else {
        SetWndSoundEnable(g_nCurWndIndex);
        SetRealHandleByWndIndex(handle, g_nCurWndIndex);
        var arrayUrl = new Array();
        arrayUrl = document.URL.split("?");
        var b = new Base64();
        var str = b.encode(domainid + "," + puid + "," + chan + "," + src + "," + Manu + "," + High);
        g_realplayurl = "浏览成功 地址：" + arrayUrl[0] + "?" + str;
        showTipsMessage('窗口'+(1+WndIndex)+'浏览成功！');
    //    修改播放图标
    }
    SetRealStopByWndIndex(0, g_nCurWndIndex);
    document.getElementById("id_ctrlplay").style.background = "url('Public/Manager/keda/images/realplay_one/stop_normal.png')";
}

// 音量
function SetWndSoundEnable(nWndIndex) {
    var nCurWndSound = g_bWndSoundEnable[g_nCurWndIndex];
    for (var i = 0; i < g_bWndSoundEnable.length; ++i) {
        if (i == nWndIndex) {
            g_bWndSoundEnable[i] = 1;
        }
        else {
            g_bWndSoundEnable[i] = 0;
        }
    }
    //如果当前选择窗口的声音开关发生了变化，这调整声音开关的样式
    if (nCurWndSound != g_bWndSoundEnable[g_nCurWndIndex]) {
        if (g_bWndSoundEnable[g_nCurWndIndex] == 1) {
            $("#id_ctrlvolume").removeClass("silent");
            $("#id_ctrlvolume").attr("title", "对选中窗口设置静音");
        }
        else {
            $("#id_ctrlvolume").addClass("silent");
            $("#id_ctrlvolume").attr("title", "对选中窗口取消静音");
        }
        OutCtrlVolume();
    }
}


function OnChangeSelectHigh(value) {
    var mcuip = location.host;//document.URL;	// 获取当期url
    mcuip = "133.1.0.160";
    var tmpdomainid = GetDomainIDByWndIndex(g_nCurWndIndex);
    var tmppuid = GetPuidByWndIndex(g_nCurWndIndex);
    var tmpchan = GetChanByWndIndex(g_nCurWndIndex);
    var tmpmanu = GetManuByWndIndex(g_nCurWndIndex);
    var tmphigh = GetHighByWndIndex(g_nCurWndIndex);
    if (tmppuid == "")return;
    var recpos = GetRecPosByWndIndex(g_nCurWndIndex);
    var logininfo =
        "<" + g_xmlTLoginInfo + ">" +
        "<" + g_xmlstrUser + ">" + g_testUser + "</" + g_xmlstrUser + ">" +
        "<" + g_xmlstrPWD + ">" + g_testPass + "</" + g_xmlstrPWD + ">" +
        "<" + g_xmlstrIP + ">" + mcuip + "</" + g_xmlstrIP + ">" +
        "</" + g_xmlTLoginInfo + ">";
    var chaninfo =
        "<" + g_xmlTDEVCHN + ">" +
        "<" + g_xmlstrDomainID + ">" + tmpdomainid + "</" + g_xmlstrDomainID + ">" +
        "<" + g_xmlstrDeviceID + ">" + tmppuid + "</" + g_xmlstrDeviceID + ">" +
        "<" + g_xmlstrChn + ">" + tmpchan + "</" + g_xmlstrChn + ">" +
        "<" + g_xmlstrSrc + ">" + tmpchan + "</" + g_xmlstrSrc + ">" +
        "</" + g_xmlTDEVCHN + ">";
    var streaminfo =
        "<" + g_xmlTStream_Param + ">" +
        "<" + g_xmlstrServerIp + ">" + "" + "</" + g_xmlstrServerIp + ">" +
        "<" + g_xmlstrServerPort + ">" + "" + "</" + g_xmlstrServerPort + ">" +
        "<" + g_xmlstrManu + ">" + tmpmanu + "</" + g_xmlstrManu + ">" +
        "<" + g_xmlstrHighDefinition + ">" + tmphigh + "</" + g_xmlstrHighDefinition + ">" +
        "<" + g_xmlstrRecPlace + ">" + recpos + "</" + g_xmlstrRecPlace + ">" +
        "</" + g_xmlTStream_Param + ">";
    var handle = -1;
    if (g_WebType == 0) {
        InitMcuOcx();
        var tmphandle = GetRealHandleByWndIndex(g_nCurWndIndex);
        if (tmphandle != -1) {
            if (g_transmode == 0) {
                objMcuOcx.SetStreamPattern(1);
            } else {
                objMcuOcx.SetStreamPattern(0);
            }
            showTipsMessage('正在切换清晰度，请稍后。。。');
            handle = objMcuOcx.SwitchPicQuality(tmphandle, chaninfo, streaminfo, g_nCurWndIndex);
        }
        else {
            if (g_transmode == 0) {
                objMcuOcx.SetStreamPattern(1);
            } else {
                objMcuOcx.SetStreamPattern(0);
            }
            showTipsMessage('正在切换清晰度，请稍后。。。');
            handle = objMcuOcx.StartRealPlay(logininfo, chaninfo, streaminfo, g_nCurWndIndex);
        }
        if (handle == -1) {
            var nErrorCode = objMcuOcx.GetLastErrCode();
            if (nErrorCode == 40001)  //设备只有4K分辨率的情况就认为浏览失败
            {
                showTipsMessage("浏览失败(" + Manu + ") 原因：不支持浏览仅有4k分辨率的设备！");
            } else if(nErrorCode == 11500){
                showTipsMessage("浏览失败(" + Manu + ") 原因：设备不在线！");

            }
            else {
                showTipsMessage("浏览失败(" + Manu + ") 结果：" + handle + "\r\n错误码：" + nErrorCode);
            }
            return;
        }else{
            showTipsMessage('窗口'+(1+WndIndex)+'切换切换清晰度成功！');
        }
        SetRealHandleByWndIndex(handle, g_nCurWndIndex);
    }
    else {
        InitMcuOcxRec();
        var tmphandle = GetRecHandleByWndIndex(g_nCurWndIndex);
        if (tmphandle == -1)return;
        var lTaskID = GetQueryHandleByWndIndex(g_nCurWndIndex);
        handle = objMcuOcxRec.SwitchPicQuality(tmphandle, logininfo, lTaskID, streaminfo, g_nCurWndIndex);
        if (handle == -1) {
            showTipsMessage("切换回放失败.结果：" + handle + "\r\n错误码：" + objMcuOcx.GetLastErrCode());
            return;
        }
        SetRecHandleByWndIndex(handle, g_nCurWndIndex);
    }
}


function RecMainLogOut()
{
    if(g_bAlreadyLogin == true)
    {
        var mcuip = location.host;
        objMcuOcxRec.LogOut();
        g_bAlreadyLogin = false;
    }
}

//切换单窗口模式
function ClickSingleScreen(){
    //设置按钮图标
    document.getElementById("id_singlesrceen").style.background = "url('Public/Manager/keda/images/single_screen_dis.bmp')";
    document.getElementById("id_foursrceen").style.background = "url('Public/Manager/keda/images/four_screen_normal.bmp')";
    document.getElementById("id_ninesrceen").style.background = "url('Public/Manager/keda/images/nine_screen_normal.bmp')";
    document.getElementById("id_sixteensrceen").style.background = "url('Public/Manager/keda/images/sixteen_screen_normal.bmp')";
    //
    if(g_WebType == 0)
    {
        InitMcuOcx();
        objMcuOcx.SetWndStyle(1);
    }
    else
    {
        InitMcuOcxRec();
        objMcuOcxRec.SetWndStyle(1);
    }
    if(g_nScreenStyle != 1){
        for(var i = 1; i<16; i++)
        {
            if(g_WebType == 0){
                var handle = GetRealHandleByWndIndex(i);
                StopRealPlay(handle,i);
            }else{
                var handleRec = GetRecHandleByWndIndex(i);
                StopRecordPlay(handleRec,i);
            }
        }
    }
    g_nScreenStyle = 1;
}

// 切换四画面
function ClickFourScreen() {
    document.getElementById("id_foursrceen").style.background = "url('Public/Manager/keda/images/four_screen_dis.bmp')";
    document.getElementById("id_singlesrceen").style.background = "url('Public/Manager/keda/images/single_screen_normal.bmp')";
    document.getElementById("id_ninesrceen").style.background = "url('Public/Manager/keda/images/nine_screen_normal.bmp')";
    document.getElementById("id_sixteensrceen").style.background = "url('Public/Manager/keda/images/sixteen_screen_normal.bmp')";
    if (g_WebType == 0) {
        InitMcuOcx();
        objMcuOcx.SetWndStyle(4);
    }
    else {
        InitMcuOcxRec();
        objMcuOcxRec.SetWndStyle(4);
    }
    if (g_nScreenStyle > 4) {
        for (var i = 4; i < 16; i++) {
            if (g_WebType == 0) {
                var handle = GetRealHandleByWndIndex(i);
                StopRealPlay(handle, i);
            } else {
                var handleRec = GetRecHandleByWndIndex(i);
                StopRecordPlay(handleRec, i);
            }
        }
    }
    g_nScreenStyle = 4;
}

//nine screen
function ClickNineScreen() {
    document.getElementById("id_foursrceen").style.background = "url('Public/Manager/keda/images/four_screen_normal.bmp')";
    document.getElementById("id_singlesrceen").style.background = "url('Public/Manager/keda/images/single_screen_normal.bmp')";
    document.getElementById("id_ninesrceen").style.background = "url('Public/Manager/keda/images/nine_screen_dis.bmp')";
    document.getElementById("id_sixteensrceen").style.background = "url('Public/Manager/keda/images/sixteen_screen_normal.bmp')";
    if (g_WebType == 0) {
        InitMcuOcx();
        objMcuOcx.SetWndStyle(9);
    }
    else {
        InitMcuOcxRec();
        objMcuOcxRec.SetWndStyle(9);
    }
    if (g_nScreenStyle > 9) {
        for (var i = 9; i < 16; i++) {
            if (g_WebType == 0) {
                var handle = GetRealHandleByWndIndex(i);
                StopRealPlay(handle, i);
            } else {
                var handleRec = GetRecHandleByWndIndex(i);
                StopRecordPlay(handleRec, i);
            }
        }
    }
    g_nScreenStyle = 9;
}


//sixteen screen
function ClickSixteenScreen() {
    document.getElementById("id_foursrceen").style.background = "url('Public/Manager/keda/images/four_screen_normal.bmp')";
    document.getElementById("id_singlesrceen").style.background = "url('Public/Manager/keda/images/single_screen_normal.bmp')";
    document.getElementById("id_ninesrceen").style.background = "url('Public/Manager/keda/images/nine_screen_normal.bmp')";
    document.getElementById("id_sixteensrceen").style.background = "url('Public/Manager/keda/images/sixteen_screen_dis.bmp')";
    if (g_WebType == 0) {
        InitMcuOcx();
        objMcuOcx.SetWndStyle(16);
    }
    else {
        InitMcuOcxRec();
        objMcuOcxRec.SetWndStyle(16);
    }
    g_nScreenStyle = 16;
}

function InitScreenStyle() {
    if (g_nScreenStyle == 1) {
        document.getElementById("id_singlesrceen").style.background = "url('Public/Manager/keda/images/single_screen_dis.bmp')";
        document.getElementById("id_foursrceen").style.background = "url('Public/Manager/keda/images/four_screen_normal.bmp')";
    }
    else {
        document.getElementById("id_singlesrceen").style.background = "url('Public/Manager/keda/images/single_screen_normal.bmp')";
        document.getElementById("id_foursrceen").style.background = "url('Public/Manager/keda/images/four_screen_dis.bmp')";
    }
}

// 全屏
function ClickFullScreen() {
    InitMcuOcx();
    objMcuOcx.PleaseZoom();
}
function OverFullScreen() {
    document.getElementById("id_fullscreen").style.background = "url('Public/Manager/keda/images/full_screen_over.bmp')";
}
function OutFullScreen() {
    document.getElementById("id_fullscreen").style.background = "url('Public/Manager/keda/images/realplay_one/full_screen_normal.png')";
}
function DownFullScreen() {
    document.getElementById("id_fullscreen").style.background = "url('Public/Manager/keda/images/full_screen_press.bmp')";
}
function UpFullScreen() {
    document.getElementById("id_fullscreen").style.background = "url('Public/Manager/keda/images/full_screen_over.bmp')";
}



function ClickCtrlVolume() {
    InitMcuOcx();
    if ($("#id_ctrlvolume").hasClass("silent")) {
        SetWndSoundEnable(g_nCurWndIndex);
        $("#id_ctrlvolume").removeClass("silent");
        $("#id_ctrlvolume").attr("title", "对选中窗口设置静音");
        document.getElementById("id_ctrlvolume").style.background = "url('Public/Manager/keda/images/realplay_one/volume_normal.png')";
        if (typeof(objMcuOcx.SetWndSoundEnable) != "undefined") {
            objMcuOcx.SetWndSoundEnable(g_nCurWndIndex, 1);
        }
    }
    else {
        $("#id_ctrlvolume").addClass("silent");
        $("#id_ctrlvolume").attr("title", "对选中窗口取消静音");
        document.getElementById("id_ctrlvolume").style.background = "url('Public/Manager/keda/images/realplay_one/volsilent-nor.png')";
        g_bWndSoundEnable[g_nCurWndIndex] = 0;
        if (typeof(objMcuOcx.SetWndSoundEnable) != "undefined") {
            objMcuOcx.SetWndSoundEnable(g_nCurWndIndex, 0);
        }
    }
}
function OverCtrlVolume() {
    if ($("#id_ctrlvolume").hasClass("silent")) {
        document.getElementById("id_ctrlvolume").style.background = "url('Public/Manager/keda/images/realplay/volsilent-hover.png')";
    }
    else {
        document.getElementById("id_ctrlvolume").style.background = "url('Public/Manager/keda/images/realplay/volume_over.png')";
    }
}
function OutCtrlVolume() {
    if ($("#id_ctrlvolume").hasClass("silent")) {
        document.getElementById("id_ctrlvolume").style.background = "url('Public/Manager/keda/images/realplay/volsilent-nor.png')";
    }
    else {
        document.getElementById("id_ctrlvolume").style.background = "url('Public/Manager/keda/images/realplay/volume_normal.png')";
    }
}
function DownCtrlVolume() {
    if ($("#id_ctrlvolume").hasClass("silent")) {
        document.getElementById("id_ctrlvolume").style.background = "url('Public/Manager/keda/images/realplay/volsilent-press.png')";
    }
    else {
        document.getElementById("id_ctrlvolume").style.background = "url('Public/Manager/keda/images/realplay/volume_press.png')";
    }
}
function UpCtrlVolume() {
    if ($("#id_ctrlvolume").hasClass("silent")) {
        document.getElementById("id_ctrlvolume").style.background = "url('Public/Manager/keda/images/realplay/volsilent-hover.png')";
    }
    else {
        document.getElementById("id_ctrlvolume").style.background = "url('Public/Manager/keda/images/realplay/volume_over.png')";
    }
}

// 播放
function ClickCtrlPlay() {

    var stop = GetRealStopByWndIndex(g_nCurWndIndex);
    console.log("stop",stop);

    if (stop == 0) {
        var handle = GetRealHandleByWndIndex(g_nCurWndIndex);
        console.log("stop handle",handle);

        StopRealPlay(handle, g_nCurWndIndex);
    }else{

        ClickComfirmDeviceBtn();
    }
}
function OverCtrlPlay() {
    document.getElementById("id_ctrlplay").style.background = "url('Public/Manager/keda/images/realplay/stop_over.png')";
}
function OutCtrlPlay() {
    document.getElementById("id_ctrlplay").style.background = "url('Public/Manager/keda/images/realplay/stop_normal.png')";
}
function DownCtrlPlay() {
    document.getElementById("id_ctrlplay").style.background = "url('Public/Manager/keda/images/realplay/stop_press.png')";
}
function UpCtrlPlay() {
    document.getElementById("id_ctrlplay").style.background = "url('Public/Manager/keda/images/realplay/stop_over.png')";
}


// TODO: VOICE 呼叫 只支持一个对讲
function ClickCtrlAudioCall()
{
    //TODO: 这里调取呼叫对讲代码。
    if(g_bWndVoiceUsable == 1){
        //正在使用，
        //执行暂停
        InitMcuOcx();
        var h = objMcuOcx.StopVoiceCall(g_bWndVoiceHandel);
        g_bWndVoiceUsable = 0;
        g_bWndVoiceDeviceId = 0;
        g_bwndVoiceDeviceChanel = 0;
        g_bWndVoiceHandel = -1;
        document.getElementById("id_audiocall").style.background = "url('Public/Manager/keda/images/realplay_one/audiocall_normal.png')";
        showTipsMessage('关闭语音对讲！');
    }else{
        InitMcuOcx();
        var handle = GetRealHandleByWndIndex(g_nCurWndIndex);
        if (handle != -1) {//当前视频播放中
            var chaninfo =
                "<" + g_xmlTDEVCHN + ">" +
                "<" + g_xmlstrDomainID + ">" + g_testDevDomainId + "</" + g_xmlstrDomainID + ">" +
                "<" + g_xmlstrDeviceID + ">" + g_testDevId + "</" + g_xmlstrDeviceID + ">" +
                "<" + g_xmlstrChn + ">" + g_testChan + "</" + g_xmlstrChn + ">" +
                "<" + g_xmlstrSrc + ">" + g_testChan + "</" + g_xmlstrSrc + ">" +
                "</" + g_xmlTDEVCHN + ">";
            showTipsMessage('正在开启语音对讲，请稍后。。。');
            var h = objMcuOcx.StartVoiceCall(chaninfo,3);

            if(h!=65535){
                g_bWndVoiceHandel = h;
                g_bWndVoiceUsable = 1;
                g_bWndVoiceDeviceId = g_testDevId;
                g_bwndVoiceDeviceChanel = g_testChan;
                showTipsMessage('开启语音对讲成功！');

                //    修改按钮样式
                document.getElementById("id_audiocall").style.background = "url('Public/Manager/keda/images/realplay_one/audiocall_disable.png')";
            }else{
                showTipsMessage('开启语音对讲失败！');
                InitMcuOcx();
            }
        }else{
            showTipsMessage('语音对讲需先启动实时视频！');
        }

    }


}

function OverCtrlAudioCall()
{
    document.getElementById("id_audiocall").style.background = "url('Public/Manager/keda/images/realplay/audiocall_over.png')";
}
function OutCtrlAudioCall()
{
    document.getElementById("id_audiocall").style.background = "url('Public/Manager/keda/images/realplay/audiocall_normal.png')";
}
function DownCtrlAudioCall()
{
    document.getElementById("id_audiocall").style.background = "url('Public/Manager/keda/images/realplay/audiocall_press.png')";
}
function UpCtrlAudioCall()
{
    document.getElementById("id_audiocall").style.background = "url('Public/Manager/keda/images/realplay/audiocall_over.png')";
}


//设置重置按钮可见
function SetRebootBtnVisible()
{
    var strVariable = g_testUser.toLowerCase();
//	showTipsMessage(strVariable + " " + g_testUser);
    if(strVariable.indexOf("admin") < 0)
    {
        document.getElementById("id_btnreboot").style.display="none";
        return ;
    }
}

//回调事件
function DealRealPlayEvent(lEvent, lWndIndex, lReserve1, lReserve2) {
    g_nCurWndIndex = lWndIndex;
    console.log("event:",lEvent);

    //showTipsMessage(lEvent + " " + lWndIndex);
    switch (lEvent) {

        case 0:	// 选择设备 AddDev
            //TODO：待研究
            //ClickGetDeviceList(0);
            //GetRealHandleByWndIndex(0);
            break;
        case 1:	// 窗口切换
            OutCtrlPlay();	// 更新控制按钮
            SetCurDevInfo(g_nCurWndIndex);
            if (g_bWndSoundEnable[g_nCurWndIndex] == 1) {
                $("#id_ctrlvolume").removeClass("silent");
                $("#id_ctrlvolume").attr("title", "对选中窗口设置静音");
            }
            else {
                $("#id_ctrlvolume").addClass("silent");
                $("#id_ctrlvolume").attr("title", "对选中窗口取消静音");
            }

            OutCtrlVolume();
            SetSelectHigh(GetHighByWndIndex(g_nCurWndIndex));
            break;
        case 2:	// OccurHappen
        {
            var playid = lReserve1;
            var errno = lReserve2;
            var nCurWndIndex = -1;
            for (var nIndex = 0; nIndex < 4; nIndex++) {
                if (playid == GetRealHandleByWndIndex(nIndex)) {
                    nCurWndIndex = nIndex;
                    break;
                }
            }
            if (nCurWndIndex == -1) {
                showTipsMessage("浏览失败");
                break;
            }
            var chan = GetChanByWndIndex(nCurWndIndex);
            var name = GetDevNameByWndIndex(nCurWndIndex);
            showTipsMessage(name + ":" + chan + " 浏览失败. 错误码：" + errno);
        }
            break;
        default:
            break;
    }
}

// PTZ控制
function ClickPTZLeftUp() {

}
function OverPTZLeftUp() {
    document.getElementById("id_ptzleftup").style.background = "url(Public/Manager/keda/images/realplay/leftup_over.png)";
    document.getElementById("id_ptzleftup").style.backgroundSize = "contain";
}
function OutPTZLeftUp() {
    document.getElementById("id_ptzleftup").style.background = "url(Public/Manager/keda/images/realplay_one/leftup.png)";
    document.getElementById("id_ptzleftup").style.backgroundSize = "contain";
}
function DownPTZLeftUp() {
    document.getElementById("id_ptzleftup").style.background = "url(Public/Manager/keda/images/realplay/leftup_press.png)";
    document.getElementById("id_ptzleftup").style.backgroundSize = "contain";
    ControlPTZCmd(4, g_ptzspeed, 0, 0);
}
function UpPTZLeftUp() {
    document.getElementById("id_ptzleftup").style.background = "url(Public/Manager/keda/images/realplay/leftup_over.png)";
    document.getElementById("id_ptzleftup").style.backgroundSize = "contain";
    ControlPTZCmd(8, g_ptzspeed, 0, 0);
}

function ClickPTZUp() {
}
function OverPTZUp() {
    document.getElementById("id_ptzup").style.background = "url('Public/Manager/keda/images/realplay/up_over.png')";
    document.getElementById("id_ptzup").style.backgroundSize = "contain";
}
function OutPTZUp() {
    document.getElementById("id_ptzup").style.background = "url('Public/Manager/keda/images/realplay_one/up.png')";
    document.getElementById("id_ptzup").style.backgroundSize = "contain";
}
function DownPTZUp() {
    document.getElementById("id_ptzup").style.background = "url('Public/Manager/keda/images/realplay/up_press.png')";
    document.getElementById("id_ptzup").style.backgroundSize = "contain";
    ControlPTZCmd(2, g_ptzspeed, 0, 0);
}
function UpPTZUp() {
    document.getElementById("id_ptzup").style.background = "url('Public/Manager/keda/images/realplay/up_over.png')";
    document.getElementById("id_ptzup").style.backgroundSize = "contain";
    ControlPTZCmd(8, g_ptzspeed, 0, 0);
}

function ClickPTZRightUp() {
}
function OverPTZRightUp() {
    document.getElementById("id_ptzrightup").style.background = "url('Public/Manager/keda/images/realplay/rightup_over.png')";
    document.getElementById("id_ptzrightup").style.backgroundSize = "contain";
}
function OutPTZRightUp() {
    document.getElementById("id_ptzrightup").style.background = "url('Public/Manager/keda/images/realplay_one/rightup.png')";
    document.getElementById("id_ptzrightup").style.backgroundSize = "contain";
}
function DownPTZRightUp() {
    document.getElementById("id_ptzrightup").style.background = "url('Public/Manager/keda/images/realplay/rightup_press.png')";
    document.getElementById("id_ptzrightup").style.backgroundSize = "contain";
    ControlPTZCmd(6, g_ptzspeed, 0, 0);
}
function UpPTZRightUp() {
    document.getElementById("id_ptzrightup").style.background = "url('Public/Manager/keda/images/realplay/rightup_over.png')";
    document.getElementById("id_ptzrightup").style.backgroundSize = "contain";
    ControlPTZCmd(8, g_ptzspeed, 0, 0);
}

function ClickPTZLeft() {
//showTipsMessage("left");
}
function OverPTZLeft() {
    document.getElementById("id_ptzleft").style.background = "url('Public/Manager/keda/images/realplay/left_over.png')";
    document.getElementById("id_ptzleft").style.backgroundSize = "contain";
}
function OutPTZLeft() {
    document.getElementById("id_ptzleft").style.background = "url('Public/Manager/keda/images/realplay_one/left.png')";
    document.getElementById("id_ptzleft").style.backgroundSize = "contain";
}
function DownPTZLeft() {
    document.getElementById("id_ptzleft").style.background = "url('Public/Manager/keda/images/realplay/left_press.png')";
    document.getElementById("id_ptzleft").style.backgroundSize = "contain";
    ControlPTZCmd(0, g_ptzspeed, 0, 0);

}
function UpPTZLeft() {
    document.getElementById("id_ptzleft").style.background = "url('Public/Manager/keda/images/realplay/left_over.png')";
    document.getElementById("id_ptzleft").style.backgroundSize = "contain";
    ControlPTZCmd(8, g_ptzspeed, 0, 0);
}

function ClickPTZReset() {
    ControlPTZCmd(12, g_ptzspeed, 0, 0);
}
function OverPTZReset() {
    document.getElementById("id_ptzreset").style.background = "url('Public/Manager/keda/images/realplay_one/reset_over.png')";
    document.getElementById("id_ptzreset").style.backgroundSize = "contain";

}
function OutPTZReset() {

    document.getElementById("id_ptzreset").style.background = "url('Public/Manager/keda/images/realplay_one/reset.png')";
    document.getElementById("id_ptzreset").style.backgroundSize = "contain";

}
function DownPTZReset() {
    document.getElementById("id_ptzreset").style.backgroundSize = "contain";

    document.getElementById("id_ptzreset").style.background = "url('Public/Manager/keda/images/realplay_one/reset_press.png')";
    document.getElementById("id_ptzreset").style.backgroundSize = "contain";

}
function UpPTZReset() {
    document.getElementById("id_ptzreset").style.backgroundSize = "contain";
    document.getElementById("id_ptzreset").style.background = "url('Public/Manager/keda/images/realplay_one/reset_over.png')";
    document.getElementById("id_ptzreset").style.backgroundSize = "contain";

}

function ClickPTZRight() {
//showTipsMessage("right");
}
function OverPTZRight() {
    document.getElementById("id_ptzright").style.background = "url('Public/Manager/keda/images/realplay/right_over.png')";
    document.getElementById("id_ptzright").style.backgroundSize = "contain";

}
function OutPTZRight() {

    document.getElementById("id_ptzright").style.background = "url('Public/Manager/keda/images/realplay_one/right.png')";
    document.getElementById("id_ptzright").style.backgroundSize = "contain";
}
function DownPTZRight() {
    document.getElementById("id_ptzright").style.background = "url('Public/Manager/keda/images/realplay/right_press.png')";
    document.getElementById("id_ptzright").style.backgroundSize = "contain";

    ControlPTZCmd(1, g_ptzspeed, 0, 0);

}
function UpPTZRight() {
    document.getElementById("id_ptzright").style.background = "url('Public/Manager/keda/images/realplay/right_over.png')";
    document.getElementById("id_ptzright").style.backgroundSize = "contain";
    ControlPTZCmd(8, g_ptzspeed, 0, 0);
}

function ClickPTZLeftDown() {
}
function OverPTZLeftDown() {
    document.getElementById("id_ptzleftdown").style.background = "url('Public/Manager/keda/images/realplay/leftdown_over.png')";
    document.getElementById("id_ptzleftdown").style.backgroundSize = "contain";
}
function OutPTZLeftDown() {
    document.getElementById("id_ptzleftdown").style.background = "url('Public/Manager/keda/images/realplay_one/leftdown.png')";
    document.getElementById("id_ptzleftdown").style.backgroundSize = "contain";
}
function DownPTZLeftDown() {
    document.getElementById("id_ptzleftdown").style.backgroundSize = "contain";
    document.getElementById("id_ptzleftdown").style.background = "url('Public/Manager/keda/images/realplay/leftdown_press.png')";
    document.getElementById("id_ptzleftdown").style.backgroundSize = "contain";
    ControlPTZCmd(5, g_ptzspeed, 0, 0);
}
function UpPTZLeftDown() {
    document.getElementById("id_ptzleftdown").style.backgroundSize = "contain";
    document.getElementById("id_ptzleftdown").style.background = "url('Public/Manager/keda/images/realplay/leftdown_over.png')";
    document.getElementById("id_ptzleftdown").style.backgroundSize = "contain";
    ControlPTZCmd(8, g_ptzspeed, 0, 0);
}

function ClickPTZDown() {
}
function OverPTZDown() {
    document.getElementById("id_ptzdown").style.background = "url('Public/Manager/keda/images/realplay/down_over.png')";
    document.getElementById("id_ptzdown").style.backgroundSize = "contain";
}
function OutPTZDown() {
    document.getElementById("id_ptzdown").style.background = "url('Public/Manager/keda/images/realplay_one/down.png')";
    document.getElementById("id_ptzdown").style.backgroundSize = "contain";
}
function DownPTZDown() {
    document.getElementById("id_ptzdown").style.backgroundSize = "contain";
    document.getElementById("id_ptzdown").style.background = "url('Public/Manager/keda/images/realplay/down_press.png')";
    document.getElementById("id_ptzdown").style.backgroundSize = "contain";
    ControlPTZCmd(3, g_ptzspeed, 0, 0);
}
function UpPTZDown() {
    document.getElementById("id_ptzdown").style.backgroundSize = "contain";
    document.getElementById("id_ptzdown").style.background = "url('Public/Manager/keda/images/realplay/down_over.png')";
    document.getElementById("id_ptzdown").style.backgroundSize = "contain";
    ControlPTZCmd(8, g_ptzspeed, 0, 0);
}

function ClickPTZRightDown() {
}
function OverPTZRightDown() {
    document.getElementById("id_ptzrightdown").style.background = "url('Public/Manager/keda/images/realplay/rightdown_over.png')";
    document.getElementById("id_ptzrightdown").style.backgroundSize = "contain";
}
function OutPTZRightDown() {
    document.getElementById("id_ptzrightdown").style.background = "url('Public/Manager/keda/images/realplay_one/rightdown.png')";
    document.getElementById("id_ptzrightdown").style.backgroundSize = "contain";
}
function DownPTZRightDown() {
    document.getElementById("id_ptzrightdown").style.backgroundSize = "contain";
    document.getElementById("id_ptzrightdown").style.background = "url('Public/Manager/keda/images/realplay/rightdown_press.png')";
    document.getElementById("id_ptzrightdown").style.backgroundSize = "contain";

    ControlPTZCmd(7, g_ptzspeed, 0, 0);
}
function UpPTZRightDown() {
    document.getElementById("id_ptzrightdown").style.backgroundSize = "contain";

    document.getElementById("id_ptzrightdown").style.background = "url('Public/Manager/keda/images/realplay/rightdown_over.png')";
    document.getElementById("id_ptzrightdown").style.backgroundSize = "contain";
    ControlPTZCmd(8, g_ptzspeed, 0, 0);
}

function ClickPTZFocusFar() {
}
function OverPTZFocusFar() {
    document.getElementById("id_ptzfocusfar").style.background = "url('Public/Manager/keda/images/realplay_one/focusfar_over.png')";
    document.getElementById("id_ptzfocusfar").style.backgroundSize = "contain";
}
function OutPTZFocusFar() {
    document.getElementById("id_ptzfocusfar").style.background = "url('Public/Manager/keda/images/realplay_one/focus_far.png')";

    document.getElementById("id_ptzfocusfar").style.backgroundSize = "cover";
}
function DownPTZFocusFar() {
    document.getElementById("id_ptzfocusfar").style.background = "url('Public/Manager/keda/images/realplay_one/focusfar_press.png')";
    ControlPTZCmd(9, g_ptzspeed, 0, 0);
    document.getElementById("id_ptzfocusfar").style.backgroundSize = "contain";
}
function UpPTZFocusFar() {
    document.getElementById("id_ptzfocusfar").style.background = "url('Public/Manager/keda/images/realplay_one/focusfar_over.png')";
    ControlPTZCmd(11, g_ptzspeed, 0, 0);
    document.getElementById("id_ptzfocusfar").style.backgroundSize = "contain";
}

function ClickPTZFocusNear() {
}
function OverPTZFocusNear() {
    document.getElementById("id_ptzfocusnear").style.background = "url('Public/Manager/keda/images/realplay_one/focusnear_over.png')";
    document.getElementById("id_ptzfocusnear").style.backgroundSize = "cover";
}
function OutPTZFocusNear() {
    document.getElementById("id_ptzfocusnear").style.background = "url('Public/Manager/keda/images/realplay_one/focus_near.png')";
    document.getElementById("id_ptzfocusnear").style.backgroundSize = "cover";
}
function DownPTZFocusNear() {
    document.getElementById("id_ptzfocusnear").style.background = "url('Public/Manager/keda/images/realplay_one/focusnear_press.png')";
    ControlPTZCmd(10, g_ptzspeed, 0, 0);
    document.getElementById("id_ptzfocusnear").style.backgroundSize = "cover";
}
function UpPTZFocusNear() {
    document.getElementById("id_ptzfocusnear").style.background = "url('Public/Manager/keda/images/realplay_one/focusnear_over.png')";
    ControlPTZCmd(11, g_ptzspeed, 0, 0);
    document.getElementById("id_ptzfocusnear").style.backgroundSize = "cover";
}
function ControlPTZCmd(PtzCmd, PtzRange, Level, HoldTime) {
    InitMcuOcx();
    var tmpdomainid = GetDomainIDByWndIndex(g_nCurWndIndex);
    var tmppuid = GetPuidByWndIndex(g_nCurWndIndex);
    var tmpchan = GetChanByWndIndex(g_nCurWndIndex);
    var tmpmanu = GetManuByWndIndex(g_nCurWndIndex);
    if (tmpdomainid == "" || tmppuid == "") {
        console.log("未选中设备");
        return;
    }
    var chaninfo =
        "<" + g_xmlTDEVCHN + ">" +
        "<" + g_xmlstrDomainID + ">" + tmpdomainid + "</" + g_xmlstrDomainID + ">" +
        "<" + g_xmlstrDeviceID + ">" + tmppuid + "</" + g_xmlstrDeviceID + ">" +
        "<" + g_xmlstrChn + ">" + tmpchan + "</" + g_xmlstrChn + ">" +
        "<" + g_xmlstrSrc + ">" + tmpchan + "</" + g_xmlstrSrc + ">" +
        "</" + g_xmlTDEVCHN + ">";
    var ptzinfo =
        "<" + g_xmlTPtzCmd + ">" +
        "<" + g_xmlstrPtzCmd + ">" + PtzCmd + "</" + g_xmlstrPtzCmd + ">" +
        "<" + g_xmlstrPtzRange + ">" + PtzRange + "</" + g_xmlstrPtzRange + ">" +
        "<" + g_xmlstrLevel + ">" + Level + "</" + g_xmlstrLevel + ">" +
        "<" + g_xmlstrHoldTime + ">" + HoldTime + "</" + g_xmlstrHoldTime + ">" +
        "</" + g_xmlTPtzCmd + ">";
    console.log("通道信息：" + chaninfo + "\nPTZ控制信息：" + ptzinfo);
    if (objMcuOcx) {
        var h = objMcuOcx.StartPtzControl(chaninfo, ptzinfo);
        if(h != 0){//失败
            console.log("开启PTZ控制失败.结果：" + h + "\r\n错误码：" + objMcuOcx.GetLastErrCode());
        }
        //showTipsMessage("ptz end");
    }
}

function InitPtzSpeedSlider() {
    $('#id_ptzspeed').slider({
        max: 14,
        min: 0,
        step: 1,
        value: 8,
        onComplete: function(value){
            g_ptzspeed = value;
            console.log(g_ptzspeed,':   g_ptzspeed');
            $('#id_ptzspeed_value').val(value);
        }
    })
}

//显示右下角提示框
function showTipsMessage(msg){
    $.messager.show({
        title:'提示',
        msg:msg,
        timeout:2000,
        showType:'slide',
        style:{
            left:0,
            right:'',
            bottom:0,
            top:''
        }
    });
}

//显示右下角提示框
function showDialogMessage(msg){
    $.messager.alert('提示',msg,'info');
}
