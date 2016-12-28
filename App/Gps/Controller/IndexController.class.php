<?php
namespace Gps\Controller;
use Gps\Model\BaiduConvertModel;
use Gps\Model\KeDaGpsModel;
use Gps\Model\KeDaOnlineModel;
use Gps\Model\VehicleDetailViewModel;
use Gps\Model\GpsModel;
use Gps\Model\LastGpsModel;
use Think\Controller;
use Think\Exception;
use Think\Log;


class IndexController extends Controller {
    const gps_pre="";

    /**
     * 获取车辆最近一次gps信息
     * @param $vehicle_ids .要获取车辆的id，使用json数组格式传入
     */
    public function getLastGps($vehicle_ids)
    {

        $vehicle_ids = json_decode($vehicle_ids, true);
        if(is_array($vehicle_ids)&&$vehicle_ids)
        {
            $where["vehicle_id"]=array("in",$vehicle_ids);
            $lastGpsModel = new LastGpsModel();
            $gpsData=$lastGpsModel->where($where)->select();
            ajaxSuccess("获取数据成功",BaiduConvertModel::convertAllToBaidu($gpsData));
        }else{
            ajaxError("参数错误或无参数!");
        }
    }

    /**
     * 获取车辆gps历史记录信息
     * @param $vehicle_id .要获取车辆的id
     * @param $start_time .查询起始日期
     * @param $end_time .查询结束日期
     * @convert_type 是否需要坐标转换，转换到百度
     * 测试实例 http://127.0.0.1/CourtGms/index.php?m=Gps&c=Index&a=getHistoryGps&need_convert=true&vehicle_id=328&start_time=2016-10-26%2000:00:00&end_time=2016-10-26%2023:59:59
     */
    public function getHistoryGps()
    {
        $paramData=checkParam("vehicle_id","start_time","end_time");
        if(is_array($paramData))
        {
            $vehicleId=$paramData["vehicle_id"];
            $vehicleDetailModel=new VehicleDetailViewModel();
            $where["vehicle_id"]=$vehicleId;
            $vehicleDetail=$vehicleDetailModel->where($where)->find();
            if ($vehicleDetail["vehicle_from"]==2)
            {
                $model=new KeDaGpsModel($vehicleDetail["device_id"]);
            }else{
                $model=new GpsModel($vehicleId);
            }
            try{
                $gpsData=$model->getHistoryGps($paramData["start_time"],$paramData["end_time"]);
                ajaxSuccess("获取数据成功",BaiduConvertModel::convertAllToBaidu($gpsData));
            }catch (\Exception $e)
            {
                ajaxSuccess("没有数据");
            }
        }else{
            ajaxError($paramData);
        }
    }


    /**
     * 获取科达平台在线车辆接口
     */
    public function getOnlineDevice()
    {
        $kedaOnlineModel=new KeDaOnlineModel();
        try{
            $onLineCars=$kedaOnlineModel->getOnlineCars();
            ajaxSuccess("获取在线列表成功",$onLineCars);
        }catch (Exception $e)
        {
            ajaxError("接口返回数据异常");
        }
    }

    /**
     * 第三方车辆更新GPS接口
     */
    public function updateGps()
    {
        $paramData=checkParam("deviceName","lat","lng","time","speed","direction");
        if(is_array($paramData))
        {
            $gmsVehicleModel=new VehicleDetailViewModel();
            $vehicle_id=$gmsVehicleModel->getVehicleIdByName($paramData["deviceName"]);
            if(!$vehicle_id)
            {
                ajaxError("更新失败，请检查是否在系统注册.");
                die;
            }
            if(IS_GET){
                $data=$_GET;
            }else{
                $data=$_POST;
            }
            $gpsModel=new GpsModel($vehicle_id);
            try{
                $addResult=$gpsModel->add($data);
            }catch (Exception $e)
            {

            }
            $lastGpsModel = new LastGpsModel();
            $lastGpsModel->tryAdd($vehicle_id,$data);
            if($addResult)
            {
                ajaxSuccess("更新成功");
            }else{
                ajaxError("更新失败,请检查GPS时间");
            }
        }else{
            ajaxError($paramData);
        }
    }
    public function generateLogs(){
        $logPath=RUNTIME_PATH."Logs";
        $tarLogPath=EXPORT_PATH.date("Y-m-d_H_i_s")."Logs.zip";
        $tarCmd="tar -zcf  $tarLogPath $logPath";
        $execResult = shell_exec($tarCmd);
        Log::record($execResult,"ALERT");
        if (file_exists($tarLogPath))
        {
            header("location:".C_WEB_ROOT.substr($tarLogPath,1));
        }else{
            echo "导出日志出错,请联系管理员";
        }
    }

    public function clearExportData()
    {
        $cmd="cd /var/www/html/CourtGms/Export&&rm *";
        shell_exec($cmd);
    }

}




