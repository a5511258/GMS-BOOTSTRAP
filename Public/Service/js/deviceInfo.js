/**
 * Created by li on 16/7/15.
 */
(function ($, window) {
    var deviceInfo = {
        lang: {
            prompt: "提示",
            edit: "编辑",
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
                url: ul_list_url,
                toolbar: '#div_toolbar',
                nowrap: false,
                remoteSort: false,
                border: false,
                pagination: true,
                pageSize: '20',
                pageNumber: 1,
                pageList: [10, 20, 50],
                sortName: 'id',
                sortOrder: 'desc',
                rownumbers: true,
                singleSelect: true,
                autoRowHeight: false,
                fit: true,
                fitColumns: true,
                onLoadError: function () {
                    $.messager.alert(that.lang.prompt, "数据载入失败!", 'error');
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
        //添加
        add: function () {

            this.operateFlag = 'add';

            $('#div_dialog').window({
                width: 700,
                height: 360,
                resizable: false,
                collapsible: false,
                minimizable: false,
                maximizable: false
            });

            $('#div_dialog').window('open');

            $('#div_dialog').window('refresh', "/TobaccoGms1/index.php?m=Service&c=DeviceInfo&a=add_edit&type=add");


        },
        //编辑
        edit: function (value) {

            this.operateFlag = 'edit';

            $('#div_dialog').window({
                width: 700,
                height: 360,
                resizable: false,
                collapsible: false,
                minimizable: false,
                maximizable: false
            });

            $('#div_dialog').window('open');

            $('#div_dialog').window('refresh', "/TobaccoGms1/index.php?m=Service&c=DeviceInfo&a=add_edit&type=edit&ID=" + value);

        },
        //删除
        deleteData: function (id, groupID) {
            var that = this;
            if (id && groupID) {
                $.messager.confirm('注意', '此操作不可恢复是否确定？', function (r) {

                    $.messager.confirm(that.lang.prompt, that.lang.sureDelete, function (r) {
                        if (r) {
                            $.ajax({
                                url: "/TobaccoGms1/index.php?m=Service&c=DeviceInfo&a=Delete",
                                type: 'Post',
                                dataType: 'Json',
                                data: {
                                    id: id,
                                    group_id: groupID
                                }
                            })
                                .done(function (result) {

                                    if (result.Result) {

                                        $.messager.alert(that.lang.prompt, result.Msg, 'info');

                                        $("#grid").datagrid('reload');
                                    } else {

                                        $.messager.alert(that.lang.prompt, result.Msg, 'error');
                                    }
                                });
                        } else {
                            $("#grid").datagrid('unselectAll');
                        }
                    });

                });
            }
        },
        //查询
        query: function (value, name) {

        },
        resetQuery: function () {

        },
        //操作
        operate: function (value, row, index) {
            var html = "<div id='operate' style='height: 30px;padding-top: 10px'>";

            if (result_edit) {
                html = html + "<a href='javascript:;' style='text-decoration: none;' onclick='window.deviceInfo.edit(\"" + row.id + "\")'>" +
                    "<img style='line-height: 30px;border:none;width: 46px;vertical-align: bottom' src='/TobaccoGms1/Public/Static/Easyui/themes/icons/edit_add2.png'></a>&nbsp;"
            }
            if (result_del) {
                html = html + "<a href='javascript:;' style='text-decoration: none;' onclick='window.deviceInfo.deleteData(\"" + row.id + "\",\"" + row.group_id + "\")'>" +
                    "<img style='line-height: 30px;border:none;width: 46px;vertical-align: bottom' src='/TobaccoGms1/Public/Static/Easyui/themes/icons/delete1.png'></a>&nbsp;"

            }
            // console.log("aaaaa"+result_edit);
            return html + "</div>";
        },
        //确定操作
        operateSure: function () {
            var that = this;
            var action = that.operateFlag == "add" ? "Create" : "Edit";

            $("#form_device").form('submit', {
                url: "/TobaccoGms1/index.php?m=Service&c=DeviceInfo&a=" + action,
                onSubmit: function () {
                    var flag = true;

                    flag = flag && $("#inp_device_no").textbox('isValid');

                    flag = flag && $("#inp_bar_code").textbox('isValid');

                    flag = flag && $("#inp_group_id").combotree('isValid');

                    flag = flag && $("#inp_device_type").combobox('isValid');

                    flag = flag && $("#inp_channel_num").combobox('isValid');


                    var str1 = $("#inp_device_type").combobox('getValue');
                    if ('--请选择--' == str1) {
                        flag = false;
                    }

                    var str2 = $("#inp_channel_num").combobox('getValue');
                    if ('--请选择--' == str2) {
                        flag = false;
                    }

                    if (!flag) {
                        $.messager.alert('提示', "数据填写不完整,请检查", 'info');
                        return flag;
                    }
                },
                success: function (data) {
                    var jsonResult = $.parseJSON(data);
                    if (jsonResult.Result) {

                        $("#grid").datagrid('reload');

                        $.messager.show({
                            title: '操作完成',
                            msg: jsonResult.Msg,
                            timeout: 3000,
                            showType: 'slide'
                        });


                        that.formDisplayToggle("div_dialog", false);

                    } else {
                        $.messager.alert(that.lang.prompt, jsonResult.Msg, 'error');
                    }
                }
            });
        },
        //取消操作
        operateCancel: function () {
        },
        //转组
        moveGroup: function () {
            var that = this;

            $('#div_dialog_changeGroup').window({
                width: 350,
                height: 260,
                resizable: false,
                collapsible: false,
                minimizable: false,
                maximizable: false
            });

            $('#div_dialog_changeGroup').window('open');

            $('#div_dialog_changeGroup').window('refresh', "/TobaccoGms1/index.php?m=Service&c=DeviceInfo&a=changeGroup");


        },
        //确定转组操作
        moveGroupSure: function () {
            var that = this;

            var ids = $('#change_device').combobox('getValues');

            console.log('期待转组ID:' + ids);

            var group_id = $('#change_group').combotree('getValue');

            console.log('目标ID' + group_id);

            $.ajax({
                url: '/TobaccoGms1/index.php?m=Service&c=DeviceInfo&a=transferGroup',
                type: 'post',
                dataType: 'json',
                data: {
                    ids: ids,
                    group_id: group_id
                },
                success: function (data) {

                    console.log(data);
                },
                error: function () {
                    $.messager.alert(that.lang.prompt, '数据载入失败!', 'error');
                }

            })


        },
        //取消转组
        moveGroupCancel: function () {

            $('#div_dialog_changeGroup').window('close');
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
        deviceInfo.init();
        if (window.parent.guide) {
            window.parent.guide.removeLoading();
        }
    });
    return window.deviceInfo = window.deviceInfo || deviceInfo;
})($, window);

