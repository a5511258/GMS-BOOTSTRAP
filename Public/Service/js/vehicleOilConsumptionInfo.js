/**
 * Created by li on 16/10/13.
 */
(function ($, window) {
    var vehicleOilConsumptionInfo = {
        lang: {
            prompt: "提示",
            edit:"编辑",
            delete1:"删除",
            sureDelete:"确认删除此项?",
            leastOne : "请至少选择一项!",
            operateFailure : "操作失败,请重试!"
        },
        //初始化
        init: function () {
            var that = this;
            $("#grid").datagrid({
                url: '/CourtGms/index.php?m=Service&c=VehicleOilConsumptionInfo&a=getVehicleOilConsumptionInfoToList',
                toolbar: '#div_toolbar',
                nowrap: false,
                remoteSort: false,
                border: false,
                pagination: true,
                pageSize: '20',
                pageNumber: 1,
                pageList: [10, 20, 50],
                sortName: 'id',
                sortOrder: 'asc',
                rownumbers: true,
                singleSelect:true,
                autoRowHeight:false,
                fit: true,
                fitColumns:true,
                onLoadError: function () {
                    $.messager.alert(that.lang.prompt, that.lang.loadError, 'error');
                },
                onLoadSuccess: function (data) {
                    // console.log(data);

                    if (0 == data.total) {
                        $.messager.show({
                            title: '搜索结果',
                            msg: '搜索完成,暂无数据',
                            timeout: 3000,
                            showType: 'slide'
                        });
                    }
                }
            });

            $("#inp_vehicle_id").combobox({
                url: vehicle_license_url,
                required: true,
                editable: false,
                valueField:'word_id',
                textField:'word',
                panelHeight:100
            });


        },
        //添加
        add: function () {

            $('#div_ftitle').text("添加");

            $("#form_vehicle_oil").form('clear');

            var that = this;
            var date = new Date();
            var todayDate = date.toLocaleDateString();

            $('#').datebox({
                value: todayDate
            });

            $("#inp_vehicle_id").combobox({readonly:false});

            that.operateFlag = "create";

            that.formDisplayToggle('div_dialog',true);
        },
        //编辑
        edit: function (value,id) {

            $('#div_ftitle').text("编辑");

            $("#form_vehicle_oil").form('clear');

            var that = this;

            that.operateFlag = "Update";

            $("#inp_vehicle_id").combobox({readonly:true});


            if (value) {
                $.ajax({
                    url: '/CourtGms/index.php?m=Service&c=VehicleOilConsumptionInfo&a='+that.operateFlag,
                    type: 'get',
                    dataType: 'Json',
                    data: {
                        vehicle_id:value,
                        id:id
                    }
                })
                    .done(function (result) {
                        if (result.Code == 200) {
                            that.fillForm(result.Result);
                        } else {
                            that.errorMessage(result.Code);
                        }
                    });
            }
            window.setTimeout(function(){$("#inp_cost").focus();},500);
            that.formDisplayToggle('div_dialog',true);
        },
        //删除
        deleteData: function (value,id) {
            console.log("value = " +value);
            var that = this;
            if (value) {
                $.messager.confirm('注意', '此操作不可恢复是否确定？', function (r) {
                    if (r) {
                        $.messager.confirm(that.lang.prompt, that.lang.sureDelete, function (r) {
                            if (r) {
                                $.ajax({
                                    url: '/CourtGms/index.php?m=Service&c=VehicleOilConsumptionInfo&a=Delete',
                                    type: 'Post',
                                    dataType: 'Json',
                                    data: {
                                        vehicle_id: value,
                                        id:id
                                    }
                                })
                                    .done(function (result) {
                                        if (result.Code == 200) {
                                            if (result.Result) {
                                                $("#grid").datagrid('reload');
                                                $.messager.alert(that.lang.prompt, result.Msg, 'info');
                                            } else {
                                                $.messager.alert(that.lang.prompt, that.lang.operateFailure, 'error');
                                            }
                                        } else {
                                            that.errorMessage(result.Code);
                                        }
                                    });
                            } else {
                                $("#grid").datagrid('unselectAll');
                            }
                        });
                    }
                    else {
                        $("#grid").datagrid('unselectAll');
                    }
                });
            }
        },

        exportData:function(){
            var data = $('#grid').datagrid('getData');
            var that = this;
            that.formDisplayToggle("div_dialog6", true);
            $.ajax({
                url:"/CourtGms/index.php?m=Service&c=vehicleOilConsumptionInfo&a=exportData",
                type:'post',
                dataType:'json',
                data: data,
                success:function(data) {
                    that.formDisplayToggle("div_dialog6", false);
                    if (data.Code == 200) {
                        // $.messager.show({
                        //     title:'消息提示',
                        //     msg:'导出数据完成',
                        //     timeout:3000,
                        //     showType:'slide'
                        // });

                        setTimeout(function(){
                            window.location.href = data.Result;
                        },100);
                    }
                    else{
                        that.errorMessage(data.Code);
                    }
                }
            });
        },
        //查询
        query: function () {

            var that = this;


            $("#s_vehicle_id").combobox({
                url: vehicle_license_url,
                editable: false,
                multiple:true,
                valueField:'word_id',
                textField:'word',
                panelHeight:100
            });




            that.formDisplayToggle('seach_dialog',true);
        },

        resetQuery:function () {

            $('#grid').datagrid({queryParams: {}});

        },

        SearchSure:function () {

            var queryParams = $('#grid').datagrid('options').queryParams;

            var vehicle_ids = $('#s_vehicle_id').combobox('getValues');

            var vehicle_driver = $('#s_vehicle_driver').textbox('getValue');

            var cost = $('#s_cost').textbox('getValue');



            console.log('vehicle_ids =>' + vehicle_ids);
            queryParams['vehicle_id'] = vehicle_ids.toString();
            console.log('queryParams[vehicle_ids] =>' + queryParams['vehicle_ids']);


            console.log('vehicle_driver =>' + vehicle_driver);
            queryParams['vehicle_driver'] = vehicle_driver.toString();
            console.log('queryParams[vehicle_driver] =>' + queryParams['vehicle_driver']);



            console.log('cost =>' + s_cost);
            queryParams['cost'] = cost.toString();
            console.log('queryParams[cost] =>' + queryParams['cost']);






            $('#grid').datagrid('reload');
        },

        //操作
        operate: function (value, row, index) {
            // console.log(row);
            // return "<a href='javascript:;' onclick='window.vehicleOilConsumptionInfo.edit(\"" + row.vehicle_id + "\",\"" + row.damage_time + "\")'>" + window.vehicleOilConsumptionInfo.lang.edit + "</a>&nbsp;|&nbsp;<a href='javascript:;' onclick='window.vehicleOilConsumptionInfo.deleteData(\"" + row.vehicle_id + "\",\"" + row.damage_time + "\")'>" + window.vehicleOilConsumptionInfo.lang.delete1 + "</a>";
            var html = "<div style='height: 30px;padding-top: 10px'>";
            if(result_edit){
                html = html +"<a href='javascript:;' style='text-decoration: none;' onclick='window.vehicleOilConsumptionInfo.edit(\"" + row.vehicle_id + "\",\"" + row.id + "\")'>" +
                    "<img style='border:none;width: 36px;' src='/CourtGms/Public/Static/Easyui/themes/icons/edit_add2.png'></a>&nbsp;"
            }
            if(result_del){
                html = html +"<a href='javascript:;' style='text-decoration: none;' onclick='window.vehicleOilConsumptionInfo.deleteData(\"" + row.vehicle_id + "\",\"" + row.id + "\")'>" +
                    "<img style='border:none;width: 36px;' src='/CourtGms/Public/Static/Easyui/themes/icons/delete1.png'></a>&nbsp;"

            }
            // console.log("aaaaa"+result_edit);
            return html+"</div>";
        },
        //确定操作
        operateSure: function () {
            var action = this.operateFlag == "create" ? "Create" : "Update";
            var that = this;
            $("#form_vehicle_oil").form('submit', {
                url: "/CourtGms/index.php?m=Service&c=VehicleOilConsumptionInfo&a="+action,
                onSubmit: function () {


                    var flag = true;

                    flag = flag && $('#inp_vehicle_id').combobox('isValid');

                    flag = flag && $('#inp_rising_number_of_fuel').numberbox('isValid');

                    flag = flag && $('#inp_cost').numberbox('isValid');


                    if(!flag){
                        $.messager.alert(that.lang.prompt,"数据填写不完整或存在错误,请检查后重试", 'info');
                        return flag;
                    }
                },
                success: function (data) {
                    var jsonResult = $.parseJSON(data);

                    console.log(jsonResult);

                        if (jsonResult.Result) {
                            $("#grid").datagrid('reload');
                            that.formDisplayToggle('div_dialog',false);
                            $.messager.show({
                                title: '操作结果',
                                msg: jsonResult.Msg,
                                timeout: 3000,
                                showType: 'slide'
                            });
                        }
                        else{
                            $.messager.alert('提示','错误原因:'+jsonResult.Msg,'info');
                        }

                }
            });
        },
        //取消操作
        operateCancel: function () {
            this.operateFlag = undefined;
            this.formDisplayToggle('div_dialog',false);
            $("#grid").datagrid('unselectAll');
        },
        //打印
        print:function() {
            window.print();

        },
        //填充表单
        fillForm: function (obj) {

            console.log(obj);

            if (obj) {
                $("#inp_id").val(obj.id);
                $("#inp_vehicle_id").attr('old',obj.vehicle_license).combobox('select',obj.vehicle_id);
                $("#inp_vehicle_driver").textbox('setValue', obj.vehicle_driver);
                $("#inp_rising_number_of_fuel").numberbox('setValue',obj.rising_number_of_fuel);
                $('#inp_time').datebox('setValue',obj.time);
                $("#inp_cost").numberbox('setValue',obj.cost);
                $("#inp_now_mileage").numberbox('setValue',obj.now_mileage);
                $("#inp_remark").textbox('setValue',obj.remark);


            }
        },
        //表单显示切换
        formDisplayToggle: function (id,flag) {
            if (flag) {
                $("#"+id).dialog('center');
                $("#"+id).dialog('open');
            } else {
                $("#"+id).dialog('close');
            }
        }
    };
    $(function () {
        vehicleOilConsumptionInfo.init();
        if(window.parent.guide){
            window.parent.guide.removeLoading();
        }
    });
    return window.vehicleOilConsumptionInfo = window.vehicleOilConsumptionInfo || vehicleOilConsumptionInfo;
})($, window);