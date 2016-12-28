/**
 * Created by 李 on 2016/9/24 0024.
 */
(function ($, window) {
    var vehicleInsuranceInfo = {
        lang: {
            prompt: "提示",
            edit: "编辑",
            delete1: "删除",
            sureDelete: "确认删除这条内容?",
            leastOne: "请至少选择一项!",
            operateFailure: "操作失败,请重试!"
        },
        isExistPolicyNumber: undefined,
        //insurance_typeArray:[],
        operateFlag: undefined,
        init: function () {
            var that = this;
            var type = undefined;


            $("#grid").datagrid({
                url: '/CourtGms/index.php?m=Service&c=vehicleInsuranceInfo&a=getInsuranceToList',
                toolbar: '#div_toolbar',
                nowrap: false,
                remoteSort: false,
                border: false,
                pagination: true,
                pageSize: '20',
                pageNumber: 1,
                pageList: [10, 20, 50],
                sortName: 'insurance_start_date',
                sortOrder: 'asc',
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
                editable: false,
                required: true,
                valueField: 'word_id',
                textField: 'word',
                panelHeight: 100
            });
            $("#inp_insurance_type").combobox({
                url: insurance_url,
                editable: false,
                valueField: 'word_id',
                textField: 'word',
                panelHeight: "auto"
            });


            $('inp_policy_number').textbox({
                required: true,
                validType: ['isExist'],
                onChange: function (newValue) {
                    var that = this;
                    if ($.trim(newValue).length > 0) {
                        window.vehicleInsuranceInfo.isExist($(that).attr('textboxname'), newValue, function () {
                            $(that).textbox('isValid');
                        });
                    }
                }
            });
        },
        extend: function () {
            var that = this;
            //扩展jquery easy ui检测控件validatebox的检测规则。
            $.extend($.fn.textbox.defaults.rules, {
                //检测重复
                isExist: {
                    validator: function (value, param) {
                        if (window.vehicleInsuranceInfo.isExistPolicyNumber == undefined) {
                            return true;
                        } else {
                            return !window.vehicleInsuranceInfo.isExistPolicyNumber;
                        }
                    },
                    message: that.lang.exist
                }
            });
            //回收jquery ajax请求对象
            $(document).ajaxComplete(function (event, request, settings) {
                request = null;
                delete request;
            });
        },
        //检测是否存在
        isExist: function (type, value, callback) {
            var getExisturl = "/CourtGms/index.php?m=Service&c=vehicleInsuranceInfo&a=getExistPolicyNumber";
            $.post(getExisturl, {
                    columnName: type,
                    Value: value
                },
                function (data) {
                    if (data.Code == 200) {
                        window.vehicleInsuranceInfo.isExistPolicyNumber = data.Result;
                        callback();
                    }
                });
        },
        formatterstates: function (value, row, index) {
            if (value == "近期到期") {
                return 'color:red;';
            }
            else if (value == "已过期") {
                return ' color:#f0c040;';
            }
            else {
                return 'color:#B3EE3A;';
            }
        },
        formatterInsuranceType: function (value, row, index) {
            var result = '';
            if (value.toString() == "1") {
                result = "交强险";
            }
            else {
                result = "商业险";
            }
            return result;
        },
        //添加保险信息
        add: function (value, name) {

            $('#div_ftitle').text("添加");
            $("#form_vehicle_insurance").form('clear');
            $("#inp_vehicle_id").combobox({
                readonly: false
            });
            $("#inp_policy_number").textbox({
                readonly: false
            });
            $("#inp_insurance_type").combobox('setValue', 1);

            var dateObj = new Date();
            var nowDay = dateObj.getFullYear() + "-" + (dateObj.getMonth() + 1) + "-" + (dateObj.getDate());
            var nextDay = (dateObj.getFullYear() + 1) + "-" + (dateObj.getMonth() + 1) + "-" + (dateObj.getDate());
            $("#inp_insurance_start_date").datebox('setValue', nowDay);
            $("#inp_insurance_end_date").datebox('setValue', nextDay);
            var that = this;
            that.operateFlag = "Create";
            this.formDisplayToggle('div_dialog', true);

        },
        //修改保险信息
        edit: function (value, name) {

            $('#div_ftitle').text("编辑");
            $("#form_vehicle_insurance").form('clear');
            $("#inp_vehicle_id").combobox({
                readonly: true
            });
            $("#inp_policy_number").textbox({
                readonly: true
            });
            var that = this;
            that.operateFlag = "Update";
            if (value && name) {
                $.ajax({
                    url: '/CourtGms/index.php?m=Service&c=vehicleInsuranceInfo&a=getInsuranceInfoByVehicleId',
                    type: 'Post',
                    dataType: 'Json',
                    data: {
                        vehicleid: value,
                        policy_number: name
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
            this.formDisplayToggle('div_dialog', true);

        },
        //删除
        deleteData: function (value, name) {
            var that = this;
            if (value) {
                $.messager.confirm('注意', '此操作不可恢复是否确定？', function (r) {
                    if (r) {
                        $.messager.confirm(that.lang.prompt, that.lang.sureDelete, function (r) {
                            if (r) {
                                $.ajax({
                                    url: '/CourtGms/index.php?m=Service&c=VehicleInsuranceInfo&a=Delete',
                                    type: 'Post',
                                    dataType: 'Json',
                                    data: {
                                        VehicleId: value,
                                        PolicyNumber: name
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
            that.formDisplayToggle("div_dialog1", true);
            $.ajax({
                url: "/CourtGms/index.php?m=Service&c=VehicleInsuranceInfo&a=exportData",
                type: 'post',
                dataType: 'json',
                data: data,
                success: function (data) {
                    that.formDisplayToggle("div_dialog1", false);
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
        query: function (value, name) {

            $('#search_dialog').window('refresh', insurance_search_url);
            $('#search_dialog').window({
                title: "搜索信息",
                width: 400,
                height: 420,
                top: (screen.height - 750) / 2,
                left: (screen.width - 700) / 2,
                resizable: false,
                collapsible: false,
                minimizable: false,
                maximizable: false
            });

            $('#search_dialog').window('open');

        },

        //重置
        resetQuery: function () {
            // $("#inp_search").searchbox('setValue', '');
            // $("#inp_search_insurance_state1").attr("checked",false);
            // $("#inp_search_insurance_state2").attr("checked",false);
            // $("#grid").datagrid({
            //     url: '/CourtGms/index.php?m=Service&c=vehicleInsuranceInfo&a=getInsuranceToList&columnName=first&type=-1'
            // });
            $('#grid').datagrid({
                queryParams: {
                    'limit': "1"
                }
            });
        },

        SearchSure: function () {

            var queryParams = $('#grid').datagrid('options').queryParams;
            $.each($('#seach_form').serializeArray(), function () {
                queryParams[this['name']] = this['value'];
            });

            var type = 3;

            if ($("#s_insurance_state1")[0].checked) {
                type = 1;
            }
            if ($("#s_insurance_state2")[0].checked) {
                type = 2;
            }
            if ($("#s_insurance_state3")[0].checked) {
                type = 3;
            }

            queryParams['type'] = type.toString();


            queryParams['limit'] = "1";

            console.log(queryParams);
            $('#grid').datagrid('reload');
        },


        //查询近一周保险到期数据
        expireSevenDayInsurenance: function () {

            var queryParams = $('#grid').datagrid('options').queryParams;

            queryParams['limit'] = "7";

            queryParams['type'] = "4";

            $('#grid').datagrid('reload');

        },
        //查询近一月保险到期数据
        expireOneMonthInsurenance: function () {

            var queryParams = $('#grid').datagrid('options').queryParams;

            queryParams['limit'] = "1";

            queryParams['type'] = "4";

            $('#grid').datagrid('reload');

        },
        //查询近三月保险到期数据
        expireThreeMonthInsurenance: function () {

            var queryParams = $('#grid').datagrid('options').queryParams;

            queryParams['limit'] = "3";

            queryParams['type'] = "4";

            $('#grid').datagrid('reload');

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
        //操作
        operate: function (value, row, index) {

            var html = "<div style='height: 30px;padding-top: 10px'>";
            if (result_edit) {
                html = html + "<a href='javascript:;' style='text-decoration: none;' onclick='window.vehicleInsuranceInfo.edit(\"" + row.vehicle_id + "\",\"" + row.policy_number + "\")'>" +
                    "<img style='border:none;width: 36px;' src='/CourtGms/Public/Static/Easyui/themes/icons/edit_add2.png'></a>&nbsp;";
            }
            if (result_del) {
                html = html + "&nbsp;<a href='javascript:;' style='text-decoration: none;' onclick='window.vehicleInsuranceInfo.deleteData(\"" + row.vehicle_id + "\",\"" + row.policy_number + "\")'>" +
                    "<img style='border:none;width: 36px;' src='/CourtGms/Public/Static/Easyui/themes/icons/delete1.png'></a>&nbsp;";

            }

            return html + "</div>";
            //return "<a href='javascript:;' onclick='window.vehicleInsuranceInfo.edit(\"" + row.vehicle_id + "\",\"" + row.policy_number + "\")'>修改保险信息</a>&nbsp;|&nbsp;<a href='javascript:;' onclick='window.vehicleInsuranceInfo.deleteData(\"" + row.vehicle_id + "\",\"" + row.policy_number + "\")'>删除</a>";
        },
        //确定操作
        operateSure: function () {
            var that = this;
            console.log(that.operateFlag);
            $("#form_vehicle_insurance").form('submit', {
                url: "/CourtGms/index.php?m=Service&c=vehicleInsuranceInfo&a=" + that.operateFlag,
                onSubmit: function () {
                    var startDate = $("#inp_insurance_start_date").datebox("getValue");

                    startDate = new Date(startDate.replace("-", "/").replace("-", "/"));

                    console.log(startDate);
                    var endDate = $("#inp_insurance_end_date").datebox("getValue");

                    endDate = new Date(endDate.replace("-", "/").replace("-", "/"));
                    console.log(endDate);
                    var flag = true;
                    flag = flag && $("#inp_vehicle_id").combobox('isValid');


                    flag = flag && $("#inp_policy_number").textbox('isValid');


                    flag = flag && $("#inp_insurance_amount").numberbox('isValid');


                    if (!flag) {
                        $.messager.alert(that.lang.prompt, "数据填写不完整或存在错误,请检查后重试", 'info');
                        return flag;
                    }


                    if (endDate <= startDate) {
                        $.messager.alert(that.lang.prompt, "保险开始时间不能早于保险结束时间", 'error');
                        return false;
                    }
                },
                success: function (data) {
                    if (data) {
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

                },
                error: function () {
                    $.messager.alert(that.lang.prompt, "数据错误", 'error');
                }
            });
        },
        //取消操作
        operateCancel: function () {
            this.operateFlag = undefined;
            this.formDisplayToggle('div_dialog', false);
            $("#grid").datagrid('unselectAll');
        },
        /**比较时间
         *
         * @param time1
         * @param time2
         * @returns {boolean}
         */
        compareTime: function (time1, time2) {

            var starttime = new Date(time1);
            var starttimes = starttime.getTime();

            var endtime = new Date(time2);
            var endtime = endtime.getTime();

            if (starttimes >= endtime) {
                return false;
            }
            else {
                return true;
            }
        },
        //错误信息
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
            var that = this;
            if (obj) {
                $("#inp_id").val(obj.id);
                $("#inp_vehicle_id").combobox('setValue', obj.vehicle_id);
                $("#inp_insurance_name").textbox('setValue', obj.insurance_name);
                $("#inp_policy_number").textbox('setValue', obj.policy_number);
                $("#inp_insurance_start_date").datebox('setValue', obj.insurance_start_date);
                $("#inp_insurance_end_date").datebox('setValue', obj.insurance_end_date);
                $("#inp_insurance_amount").numberbox('setValue', obj.insurance_amount);
                $("#inp_insurance_type").combobox('setValue', obj.insurance_type);
                $("#inp_insurance_remark").textbox('setValue', obj.insurance_remark);
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
        vehicleInsuranceInfo.init();
        //vehicleInsuranceInfo.extend();
        if (window.parent.guide) {
            window.parent.guide.removeLoading();
        }
    });
    return window.vehicleInsuranceInfo = window.vehicleInsuranceInfo || vehicleInsuranceInfo;
})($, window);
