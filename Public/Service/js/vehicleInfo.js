/**
 * Created by li on 16/7/20.
 */
(function ($, window) {
    var vehicleInfo = {
        lang: {},
        operateFlag: undefined,
        vehicle_type: [],
        vehicle_from: [],
        device_name: [],
        edit_device_name: undefined,
        isExistCarLicense: undefined,
        isExistDeviceName: undefined,
        uploadCarImgDir: '/TobaccoGms1/Uploads/', // 定义上传图片目录
        groupId: '',
        //初始化
        init: function () {
            var that = this;
            $("#inp_vehicle_type").combobox({
                url: vehicle_type_url,
                editable: false,
                valueField: 'word_id',
                textField: 'word',
                panelHeight: 100,
                onLoadSuccess: function () {
                    var data = $("#inp_vehicle_type").combobox('getData');
                    that.vehicle_type = data;
                }
            });

            $("#inp_vehicle_from").combobox({
                url: vehicle_from_url,
                editable: false,
                valueField: 'word_id',
                textField: 'word',
                panelHeight: 'auto',
                onLoadSuccess: function () {
                    var data = $("#inp_vehicle_from").combobox('getData');
                    that.vehicle_from = data;
                },
                onSelect: function (record) {

                    if ("2" == record.word_id) {
                        var div = '<label>设备名称：</label>' +
                            '<input id="inp_device_name1" name="device_name" style="width: 160px;height: 30px"/>' +
                            '<span style="color: #CC0000;margin-left: 5px;line-height: inherit;text-align:center;font-weight: bold;">*</span>';
                        $("#test1").html(div);
                        $('#inp_device_name1').combobox({
                            url: vehicle_fromK_device_url,
                            required: true,
                            editable: true,
                            valueField: 'word',
                            textField: 'word',
                            panelHeight: '100',
                            onLoadSuccess: function () {
                                var data = $("#inp_device_name1").combobox('getData');
                                that.device_name = data;
                            }
                        })
                    }
                    else if ("3" == record.word_id) {
                        var div = '<label style="text-decoration: line-through">设备名称：</label>' +
                            '<input id="inp_device_name1" name="device_name" style="width: 160px;height: 30px"/>';
                        $("#test1").html(div);
                        $("#inp_device_name1").textbox({
                            disabled: true
                        });
                    }
                    else {
                        var div = '<label >设备名称：</label>' +
                            '<input id="inp_device_name1" name="device_name" style="width: 160px;height: 30px"/>' +
                            '<span style="color: #CC0000;margin-left: 5px;line-height: inherit;text-align:center;font-weight: bold;">*</span>';
                        $("#test1").html(div);
                        $("#inp_device_name1").textbox({
                            required: true,
                            validType: ['lettersChineseDigital[30]', 'isExistDevice'],
                            onChange: function (newValue) {
                                var that = this;
                                if ($.trim(newValue).length > 0) {
                                    if (window.vehicleInfo.operateFlag == "edit") {
                                        var oldAttr = $(this).attr('old');
                                        if (newValue == oldAttr) {
                                            window.vehicleInfo.isExistDeviceName = false;
                                            $(that).textbox('isValid');
                                            return;
                                        }
                                    }
                                    window.vehicleInfo.isExistDevice($(that).attr('textboxname'), newValue, function () {
                                        $(that).textbox('isValid');
                                    });
                                }
                            }
                        });
                    }
                }
            });
            
            $("#grid").datagrid({
                url: '/TobaccoGms1/index.php?m=Service&c=VehicleInfo&a=getVehicleInfoToList',
                toolbar: '#div_toolbar',
                nowrap: false,
                remoteSort: false,
                border: false,
                pagination: true,
                pageSize: '20',
                pageNumber: 1,
                pageList: [10, 20, 50],
                sortName: 'vehicle_id',
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

            $("#inp_vehicle_license").textbox({
                required: true,
                validType: ['lettersChineseDigital[30]', 'isExistLicense'],
                onChange: function (newValue) {
                    var that = this;
                    if ($.trim(newValue).length > 0) {
                        if (window.vehicleInfo.operateFlag == "edit") {
                            var oldAttr = $(this).attr('old');
                            if (newValue == oldAttr) {
                                window.vehicleInfo.isExistCarLicense = false;
                                $(that).textbox('isValid');
                                return;
                            }
                        }
                        window.vehicleInfo.isExistLicense($(that).attr('textboxname'), newValue, function () {
                            $(that).textbox('isValid');
                        });
                    }
                }
            });
        },

        formatterVehicleType: function (value, row, index) {
            var result = '';
            $.each(window.vehicleInfo.vehicle_type, function (index, val) {
                if (val.word_id == value.toString()) {
                    result = val.word;
                }
            });
            return result;
        },
        formatterVehicleFrom: function (value, row, index) {
            var result = '';
            $.each(window.vehicleInfo.vehicle_from, function (index, val) {
                if (val.word_id == value.toString()) {
                    result = val.word;
                }
            });
            return result;
        },
        formatterUseFlag: function (value, row, index) {
            if (value == 1) {
                return window.vehicleInfo.lang.yes;
            } else {
                return window.vehicleInfo.lang.no;
            }
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
                },
                //检测重复
                isExistLicense: {
                    validator: function (value, param) {
                        if (window.vehicleInfo.isExistCarLicense == undefined) {
                            return true;
                        } else {
                            return !window.vehicleInfo.isExistCarLicense;
                        }
                    },
                    message: that.lang.exist
                },
                //检测重复
                isExistDevice: {
                    validator: function (value, param) {
                        if (window.vehicleInfo.isExistDeviceName == undefined) {
                            return true;
                        } else {
                            return !window.vehicleInfo.isExistDeviceName;
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
        isExistLicense: function (type, value, callback) {
            var getExisturl = "/TobaccoGms1/index.php?m=Service&c=VehicleInfo&a=getExistCarLicense";
            $.post(getExisturl, {
                    columnName: type,
                    Value: value
                },
                function (data) {
                    if (data.Code == 200) {
                        window.vehicleInfo.isExistCarLicense = data.Result;
                        callback();
                    }
                });
        },
        //检测是否存在
        isExistDevice: function (type, value, callback) {
            var getExisturl = "/TobaccoGms1/index.php?m=Service&c=VehicleInfo&a=getExistDeviceName";
            $.post(getExisturl, {
                    columnName: type,
                    Value: value
                },
                function (data) {
                    if (data.Code == 200) {
                        window.vehicleInfo.isExistDeviceName = data.Result;
                        callback();
                    }
                });
        },

        fileSelected: function () {
            var fileinfo = document.getElementById('inp_vehicle_photo').files[0];
            var url = window.URL.createObjectURL(fileinfo);
            $('#img_photo').attr('src', url);
        },
        //添加
        add: function () {
            $("#form_vehicle").form('clear');
            this.operateFlag = 'add';
            $("#inp_group_id").combotree({
                url: ul_tree_url,
                required: true,
                editable: false,
                panelHeight: 180,
                disabled: false
            });

            $('#inp_vehicle_from').combobox('select', "1");


            var that = this;
            $("#div_ftitle").text(this.lang.add);

            that.isExistCarLicense = undefined;
            var data = $("#inp_vehicle_type").combobox('getData');
            $("#inp_vehicle_type").combobox('setValue', data[0].word_id);
            var data = $("#inp_vehicle_from").combobox('getData');
            $("#inp_vehicle_from").combobox('setValue', data[0].word_id);
            $("#inp_yes").click();
            var dateObj = new Date();
            var todayDate = dateObj.getFullYear() + "-" + (dateObj.getMonth() + 1) + "-" + dateObj.getDate();
            $("#inp_vehicle_buy_date").datebox('setValue', todayDate);

            $('#img_photo').attr('src', "/TobaccoGms1/Public/Service/img/profile.png");

            that.formDisplayToggle('div_dialog', true);
        },
        //编辑
        edit: function (value) {
            $("#form_vehicle").form('clear');
            this.operateFlag = 'edit';
            $("#inp_group_id").combotree({
                url: ul_tree_url,
                required: true,
                editable: false,
                panelHeight: 180,
                disabled: true
            });
            $('#img_photo').attr('src', "/TobaccoGms1/Public/Service/img/profile.png");
            var that = this;
            $("#div_ftitle").text(this.lang.edit);
            if (value) {
                $.ajax({
                    url: '/TobaccoGms1/index.php?m=Service&c=VehicleInfo&a=getVehicleInfoByVehicleId',
                    type: 'Post',
                    dataType: 'Json',
                    data: {
                        vehicleid: value
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
                $("#inp_vehicle_license").focus();
            }, 500);
            that.formDisplayToggle('div_dialog', true);
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
                                    url: '/TobaccoGms1/index.php?m=Service&c=VehicleInfo&a=Delete',
                                    type: 'Post',
                                    dataType: 'Json',
                                    data: {
                                        VehicleId: value,
                                        CarLicense: name
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
        //预览图片
        showView: function (value) {
            console.log(value);
            var that = this;
            if (value) {
                $.ajax({
                    url: '/TobaccoGms1/index.php?m=Service&c=VehicleInfo&a=getVehicleInfoByVehicleId',
                    type: 'Post',
                    dataType: 'Json',
                    data: {
                        vehicleid: value
                    }
                })
                    .done(function (result) {
                        if (result.Code == 200) {
                            that.fillFormForShow(result.Result);
                            console.log('showView', result.Result);

                        } else {
                            that.errorMessage(result.Code);
                        }
                    });
            }
            that.formDisplayToggle('div_dialog4', true);
        },
        exportData: function () {
            var data = $('#grid').datagrid('getData');
            var that = this;
            that.formDisplayToggle("div_dialog6", true);
            $.ajax({
                url: "/TobaccoGms1/index.php?m=Service&c=VehicleInfo&a=exportData",
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


            $('#search_dialog').window('refresh', vehicle_search_url);
            $('#search_dialog').window({
                title: "搜索信息",
                width: 400,
                height: 'auto',
                top: (screen.height - 750) / 2,
                left: (screen.width - 700) / 2,
                resizable: false,
                collapsible: false,
                minimizable: false,
                maximizable: false
            });
            $('#search_dialog').window('open');


            //
            // var that = this;
            //
            //
            //
            // that.formDisplayToggle('search_dialog',true);


        },

        SearchSure: function () {

            var queryParams = $('#grid').datagrid('options').queryParams;
            $.each($('#seach_form').serializeArray(), function () {
                queryParams[this['name']] = this['value'];
            });

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
        //重置
        resetQuery: function () {

            $('#grid').datagrid({queryParams: {}});

        },

        //操作
        operate: function (value, row, index) {
            // return "<a href='javascript:;' onclick='window.vehicleInfo.edit(\"" + row.vehicle_id + "\")'>" + window.vehicleInfo.lang.edit + "</a>&nbsp;|&nbsp;<a href='javascript:;' onclick='window.vehicleInfo.deleteData(\"" + row.vehicle_id + "\",\"" + row.vehicle_license + "\")'>" + window.vehicleInfo.lang.delete1 + "</a>";
            var html = "<div id='operate' style='height: 30px;padding-top: 10px'>";

            html = html + "<a href='javascript:;' style='text-decoration: none;' onclick='window.vehicleInfo.showView(\"" + row.vehicle_id + "\")'>" +
                "<img style='line-height: 30px;border:none;width: 36px;vertical-align: bottom' src='/TobaccoGms1/Public/Static/Easyui/themes/icons/detail.png'></a>&nbsp;"


            if (result_edit) {
                html = html + "<a href='javascript:;' style='text-decoration: none;' onclick='window.vehicleInfo.edit(\"" + row.vehicle_id + "\")'>" +
                    "<img style='line-height: 30px;border:none;width: 36px;vertical-align: bottom' src='/TobaccoGms1/Public/Static/Easyui/themes/icons/edit_add2.png'></a>&nbsp;"
            }
            if (result_del) {
                html = html + "<a href='javascript:;' style='text-decoration: none;' onclick='window.vehicleInfo.deleteData(\"" + row.vehicle_id + "\",\"" + row.vehicle_license + "\")'>" +
                    "<img style='line-height: 30px;border:none;width: 36px;vertical-align: bottom' src='/TobaccoGms1/Public/Static/Easyui/themes/icons/delete1.png'></a>&nbsp;"

            }
            // console.log("aaaaa"+result_edit);
            return html + "</div>";
        },
        //确定操作
        operateSure: function () {
            var action = this.operateFlag == "add" ? "Create" : "Update";
            var that = this;
            $("#form_vehicle").form('submit', {
                url: "/TobaccoGms1/index.php?m=Service&c=VehicleInfo&a=" + action,
                onSubmit: function () {
                    var flag = true;
                    flag = flag && $("#inp_vehicle_license").textbox('isValid');


                    flag = flag && $("#inp_group_id").combotree('isValid');

                    flag = flag && $("#inp_vehicle_buy_date").combotree('isValid');

                    flag = flag && $("#inp_device_name1").combotree('isValid');


                    if (!flag) {
                        $.messager.alert(that.lang.prompt, "数据填写不完整或存在错误,请检查后重试", 'info');
                        return flag;
                    }


                    var select = $('#inp_vehicle_from').combobox('getValue');


                    if ("2" == select.toString()) {

                        flag = false;

                        var name = $('#inp_device_name1').combobox('getValue');


                        if ('Update' == action) {


                            if (that.edit_device_name != '' && that.edit_device_name != undefined) {

                                console.log(that.device_name);
                                var temps = new Object();
                                temps['word'] = that.edit_device_name;
                                that.device_name.push(temps);

                            }


                        }

                        $.each(that.device_name, function (index, val) {

                            console.log(val.word);
                            if (val.word == name.toString()) {

                                console.log(index);

                                that.device_name.pop();

                                flag = true;
                            }
                        });

                        if (!flag) {
                            $.messager.alert(that.lang.prompt, "请选择正确的设备名称!", 'info');
                            return flag;
                        }
                    }
                },
                success: function (data) {
                    var jsonResult = $.parseJSON(data);

                    if (jsonResult.Result) {
                        $("#grid").datagrid('reload');

                        that.formDisplayToggle('div_dialog', false);

                        $.messager.show({
                            title: '操作完成',
                            msg: jsonResult.Msg,
                            timeout: 3000,
                            showType: 'slide'
                        });

                    }
                    else {
                        $.messager.alert(that.lang.prompt, "错误原因:" + jsonResult.Msg, 'info');
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

        close: function () {
            var that = this;
            that.formDisplayToggle('div_dialog_changeGroup', false);
        },
        //确定转组操作
        save: function () {
            var that = this;
            var t = $('#change_cars').combotree('tree');
            var nodes = t.tree('getChecked');

            //      逗号分割 车id
            var vehicleIdsString = "";

            for (var i = 0; i < nodes.length; i++) {
                var node = nodes[i];
                if (node.attributes.type == 'car') {
                    if (vehicleIdsString == '') {
                        vehicleIdsString += node.attributes.carinfo.vehicle_id;
                    } else {
                        vehicleIdsString += ',' + node.attributes.carinfo.vehicle_id;
                    }
                }
            }

            if (vehicleIdsString == '') {
                showTipsMessage('请至少选取一辆车！');
                return;
            }
            console.log(vehicleIdsString);
            //        目标组id
            var node1 = $('#change_group').combotree('getValue');


            if (node1 == '') {
                showTipsMessage('请至少选取一个单位');
                return;
            }


            console.log(node1);
            //        获取选中控件的组id
            var groupId = node1;

            //        这里换乘转组的api，ajax请求

            $.ajax({
                url: "/TobaccoGms1/index.php?m=Service&c=VehicleInfo&a=UpdateGroupId",
                type: 'Post',
                dataType: 'Json',
                data: {
                    "vehicle_id": vehicleIdsString,
                    group_id: groupId
                },
                success: function (result) {
                    console.log(result);
                    if (result.Msg) {
                        showTipsMessage(result.Msg);

                        $("#grid").datagrid('reload');
                    }
                    that.formDisplayToggle('div_dialog_changeGroup', false);
                },
                error: function () {

                    that.formDisplayToggle('div_dialog_changeGroup', false);
                }
            });
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
            console.log(obj);
            var that = this;
            if (obj) {
                $("#inp_vehicleid").val(obj.vehicle_id);
                $("#inp_vehicle_license").attr('old', obj.vehicle_license).textbox('setValue', obj.vehicle_license);
                var tree = $("#inp_group_id").combotree('tree');
                var node = tree.tree('find', obj.group_id);
                if (node) {
                    $("#inp_group_id").combotree('setValue', obj.group_id);
                } else {
                    $("#inp_group_id").combotree('clear');
                }
                $("#inp_vehicle_from").combobox('select', obj.vehicle_from);

                // $("#inp_device_name1").val(obj.device_name);
                if (obj.vehicle_from == 2) {

                    that.edit_device_name = obj.device_name;
                    $('#inp_device_name1').combobox('setValue', obj.device_name);
                }
                else {
                    $("#inp_device_name1").textbox('setValue', obj.device_name);
                }
                $("#inp_vehicle_type").combobox('setValue', obj.vehicle_type);
                $("#inp_vehicle_brand").textbox('setValue', obj.vehicle_brand);
                $("#inp_vehicle_model").textbox('setValue', obj.vehicle_model);
                $("#inp_vehicle_driver").textbox('setValue', obj.vehicle_driver);
                $("#inp_vehicle_buy_price").numberbox('setValue', obj.vehicle_buy_price);
                $("#inp_vehicle_buy_date").datebox('setValue', obj.vehicle_buy_date);
                $("#inp_vehicle_explain").textbox('setValue', obj.vehicle_explain);
                if (obj.vehicle_state == 1) {
                    $("#inp_yes").click();
                } else {
                    $("#inp_no").click();
                }
                if (obj.vehicle_photo != 'null' && obj.vehicle_photo != null && obj.vehicle_photo.length > 0) {
                    $('#img_photo').attr('src', that.uploadCarImgDir + obj.vehicle_photo);
                }
                else {
                    $('#img_photo').attr('src', "/TobaccoGms1/Public/Service/img/profile.png");
                }
                $("#inp_vehicle_place").textbox('setValue', obj.vehicle_place);
            }
        },
        //填充表单
        fillFormForShow: function (obj) {
            console.log(obj);
            var that = this;
            if (obj) {
                $("#inp_vehicleid_view").val(obj.vehicle_id);
                $("#inp_vehicle_license_view").attr('old', obj.vehicle_license).textbox('setValue', obj.vehicle_license);


                //$("#inp_vehicle_type_view").combobox('setValue', obj.vehicle_type);
                $("#inp_vehicle_brand_view").textbox('setValue', obj.vehicle_brand);
                $("#inp_vehicle_model_view").textbox('setValue', obj.vehicle_model);
                $("#inp_vehicle_driver_view").textbox('setValue', obj.vehicle_driver);
                $("#inp_vehicle_buy_price_view").numberbox('setValue', obj.vehicle_buy_price);
                $("#inp_vehicle_explain_view").textbox('setValue', obj.vehicle_explain);
                // console.log('vehicle_buy_date',obj.vehicle_buy_date);

                $("#inp_vehicle_buy_date_view").textbox('setValue', obj.vehicle_buy_date);

                //if(obj.vehicle_state == 1){
                //    $("#inp_vehicle_state_view").textbox('setValue','启用');
                //}else{
                //    $("#inp_vehicle_state_view").textbox('setValue','已停用');
                //}
                if (obj.vehicle_photo != 'null' && obj.vehicle_photo != null && obj.vehicle_photo.length > 0) {
                    $('#img_photo_view').attr('src', that.uploadCarImgDir + obj.vehicle_photo);
                }
                else {
                    $('#img_photo_view').attr('src', "/TobaccoGms1/Public/Service/img/profile.png");
                }
                //$("#inp_vehicle_place_view").textbox('setValue',obj.vehicle_place);
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
        vehicleInfo.init();
        vehicleInfo.extend();
        if (window.parent.guide) {
            window.parent.guide.removeLoading();
        }
    });
    return window.vehicleInfo = window.vehicleInfo || vehicleInfo;
})($, window);
