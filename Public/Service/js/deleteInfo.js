/**
 * Created by li on 16/9/12.
 */
(function ($, window) {
    var deleteInfo = {
        lang: {},
        groupName:[],
        vehicleLicense:[],
        //初始化
        init: function () {
            var that=this;
            $.ajax({
                type: "GET",
                url:"/CourtGms/index.php?m=Service&c=DeleteRecordInfo&a=getGroupName",
                dataType: "json",
                success: function(data){
                    that.groupName = data;
                }
            });
            $.ajax({
                type: "GET",
                url:"/CourtGms/index.php?m=Service&c=VehicleRepairInfo&a=getVehicleLicenseList",
                dataType: "json",
                success: function(data){
                    that.vehicleLicense = data;
                }
            });
            $("#grid").datagrid({
                url:'/CourtGms/index.php?m=Service&c=DeleteRecordInfo&a=getDeleteRecordList&value',
                toolbar:'#div_toolbar',
                rownumbers:true,
                nowrap: false,
                striped: true,
                remoteSort: false,
                border: false,
                pagination: true,
                pageSize: '20',
                fit: true,
                onLoadError:function(){
                    $.messager.alert(that.lang.prompt,that.lang.loadError,'error');
                }
            });


        },
        formatVehicleLicense:function(value,row,index){
            var result='';
            console.log(value);
            if(value==undefined||value==null)
                return result;
            $.each(window.deleteInfo.vehicleLicense, function(index, val) {
                if(val.word_id==value.toString()){
                    result= val.word;
                }
            });
            return result;
        },
        formatGroupName:function(value,row,index){
            var result='';
            if(value==undefined||value==null)
                return result;
            $.each(window.deleteInfo.groupName, function(index, val) {
                if(val.group_id==value.toString()){
                    result= val.group_name;
                }
            });
            return result;
        },
        operate:function(value, row, index){
            return "<a href='javascript:;' onclick='window.deleteInfo.rollback(\"" + row.delete_time + "\",\"" + row.columns_id + "\",\"" + row.columns_no + "\",\"" + row.columns_from + "\")'>恢复</a>";
        },
        //查询
        query: function (value, name) {
            value = window.deleteInfo.filterChar(value);
            if(value.length>0){
                $("#grid").datagrid({
                    url:'/CourtGms/index.php?m=Service&c=DeleteRecordInfo&a=getDeleteRecordList&value='+value+'&columnName='+name
                });
            }
        },
        rollback: function (delete_time,columns_id,columns_no,columns_from) {
            $.post(
                '/CourtGms/index.php?m=Service&c=DeleteRecordInfo&a=rollBack',
                {
                    columns_id : columns_id,
                    columns_no : columns_no,
                    delete_time : delete_time,
                    columns_from : columns_from
                },
                function (data) {
                    var jsonResult = $.parseJSON(data);
                    if (jsonResult.Code == 200) {
                        if (jsonResult.Result) {
                            $("#grid").datagrid('reload');
                            $.messager.alert("提示", jsonResult.Msg , 'info');
                        } else {
                            $.messager.alert("提示", jsonResult.Msg , 'error');
                        }
                    }
                    else{
                        $.messager.alert("提示", jsonResult.Msg , 'error');
                    }
                }
            );
        },
        //过滤字符
        filterChar: function (s) {
            var pattern = new RegExp("[%--`~!@#$^&*()=|{}':;',\\[\\].<>/?~！@#￥……&*（）——|{}【】‘；：”“'。，、？]");        //格式 RegExp("[在中间定义特殊过滤字符]")
            var rs = "";
            for (var i = 0; i < s.length; i++) {
                rs = rs + s.substr(i, 1).replace(pattern, '');
            }
            return rs;
        },
        resetQuery: function () {
            $("#inp_search").searchbox('setValue', '');
            $("#grid").datagrid({
                url:'/CourtGms/index.php?m=Service&c=DeleteRecordInfo&a=getDeleteRecordList&value'
            });
        },

    };
    $(function () {
        deleteInfo.init();
    });
    return window.deleteInfo = window.deleteInfo || deleteInfo;
})($, window);
