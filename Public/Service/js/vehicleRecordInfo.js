/**
 * Created by li on 16/10/13.
 */
(function ($, window) {
    var vehicleRecordInfo = {
        lang: {
            prompt: "提示",
            edit: "编辑",
            delete1: "删除",
            sureDelete: "确认删除此项?",
            leastOne: "请至少选择一项!",
            operateFailure: "操作失败,请重试!"
        },
        uploadCarImgDir: '/CourtGms/Uploads/', // 定义上传图片目录
        //初始化
        init: function () {
            var that = this;
            $("#grid").datagrid({
                url: '/CourtGms/index.php?m=Service&c=VehicleRecordInfo&a=getVehicleRecordInfoToList',
                toolbar: '#div_toolbar',
                nowrap: false,
                remoteSort: false,
                border: false,
                pagination: true,
                pageSize: '20',
                rownumbers: true,
                fitColumns: true,
                singleSelect: true,
                autoRowHeight: false,
                fit: true,
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

            // $("#s_start_time").datetimebox('setValue',startTime);
            // $("#s_end_time").datetimebox('setValue',endTime);
            $("#inp_vehicle_id").combobox({
                url: vehicle_license_url,
                required: true,
                editable: false,
                valueField: 'word',
                textField: 'word',
                panelHeight: 100
            });


        },
        extend: function () {
            var that = this;
            //回收jquery ajax请求对象
            $(document).ajaxComplete(function (event, request, settings) {
                request = null;
                delete request;
            });
        },
        //添加
        add: function () {

            $('#div_ftitle').text("添加");

            $("#form_vehicle_record").form('clear');

            var that = this;


            var dateObj = new Date();
            var endTime = dateObj.getFullYear() + "-" + that.round(dateObj.getMonth() + 1) + "-" + that.round(dateObj.getDate()) + " " + this.round(dateObj.getHours()) + ":" + this.round(dateObj.getMinutes()) + ":" + this.round(dateObj.getSeconds());
            var MinMilli = 1000 * 60;
            var HrMilli = MinMilli * 60;
            var DyMilli = HrMilli * 24;
            var nowMilli = dateObj.getTime();
            dateObj.setTime(nowMilli - DyMilli * 1);//推后一天
            var startTime = dateObj.getFullYear() + "-" + that.round(dateObj.getMonth() + 1) + "-" + that.round(dateObj.getDate()) + " " + this.round(dateObj.getHours()) + ":" + this.round(dateObj.getMinutes()) + ":" + this.round(dateObj.getSeconds());
            $("#inp_start_time").datetimebox('setValue', startTime);
            $("#inp_apply_time").datetimebox('setValue', startTime);
            $("#inp_end_time").datetimebox('setValue', endTime);

            $("#inp_vehicle_id").combobox({readonly: false});
            that.operateFlag = "create";

            that.formDisplayToggle('div_dialog', true);
        },
        //编辑
        edit: function (value, time_start, time_end) {

            $('#div_ftitle').text("编辑");

            $("#form_vehicle_record").form('clear');

            console.log("value" + value);

            console.log("start_time" + time_start);

            console.log("end_time" + time_end);

            var that = this;

            that.operateFlag = "Update";


            $("#inp_vehicle_id").attr('old', '').textbox('setValue', '');
            $("#inp_driver_name").textbox('setValue', '');
            $("#inp_start_time").datetimebox('setValue', '');
            $("#inp_end_time").datetimebox('setValue', '');
            $("#inp_apply_man").textbox('setValue', '');
            $("#inp_apply_time").datetimebox('setValue', '');
            $("#inp_destination").textbox('setValue', '');
            $("#inp_remark").textbox('setValue', '');


            $("#inp_vehicle_id").combobox({readonly: true});


            if (value) {
                $.ajax({
                    url: '/CourtGms/index.php?m=Service&c=vehicleRecordInfo&a=' + that.operateFlag,
                    type: 'get',
                    dataType: 'Json',
                    data: {
                        vehicle_license: value,
                        start_date: time_start,
                        end_date: time_end
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
            window.setTimeout(function () {
                $("#inp_vehicle_id").focus();
            }, 500);
            that.formDisplayToggle('div_dialog', true);
        },


        view: function (value, time_start, time_end) {
            var that = this;

            if (value) {
                $.ajax({
                    url: '/CourtGms/index.php?m=Service&c=vehicleRecordInfo&a=Update',
                    type: 'get',
                    dataType: 'Json',
                    data: {
                        vehicle_license: value,
                        start_date: time_start,
                        end_date: time_end
                    }
                })
                    .done(function (result) {
                        if (result.Code == 200) {
                            that.fillFormForShow(result.Result);
                        } else {
                            that.errorMessage(result.Code);
                        }
                    });
            }
            that.formDisplayToggle('view_dialog', true);
        },
        //删除
        deleteData: function (value) {
            console.log("value = " + value);
            var that = this;
            if (value) {
                $.messager.confirm('注意', '此操作不可恢复是否确定？', function (r) {
                    if (r) {
                        $.messager.confirm(that.lang.prompt, that.lang.sureDelete, function (r) {
                            if (r) {
                                $.ajax({
                                    url: '/CourtGms/index.php?m=Service&c=vehicleRecordInfo&a=Delete',
                                    type: 'Post',
                                    dataType: 'Json',
                                    data: {
                                        vehicle_license: value
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

        exportData: function () {
            var data = $('#grid').datagrid('getData');
            var that = this;
            that.formDisplayToggle("div_dialog6", true);
            $.ajax({
                url: "/CourtGms/index.php?m=Service&c=vehicleRecordInfo&a=exportData",
                type: 'post',
                dataType: 'json',
                data: data,
                success: function (data) {
                    that.formDisplayToggle("div_dialog6", false);
                    if (data.Code == 200) {
                        // $.messager.show({
                        //     title:'消息提示',
                        //     msg:'导出数据完成',
                        //     timeout:3000,
                        //     showType:'slide'
                        // });

                        setTimeout(function () {
                            window.location.href = data.Result;
                        }, 100);
                    }
                    else {
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
                multiple: true,
                valueField: 'word_id',
                textField: 'word',
                panelHeight: 100
            });


            $("#s_vehicle_from").combobox({
                url: vehicle_from_url,
                editable: false,
                valueField: 'word_id',
                textField: 'word',
                panelHeight: 'auto'
            });


            function formatDate(date) {
                var now = new Date();
                ;
                if (date) {
                    now = date;
                }

                var year = now.getYear();
                year = year < 1900 ? 1900 + year : year;
                var month = now.getMonth() + 1;
                var date = now.getDate();
                var hour = now.getHours();
                var minute = now.getMinutes();
                var second = now.getSeconds();
                return year + "-" + month + "-" + date;
            }


            var startTime = new Date().getTime() / 1000;

            var startStr = formatDate(new Date(parseInt(startTime) * 1000));

            $('#s_start_time').datebox({
                value: startStr
            });


            that.formDisplayToggle('seach_dialog', true);
        },

        resetQuery: function () {

            $('#grid').datagrid({queryParams: {}});

        },

        SearchSure: function () {

            var queryParams = $('#grid').datagrid('options').queryParams;


            var vehicle_ids = $('#s_vehicle_id').combobox('getValues');

            var vehicle_from = $('#s_vehicle_from').combobox('getValue');

            var driver_name = $('#s_driver_name').textbox('getValue');

            var destination = $('#s_destination').textbox('getValue');

            var start_time = $('#s_start_time').datetimebox('getValue');

            // var end_time = $('#s_end_time').datetimebox('getValue');


            // var username_ids = $('#s_username').combobox('getValues');
            //
            console.log('vehicle_ids =>' + vehicle_ids);
            queryParams['vehicle_id'] = vehicle_ids.toString();
            console.log('queryParams[vehicle_ids] =>' + queryParams['vehicle_ids']);


            console.log('vehicle_from =>' + vehicle_from);
            queryParams['vehicle_from'] = vehicle_from.toString();
            console.log('queryParams[vehicle_from] =>' + queryParams['vehicle_from']);


            console.log('driver_name =>' + driver_name);
            queryParams['driver_name'] = driver_name.toString();
            console.log('queryParams[driver_name] =>' + queryParams['driver_name']);


            console.log('destination =>' + destination);
            queryParams['destination'] = destination.toString();
            console.log('queryParams[destination] =>' + queryParams['destination']);


            console.log('start_time =>' + start_time);
            queryParams['startTime'] = start_time.toString();
            console.log('queryParams[startTime] =>' + queryParams['startTime']);


            $('#grid').datagrid('reload');

        },

        //操作
        operate: function (value, row, index) {
            // console.log(row);
            // return "<a href='javascript:;' onclick='window.vehicleRecordInfo.edit(\"" + row.vehicle_id + "\",\"" + row.damage_time + "\")'>" + window.vehicleRecordInfo.lang.edit + "</a>&nbsp;|&nbsp;<a href='javascript:;' onclick='window.vehicleRecordInfo.deleteData(\"" + row.vehicle_id + "\",\"" + row.damage_time + "\")'>" + window.vehicleRecordInfo.lang.delete1 + "</a>";
            var html = "<div style='height: 30px;padding-top: 10px'>";

            html = html + "<a href='javascript:;' style='text-decoration: none;' onclick='window.vehicleRecordInfo.view(\"" + row.vehicle_license + "\",\"" + row.start_date + "\",\"" + row.end_date + "\")'>" +
                "<img style='border:none;width: 36px;' src='/CourtGms/Public/Static/Easyui/themes/icons/detail.png'></a>&nbsp;"

            if (result_edit) {
                html = html + "<a href='javascript:;' style='text-decoration: none;' onclick='window.vehicleRecordInfo.edit(\"" + row.vehicle_license + "\",\"" + row.start_date + "\",\"" + row.end_date + "\")'>" +
                    "<img style='border:none;width: 36px;' src='/CourtGms/Public/Static/Easyui/themes/icons/edit_add2.png'></a>&nbsp;"
            }
            if (result_del) {
                html = html + "<a href='javascript:;' style='text-decoration: none;' onclick='window.vehicleRecordInfo.deleteData(\"" + row.vehicle_license + "\",\"" + row.start_date + "\",\"" + row.end_date + "\")'>" +
                    "<img style='border:none;width: 36px;' src='/CourtGms/Public/Static/Easyui/themes/icons/delete1.png'></a>&nbsp;"

            }
            // console.log("aaaaa"+result_edit);
            return html + "</div>";
        },
        //确定操作
        operateSure: function () {
            var action = this.operateFlag == "create" ? "Create" : "Update";
            var that = this;
            $("#form_vehicle_record").form('submit', {
                url: "/CourtGms/index.php?m=Service&c=vehicleRecordInfo&a=" + action,
                onSubmit: function () {

                    var flag = true;

                    flag = flag && $('#inp_vehicle_id').combobox('isValid');


                    flag = flag && $('#inp_driver_name').textbox('isValid');


                    flag = flag && $('#inp_apply_man').textbox('isValid');


                    flag = flag && $('#inp_start_time').datetimebox('isValid');


                    flag = flag && $('#inp_end_time').datetimebox('isValid');


                    var start_time = $('#inp_start_time').datetimebox('getValue');


                    var end_time = $('#inp_end_time').datetimebox('getValue');


                    flag = flag && $('#inp_apply_time').datetimebox('isValid');


                    if (!flag) {
                        $.messager.alert(that.lang.prompt, "数据填写不完整或存在错误,请检查后重试", 'info');
                        return flag;
                    }


                    startTime = new Date(start_time.replace("-", "/").replace("-", "/"));
                    console.log(startTime);
                    endTime = new Date(end_time.replace("-", "/").replace("-", "/"));
                    console.log(endTime);

                    if (endTime <= startTime) {
                        $.messager.alert(that.lang.prompt, "回车时间不能晚于出车时间", 'info');

                        return false;
                    }


                },
                success: function (data) {
                    var jsonResult = $.parseJSON(data);


                    if (jsonResult.Result) {
                        $("#grid").datagrid('reload');

                        that.formDisplayToggle('div_dialog', false);

                        $.messager.show({
                            title: '操作结果',
                            msg: jsonResult.Msg,
                            timeout: 3000,
                            showType: 'slide'
                        });
                    }
                    else {
                        $.messager.alert('提示','错误原因:'+jsonResult.Msg,'info');
                    }

                }
            });
        },
        //取消操作
        operateCancel: function () {
            this.operateFlag = undefined;
            this.formDisplayToggle('div_dialog', false);
            $("#grid").datagrid('unselectAll');
        },
        //补零
        round: function (value) {
            value = parseInt(value);
            return value < 10 ? ("0" + value) : value;
        },
        //打印
        print: function () {
            window.print();

            // $("#grid").jqprint({
            //     importCSS: true,
            //     printContainer: true,
            //     operaSupport: true
            // });

        },
        //填充表单
        fillForm: function (obj) {

            console.log(obj);

            if (obj) {
                $("#inp_id").val(obj.id);
                $("#inp_vehicle_id").attr('old', obj.vehicle_license).textbox('setValue', obj.vehicle_license);
                $("#inp_driver_name").textbox('setValue', obj.driver_name);
                $("#inp_start_time").datetimebox('setValue', obj.start_date);
                $("#inp_end_time").datetimebox('setValue', obj.end_date);
                $("#inp_apply_man").textbox('setValue', obj.apply_man);
                $("#inp_apply_time").datetimebox('setValue', obj.apply_date);
                $("#inp_destination").textbox('setValue', obj.destination);
                $("#inp_remark").textbox('setValue', obj.remark);


            }
        },
        //填充表单
        fillFormForShow: function (obj) {
            console.log(obj);
            var that = this;
            if (obj) {


                $("#vehicle_id_view").textbox('setValue', obj.vehicle_license);

                $("#vehicle_brand_view").textbox('setValue', obj.vehicle_brand);

                if ('1' == obj.vehicle_state.toString()) {

                    $("#vehicle_state_view").textbox('setValue', "启用");
                }
                else {
                    $("#vehicle_state_view").textbox('setValue', "已停用");
                }

                $("#vehicle_from_view").combobox({url: vehicle_from_url});


                $("#vehicle_from_view").combobox('setValue', obj.vehicle_from);


                $("#driver_name_view").textbox('setValue', obj.driver_name);

                $("#device_name_view").textbox('setValue', obj.device_name);


                $("#start_time_view").datetimebox('setValue', obj.start_date);

                $("#end_time_view").datetimebox('setValue', obj.end_date);


                $("#apply_name_view").textbox('setValue', obj.apply_man);

                $("#apply_time_view").datetimebox('setValue', obj.apply_date);

                $("#destination_view").textbox('setValue', obj.destination);

                $("#remark_view").textbox('setValue', obj.remark);


                if (obj.vehicle_photo != 'null' && obj.vehicle_photo != null && obj.vehicle_photo.length > 0) {
                    $('#img_photo_view').attr('src', that.uploadCarImgDir + obj.vehicle_photo);
                }
                else {
                    $('#img_photo_view').attr('src', "/CourtGms/Public/Service/img/profile.png");
                }
            }
        },
        //表单显示切换
        formDisplayToggle: function (id, flag) {
            if (flag) {
                $("#" + id).dialog('center');
                $("#" + id).dialog('open');
            } else {
                $("#" + id).dialog('close');
            }
        }
    };
    $(function () {
        vehicleRecordInfo.init();
        vehicleRecordInfo.extend();
        if (window.parent.guide) {
            window.parent.guide.removeLoading();
        }
    });
    return window.vehicleRecordInfo = window.vehicleRecordInfo || vehicleRecordInfo;
})($, window);