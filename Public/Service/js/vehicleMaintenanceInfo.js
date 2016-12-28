/**
 * Created by l on 2016年09月22日18:42:07
 */
(function ($, window) {
    var vehicleMaintenanceInfo = {
        lang: {
            prompt: "提示",
            edit: "编辑",
            delete1: "删除",
            sureDelete: "确认删除这条内容?",
            leastOne: "请至少选择一项!",
            operateFailure: "操作失败,请重试!"
        },
        MaintenanceIdentifyState: [],
        MaintenanceState: [],
        Vehicle_license: [],
        operateFlag: undefined,
        //初始化
        init: function () {
            var that = this;

            $("#inp_vehicle_id").combobox({
                url: vehicle_license_url,
                required: true,
                editable: false,
                valueField: 'word_id',
                textField: 'word',
                panelHeight: 100
            });

            $("#inp_maintenance_identify_state").combobox({
                url: maintenance_url2,
                required: true,
                editable: false,
                valueField: 'word_id',
                textField: 'word',
                panelHeight: 'auto',
                onLoadSuccess: function () {
                    var data = $("#inp_maintenance_identify_state").combobox('getData');
                    that.MaintenanceIdentifyState = data;
                }
            });

            $("#inp_maintenance_state").combobox({
                url: maintenance_url1,
                required: true,
                editable: false,
                valueField: 'word_id',
                textField: 'word',
                panelHeight: 'auto',
                onLoadSuccess: function () {
                    var data = $("#inp_maintenance_state").combobox('getData');
                    that.MaintenanceState = data;
                }
            });


            $("#grid").datagrid({
                url: '/CourtGms/index.php?m=Service&c=VehicleMaintenanceInfo&a=getVehicleMaintenanceInfoToList',
                toolbar: '#div_toolbar',
                pageSize: 20,
                pageList: [10, 20, 50],
                pageNumber: 1,
                sortName: 'maintenance_time',
                sortOrder: 'asc',
                nowrap: false,
                remoteSort: false,
                border: false,
                pagination: true,
                rownumbers: true,
                singleSelect: true,
                autoRowHeight: false,
                fit: true,
                fitColumns: true,
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

        },

        formattermaintenanceIdentifyState: function (value, row, index) {
            var result = '';
            $.each(window.vehicleMaintenanceInfo.MaintenanceIdentifyState, function (index, val) {
                if (val.word_id == value.toString()) {
                    result = val.word;
                }
            });
            return result;
        },

        formattermaintenanceState: function (value, row, index) {
            var result = '';
            $.each(window.vehicleMaintenanceInfo.MaintenanceState, function (index, val) {
                if (val.word_id == value.toString()) {
                    result = val.word;
                }
            });
            return result;
        },
        extend: function () {
            var that = this;
            //扩展jquery easy ui检测控件validatebox的检测规则。
            $.extend($.fn.textbox.defaults.rules, {
                //字母汉字数字
                lettersChineseDigital: {
                    validator: function (value, param) {
                        var reg = /^[\s\u4E00-\u9FA5A-Za-z0-9-_]*$/;
                        value = $.trim(value);
                        return reg.test(value) && value.length <= param && value.length > 0;
                    },
                    message: that.lang.inputError
                }
            });
            //回收jquery ajax请求对象
            $(document).ajaxComplete(function (event, request, settings) {
                request = null;
                delete request;
            });
        },
        //添加
        add: function () {
            $("#form_vehicle_maintenance").form('clear');

            $("#inp_vehicle_id").combobox({
                readonly: false
            });
            $("#inp_maintenance_time").datetimebox({
                readonly: false
            });
            this.operateFlag = 'add';
            var that = this;
            $("#div_ftitle").text("添加");
            $("#inp_vehicle_id").combobox('clear');
            $("#inp_maintenance_identify_state").combobox('setValue', 1);
            $("#inp_maintenance_state").combobox('setValue', 1);
            var dateObj = new Date();
            var todayDate = dateObj.getFullYear() + "-" + (dateObj.getMonth() + 1) + "-" + dateObj.getDate() + " " + (dateObj.getHours()) + ":" + dateObj.getMinutes() + ":" + (dateObj.getSeconds());
            $("#inp_maintenance_time").datetimebox('setValue', todayDate);
            that.formDisplayToggle('div_dialog', true);
        },
        //编辑
        edit: function (value, id) {


            console.log(value);

            console.log(id);

            $("#form_vehicle_maintenance").form('clear');

            $("#inp_vehicle_id").combobox({
                readonly: true
            });
            $("#inp_maintenance_time").datetimebox({
                readonly: false
            });
            this.operateFlag = 'edit';
            var that = this;
            $("#div_ftitle").text(this.lang.edit);
            $("#inp_vehicle_id").combobox('clear');
            $("#inp_maintenance_identify_state").combobox('clear');
            $("#inp_maintenance_state").combobox('clear');
            $("#inp_maintenance_reason").textbox('clear');
            $("#inp_maintenance_cost").textbox('clear');
            $("#inp_maintenance_time").datetimebox('clear');
            if (value) {
                $.ajax({
                    url: '/CourtGms/index.php?m=Service&c=vehicleMaintenanceInfo&a=getvehicleMaintenanceInfoByVehicleId',
                    type: 'Post',
                    dataType: 'Json',
                    data: {
                        vehicleid: value,
                        id: id
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
            this.formDisplayToggle('div_dialog', true);
        },
        //删除
        deleteData: function (value, id) {
            console.log("vehicle_id = " + value);
            console.log("name = " + name);
            var that = this;
            if (value) {
                $.messager.confirm('注意', '此操作不可恢复是否确定？', function (r) {
                    if (r) {
                        $.messager.confirm(that.lang.prompt, that.lang.sureDelete, function (r) {
                            if (r) {
                                $.ajax({
                                    url: '/CourtGms/index.php?m=Service&c=vehicleMaintenanceInfo&a=Delete',
                                    type: 'Post',
                                    dataType: 'Json',
                                    data: {
                                        VehicleId: value,
                                        id: id
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
                url: "/CourtGms/index.php?m=Service&c=VehicleMaintenanceInfo&a=exportData",
                type: 'post',
                dataType: 'json',
                data: data,
                success: function (data) {
                    that.formDisplayToggle("div_dialog6", false);
                    if (data.Code == 200) {
                        //console.log(data.Result);
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

            $('#search_dialog').window('refresh', maintenance_search_url);
            $('#search_dialog').window({
                title: "搜索信息",
                width: 380,
                height: 230,
                top: (screen.height - 750) / 2,
                left: (screen.width - 700) / 2,
                resizable: false,
                collapsible: false,
                minimizable: false,
                maximizable: false
            });
            $('#search_dialog').window('open');


            var that = this;


            that.formDisplayToggle('search_dialog', true);
        },

        resetQuery: function () {

            $('#grid').datagrid({queryParams: {}});
        },


        SearchSure: function () {

            var queryParams = $('#grid').datagrid('options').queryParams;
            $.each($('#seach_form').serializeArray(), function () {
                queryParams[this['name']] = this['value'];
            });

            startTime = new Date(queryParams['startTime'].replace("-", "/").replace("-", "/"));
            console.log(startTime);
            endTime = new Date(queryParams['endTime'].replace("-", "/").replace("-", "/"));
            console.log(endTime);

            if (endTime < startTime) {

                $.messager.alert('提示', "开始时间不能晚于结束时间", 'info');
            }
            else {
                $('#grid').datagrid('reload');
                $('#search_dialog').window('close');
            }

        },


        //操作
        operate: function (value, row, index) {
            console.log(row);
            // return "<a href='javascript:;' onclick='window.vehicleMaintenanceInfo.edit(\"" + row.vehicle_id + "\")'>" + window.vehicleMaintenanceInfo.lang.edit + "</a>&nbsp;|&nbsp;<a href='javascript:;' onclick='window.vehicleMaintenanceInfo.deleteData(\"" + row.vehicle_id + "\",\"" + row.maintenance_time + "\")'>" + window.vehicleMaintenanceInfo.lang.delete1 + "</a>";
            var html = "<div style='height: 30px;padding-top: 10px'>";

            if (result_edit) {
                html = html + "<a href='javascript:;' style='text-decoration: none;' onclick='window.vehicleMaintenanceInfo.edit(\"" + row.vehicle_id + "\",\"" + row.id + "\")'>" +
                    "<img style='border:none;width: 36px;' src='/CourtGms/Public/Static/Easyui/themes/icons/edit_add2.png'></a>&nbsp;"
            }
            if (result_del) {
                html = html + "&nbsp;<a href='javascript:;' style='text-decoration: none;' onclick='window.vehicleMaintenanceInfo.deleteData(\"" + row.vehicle_id + "\",\"" + row.id + "\")'>" +
                    "<img style='border:none;width: 36px;' src='/CourtGms/Public/Static/Easyui/themes/icons/delete1.png'></a>&nbsp;"

            }
            // console.log("aaaaa"+result_edit);
            return html + "</div>";
        },
        //确定操作
        operateSure: function () {
            var action = this.operateFlag == "add" ? "Create" : "Update";
            var that = this;
            $("#form_vehicle_maintenance").form('submit', {
                url: "/CourtGms/index.php?m=Service&c=vehicleMaintenanceInfo&a=" + action,
                onSubmit: function () {
                    var flag = true;
                    flag = flag && $("#inp_vehicle_id").textbox('isValid');

                    flag = flag && $("#inp_maintenance_time").datetimebox('isValid');

                    flag = flag && $("#inp_maintenance_identify_state").textbox('isValid');

                    flag = flag && $("#inp_maintenance_state").combotree('isValid');

                    flag = flag && $("#inp_maintenance_cost").numberbox('isValid');


                    if (!flag) {
                        $.messager.alert(that.lang.prompt, "数据填写不完整或存在错误,请检查后重试", 'info');
                        return flag;
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
                    } else {
                        $.messager.alert(that.lang.prompt, '错误原因:'+jsonResult.Msg, 'error');
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
        },
        errorMessage: function (code) {
            var that = this;
            switch (code) {
                case 201:
                    $.messager.alert(that.lang.prompt, that.lang.serverError, 'error');
                    break;
                case 202:
                    $.messager.alert(that.lang.prompt, that.lang.serverError, 'error');
                    break;
                case 203:
                    $.messager.alert(that.lang.prompt, that.lang.noPower, 'error');
                    break;
                case 204:
                    $.messager.alert(that.lang.prompt, that.lang.loginError, 'error');
                    break;
                case 206:
                    $.messager.alert(that.lang.prompt, that.lang.dataTimeout, 'info');
                    break;
                case 207:
                    $.messager.alert(that.lang.prompt, that.lang.overPower, 'info');
                    break;
                default:
                    break;
            }
        },

        //填充表单
        fillForm: function (obj) {
            if (obj) {
                $("#inp_id").val(obj.id);
                $("#inp_vehicle_id").combobox('setValue', obj.vehicle_id);
                $("#inp_maintenance_time").datetimebox('setValue', obj.maintenance_time);
                $("#inp_maintenance_reason").textbox('setValue', obj.maintenance_reason);
                $("#inp_maintenance_identify_state").combobox('setValue', obj.maintenance_identify_state);
                $("#inp_maintenance_cost").textbox('setValue', obj.maintenance_cost);
                $("#inp_maintenance_state").combobox('setValue', obj.maintenance_state);
                $("#inp_maintenance_remark").textbox('setValue', obj.maintenance_remark);
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
        vehicleMaintenanceInfo.init();
        vehicleMaintenanceInfo.extend();
        if (window.parent.guide) {
            window.parent.guide.removeLoading();
        }
    });
    return window.vehicleMaintenanceInfo = window.vehicleMaintenanceInfo || vehicleMaintenanceInfo;
})($, window);

