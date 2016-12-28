<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 2016/10/12
 * Time: 10:57
 */

namespace Gps\Controller;


use Gps\Model\KedaGpsSyncModel;
use Gps\Model\KedaInfoModel;
use Gps\Model\RequestLogModel;
use Gps\Model\VehicleDetailViewModel;
use Service\Model\VehicleRecordModel;
use SoapClient;
use Think\Controller;
use Think\Exception;

class SyncController extends Controller
{
    const DEFAULT_START_DATE="1990-10-11 11:39:24";
    protected $message;
    /**
     * 科达基础数据同步
     */
    public function syncKeDaBaseData()
    {
        $currDate=currentDate();
        echo $currDate;
        header("Content-Type:text/html;charset=utf-8");
        $sql = "select `tblDevice`.`devName` AS `device_name`,`tblDevice`.`devId` AS `device_id`,`tblDevice`.`domainId` AS `domain_id`,`tblDeviceModel`.`capNum` AS `cap_num` from (`tblDevice` join `tblDeviceModel`) where ((`tblDevice`.`devType` = 1) and (`tblDeviceModel`.`devCapId` = 1) and (`tblDevice`.`devModelId` = `tblDeviceModel`.`modelId`))";
        $model = M("", "", DB_CONFIG_NAME_KEDA_KDMAAA);
        $kedaInfoModel = new KedaInfoModel();
        $ours = $kedaInfoModel->getField("device_id,device_name,device_id,domain_id,cap_num");
        $othersTemp = $model->query($sql);
        foreach ($othersTemp as $key => $item) {
            $others[$item["device_id"]] = $item;
        }
        $needAdds = array();
        $deleteCount=0;
        $updateCount=0;
        $addCount=0;
        foreach ($others as $deviceId => $device) {
            if (isset($ours[$deviceId]))//如果本地库有的话
            {
                $diffResult = array_diff($device, $ours[$deviceId]);
                if ($diffResult)//有数据差异
                {
                    try {
                    $updateCount+=$kedaInfoModel->save($device);
                    } catch (\Exception $e) {
                        $this->attachMessage("更新".$device["device_id"]."失败");
                    }
                }
                unset($ours[$deviceId]);
            } else {//如果本地库没有
                $needAdds[] = $device;
            }
        }
        if ($ours) {
            p("有待删除设备设备");
            foreach ($ours as $ourDelete) {
                try {
                    $deleteCount += $kedaInfoModel->delete($ourDelete["device_id"]);
                } catch (\Exception $e) {
                    $this->attachMessage("删除".$ourDelete["device_id"]."失败");
                }
            }
        }
        if ($needAdds) {
            p("有新设备");
            foreach ($needAdds as $needAdd) {
                try {
                    $addCount = $kedaInfoModel->add($needAdd);
                } catch (\Exception $e) {
                    $this->attachMessage("添加".$needAdd["device_id"]."失败");
                }
            }
        }
        if($updateCount||$deleteCount||$addCount){
            $this->attachMessage("更新$updateCount . 删除$deleteCount .添加$addCount");
        }else{
            $this->attachMessage("没有任何更新.");
        }
        echo $this->message;
    }

    function attachMessage($msg){
        $this->message.=$this->message."$msg \n";
    }

    /**
     * 同步科达在线设备gps位置
     */
    public function syncKeDaGPS()
    {
        $model=new KedaGpsSyncModel();
        ajaxSuccess("同步成功",$model->startSyncOnlineGps());
    }

    public function syncVehicleRecords()
    {
        $currDate=currentDate();
        echo $currDate;
        $vehicleModel= new VehicleRecordModel();
        $requestLogModel= new RequestLogModel();
        $startDate= $requestLogModel->getLastSyncRecordTime();
        $endDate=currentDate();
        if(!USE_SIMULATE_DATA)
        {
            $result = $this->requestSoap($startDate, $endDate);
        }else{
            $result=file_get_contents("./Test/keda_car_record.xml");
        }
        if ($result)
        {
            $xml = simplexml_load_string($result);
            if ($xml->message->result==1&&isset($xml->data->list)&&count($xml->data->list)>0)
            {
                $count=0;
                foreach ($xml->data->list as $record) {
                    $record=(array)$record;
                    $item["car_license"]=$record["voitureNum"];
                    $item["start_date"]=$record["startDate"];
                    $item["end_date"]=$record["endDate"];
                    $item["driver_name"]=$record["motorMan"];
                    $item["apply_date"]=$record["applyDate"];
                    $item["apply_man"]=$record["applyMan"];
                    $item["destination"]=$record["destination"];
                    try{
                        $count+=$vehicleModel->add($item);
                    }catch (\Exception $e)
                    {

                    }
                }
                $requestLogModel->setLastSyncRecordTime($currDate);
                ajaxSuccess("成功更新$count 条记录");
            }else{
                $requestLogModel->setLastSyncRecordTime($currDate);
                ajaxSuccess("没有新记录");
            }
        }else{
            ajaxError("请求协同办公数据接口失败");
        }
    }

    /**
     * @param $startDate
     * @param $endDate
     * @return mixed
     */
    protected function requestSoap($startDate, $endDate)
    {
        $soapOption = array(
            //注意: 这个location指定的是server端代码在服务器中的具体位置, 而且不能在最后加上"?wdsl"字符串
            'location' => XIETONG_HOST . '/defaultroot/xfservices/GeneralWeb',
            #'location'=>'http://192.168.100.187:8080/GBMP/services/CTIService?wsdl', ##错误
            'uri' => XIETONG_HOST,
        );
        $client = new SoapClient(null, $soapOption);
        $input = "<input><key>dffd512f3c274ec11af53753fc82b483</key><cmd>getCompletedVoitureApply</cmd><domainId>0</domainId><startTime>$startDate</startTime><endTime>$endDate</endTime></input>";
        $result = $client->__Call('OAManager', array($input));
        return $result;
    }


}