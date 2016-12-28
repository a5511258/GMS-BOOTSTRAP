/**
 * Created by l on 2016年09月22日18:42:07
 */
(function ($, window) {
    var vehicleViolationInfo = {
        lang: {
            prompt: "提示",
            edit: "编辑违规信息",
            delete1: "删除",
            sureDelete: "确认删除这条内容?",
            leastOne: "请至少选择一项!",
            operateFailure: "操作失败,请重试!"
        },
        operateFlag: undefined,
        //初始化
        init: function () {
            var that = this;
            $("#grid").datagrid({
                url: vehicle_violation_info_url,
                toolbar: '#div_toolbar',
                nowrap: false,
                remoteSort: false,
                border: false,
                pagination: true,
                pageSize: '20',
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
            $("#inp_vehicle_id").combobox({
                url: vehicle_license_url,
                required: true,
                editable: false,
                valueField: 'word_id',
                textField: 'word',
                panelHeight: 100
            });

            $("#inp_vehicle_id_search").combobox({
                url: vehicle_license_url,
                multiple: true,
                editable: false,
                valueField: 'word_id',
                textField: 'word',
                panelHeight: 150
            });
            var dateObj = new Date();
            var endTime = dateObj.getFullYear() + "-" + (dateObj.getMonth() + 1) + "-" + (dateObj.getDate());
            var MinMilli = 1000 * 60;
            var HrMilli = MinMilli * 60;
            var DyMilli = HrMilli * 24;
            var nowMilli = dateObj.getTime();
            dateObj.setTime(nowMilli - DyMilli * 1);//推后一天
            var startTime = dateObj.getFullYear() + "-" + (dateObj.getMonth() + 1) + "-" + (dateObj.getDate());
            $("#inp_vehicle_violation_start_date").datebox('setValue', startTime);
            $("#inp_vehicle_violation_end_date").datebox('setValue', endTime);
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
            $("#form_vehicle_violation").form('clear');
            $("#inp_vehicle_id").combobox({
                readonly: false
            });
            $("#inp_violation_time").datetimebox({
                readonly: false
            });
            this.operateFlag = 'add';
            var that = this;
            $("#div_ftitle").text("添加");
            $("#inp_vehicle_id").combobox('clear');
            var dateObj = new Date();
            var todayDate = dateObj.getFullYear() + "-" + (dateObj.getMonth() + 1) + "-" + dateObj.getDate() + " " + (dateObj.getHours()) + ":" + dateObj.getMinutes() + ":" + (dateObj.getSeconds());
            $("#inp_violation_time").datetimebox('setValue', todayDate);
            that.formDisplayToggle('div_dialog', true);
        },
        //编辑
        edit: function (value, id) {


            $("#div_ftitle").text("编辑");

            console.log(value);

            console.log(id);

            $("#form_vehicle").form('clear');
            $("#inp_vehicle_id").combobox({
                readonly: true
            });
            $("#inp_violation_time").datetimebox({
                readonly: false
            });
            this.operateFlag = 'edit';
            var that = this;
            $("#inp_vehicle_id").combobox('clear');
            $("#inp_violation_points").numberspinner('setValue', 0);
            $("#inp_violation_name").textbox('clear');
            $("#inp_violation_cost").numberbox('clear');
            $("#inp_violation_location").textbox('clear');
            $("#inp_violation_time").datetimebox('clear');
            if (value) {
                $.ajax({
                    url: '/CourtGms/index.php?m=Service&c=vehicleViolationInfo&a=getVehicleViolationInfoByVehicleId',
                    type: 'Post',
                    dataType: 'Json',
                    data: {
                        vehicleId: value,
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
        deleteData: function (value, name) {
            console.log("vehicle_id = " + value);
            console.log("name = " + name);
            var that = this;
            if (value) {
                $.messager.confirm('注意', '此操作不可恢复是否确定？', function (r) {
                    if (r) {
                        $.messager.confirm(that.lang.prompt, that.lang.sureDelete, function (r) {
                            if (r) {
                                $.ajax({
                                    url: '/CourtGms/index.php?m=Service&c=vehicleViolationInfo&a=Delete',
                                    type: 'Post',
                                    dataType: 'Json',
                                    data: {
                                        VehicleId: value,
                                        id: name
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
                url: "/CourtGms/index.php?m=Service&c=VehicleViolationInfo&a=exportData",
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


        //操作
        operate: function (value, row, index) {
            console.log(row);
            // return "<a href='javascript:;' onclick='window.vehicleViolationInfo.edit(\"" + row.vehicle_id + "\",\"" + row.violation_time + "\")'>" + window.vehicleViolationInfo.lang.edit + "</a>&nbsp;|&nbsp;<a href='javascript:;' onclick='window.vehicleViolationInfo.deleteData(\"" + row.vehicle_id + "\",\"" + row.violation_time + "\")'>" + window.vehicleViolationInfo.lang.delete1 + "</a>";
            var html = "<div style='height: 30px;padding-top: 10px'>";

            if (result_edit) {
                html = html + "<a href='javascript:;' style='text-decoration: none;' onclick='window.vehicleViolationInfo.edit(\"" + row.vehicle_id + "\",\"" + row.id + "\")'>" +
                    "<img style='border:none;width: 36px;' src='/CourtGms/Public/Static/Easyui/themes/icons/edit_add2.png'></a>&nbsp;"
            }
            if (result_del) {
                html = html + "&nbsp;<a href='javascript:;' style='text-decoration: none;' onclick='window.vehicleViolationInfo.deleteData(\"" + row.vehicle_id + "\",\"" + row.id + "\")'>" +
                    "<img style='border:none;width: 36px;' src='/CourtGms/Public/Static/Easyui/themes/icons/delete1.png'></a>&nbsp;"

            }
            // console.log("aaaaa"+result_edit);
            return html + "</div>";
        },
        //确定操作
        operateSure: function () {
            var action = this.operateFlag == "add" ? "Create" : "Update";
            var that = this;
            $("#form_vehicle_violation").form('submit', {
                url: "/CourtGms/index.php?m=Service&c=vehicleViolationInfo&a=" + action,
                onSubmit: function () {
                    var flag = true;
                    flag = flag && $("#inp_vehicle_id").textbox('isValid');

                    flag = flag && $("#inp_violation_time").datetimebox('isValid');

                    flag = flag && $("#inp_violation_points").numberbox('isValid');

                    flag = flag && $("#inp_violation_cost").numberspinner('isValid');

                    if ($("#inp_violation_points").numberspinner('getValue') < 0 || $("#inp_violation_points").numberspinner('getValue') > 12) {
                        $.messager.alert(that.lang.prompt, "请正确填写扣分情况", 'info');
                        return false;
                    }

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
                            title:'操作结果',
                            msg:jsonResult.Msg,
                            timeout:3000,
                            showType:'slide'
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
                $("#inp_vehicle_id").combobox('setValue', obj.vehicle_id);
                $("#inp_violation_time").datetimebox('setValue', obj.violation_time);
                $("#inp_violation_points").numberspinner('setValue', obj.violation_points);
                $("#inp_violation_name").textbox('setValue', obj.violation_name);
                $("#inp_violation_cost").numberbox('setValue', obj.violation_cost);
                $("#inp_violation_location").textbox('setValue', obj.violation_location);
                $("#inp_violation_remark").textbox('setValue', obj.violation_remark);
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
        vehicleViolationInfo.init();
        vehicleViolationInfo.extend();
        if (window.parent.guide) {
            window.parent.guide.removeLoading();
        }
    });
    return window.vehicleViolationInfo = window.vehicleViolationInfo || vehicleViolationInfo;
})($, window);

