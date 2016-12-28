/**
 * Created by li on 16/7/17.
 */
(function ($, window) {
    var group = {
        operateFlag: undefined,
        //初始化
        init: function () {
            var that = this;

            //加载列表数据
            $('#grid').treegrid({
                url: grid_url,
                idField: 'id',
                treeField: 'text',
                toolbar: '#div_toolbar',
                border: false,
                singleSelect: true,
                rownumbers: true,
                animate: true,
                collapsible: true,
                fit: true,
                fitColumns: true,
                onLoadError: function (error) {
                    $.messager.alert(error.status + '错误', "未成功获取数据，错误原因：" + error.statusText, 'error');
                },
                onLoadSuccess: function (row, data) {

                    if (0 == data.length) {
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

        add: function () {
            this.operateFlag = 'add';

            $('#div_dialog').window({
                width: 700,
                height: 400,
                resizable: false,
                collapsible: false,
                minimizable: false,
                maximizable: false
            });

            $('#div_dialog').window('open');
            $('#div_dialog').window('refresh', "/TobaccoGms1/index.php?m=Service&c=GroupInfo&a=add_edit&type=add");
        },

        //编辑
        edit: function (value) {
            this.operateFlag = 'edit';

            $('#div_dialog').window({
                width: 700,
                height: 400,
                resizable: false,
                collapsible: false,
                minimizable: false,
                maximizable: false
            });

            $('#div_dialog').window('open');
            $('#div_dialog').window('refresh', "/TobaccoGms1/index.php?m=Service&c=GroupInfo&a=add_edit&type=edit&groupID=" + value);
        },

        deleteData: function (group_id) {
            var that = this;
            if (group_id == 1) {
                $.messager.alert('提示', '无法删除根节点', 'info');
                window.setTimeout(function () {
                    $("#grid").treegrid('unselectAll');
                }, 500);
            }
            else {
                $.messager.confirm('注意', '此操作不可恢复是否确定？', function (r) {
                    if (r) {
                        $.messager.confirm('提示', '确定要删除当前选项？', function (r) {
                            if (r) {
                                $.ajax({
                                    url: del_url,
                                    type: 'post',
                                    data: {
                                        group_id: group_id
                                    },
                                    dataType: 'json',
                                    success: function (data) {

                                        $("#grid").treegrid('reload');

                                        $.messager.alert('提示', data.Msg, 'info');
                                    },
                                    error: function (errorInfo) {

                                        $.messager.alert("error:" + errorInfo.statusText);

                                    }

                                });
                            }
                        });
                    }
                });
            }
        },


        //检测自己是否选择自身或子节点
        checkIsSelfOrChildren: function (one, select) {
            //原节点
            var forNode = select.tree('find', one);
            var childrenNode = select.tree('getData', forNode.target);
            //选择的节点
            var selectNode = select.tree('getSelected');
            //自身节点
            if (forNode.id == selectNode.id) return true;
            else if (childrenNode.children != undefined) {
                for (var i = 0, len = childrenNode.children.length; i < len; i++) {
                    if (selectNode.id == childrenNode.children[i].id) return true;
                    if (childrenNode.children[i].children != undefined)
                        this.foreachCheck(selectNode.id, childrenNode.children[i].children);
                }
            }
            return false;
        },
        //递归检测
        foreachCheck: function (id, node) {
            for (var i = 0, len = node.length; i < len; i++) {
                if (id == node[i].id) return true;
                if (node[i].children != undefined)
                    window.group.foreachCheck(id, node[i].children);
            }
            return false;
        },


        //组织架构视图
        exportTree: function () {
            $("#div_org").empty();


            $('#div_dialog2').window({
                resizable: false,
                collapsible: false,
                minimizable: false,
                maximizable: false
            });


            $('#ul_tree').combotree({
                url:'/TobaccoGms1/index.php?m=Admin&c=Function&a=getGroupInfosToTree',
                onLoadSuccess:function () {
                    $("#ul_tree").hide();
                    var tree = $("#ul_tree").combotree('tree').clone();
                    if (tree.length > 0) {
                        $("div", tree).replaceWith(function () {
                            return $(this).text();
                        });
                        tree.jOrgChart({
                            chartElement: '#div_org'
                        });
                    }
                    $('#div_dialog2').window('open');
                },
                onLoadError:function (error) {
                    $.messager.alert(e.status + '错误', "未成功获取数据，错误原因：" + e.statusText, 'error');
                }
            });


        },


        //导出数据
        exportData:function(){
            var nodes = $('#grid').treegrid('getChildren');

            var datas = [];
            for(var i = 0;i<nodes.length;i++){
                var node = nodes[i];
                datas.push({
                    'group_name':node.text,
                    'parent_name':node.parent_name,
                    'limit_num':node.limit_num,
                    'group_type':node.group_type,
                    'industry':node.industry,
                    'responsibility_people':node.responsibility_people,
                    'tel_no':node.tel_no,
                    'address':node.address,
                    'description':node.description
                });
            }
            var that = this;
            that.formDisplayToggle("div_dialog_loading", true);
            $.ajax({
                url:"/TobaccoGms1/index.php?m=Service&c=GroupInfo&a=exportData",
                type:'post',
                dataType:'json',
                data: {'rows':datas},
                success:function(data) {
                    that.formDisplayToggle("div_dialog_loading", false);
                    if (data.Result) {
                        setTimeout(function(){
                            window.location.href = data.Data;
                        },100);
                    }
                },
                error:function (e) {
                    $.messager.alert(e.status + '错误', "未成功获取数据，错误原因：" + e.statusText, 'error');
                }
            });
        },


        //查询
        query: function () {


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

            $('#search_dialog').window('refresh', group_search_url);

            $('#search_dialog').window('open');

        },

        SearchSure: function () {

            var queryParams = $('#grid').datagrid('options').queryParams;
            $.each($('#seach_form').serializeArray(), function () {
                queryParams[this['name']] = this['value'];
            });

            $('#grid').treegrid('reload');
        },
        resetQuery: function () {

            $('#grid').treegrid({queryParams: {}});

        },
        //操作
        operate: function (value, row, index) {
            return "<a href='javascript:;' onclick='window.group.edit(\"" + row.id + "\")'>更新</a>&nbsp;|&nbsp;<a href='javascript:;' onclick='window.group.deleteData(\"" + row.id + "\")'>删除</a>";
        },
        //确定操作
        operateSure: function () {
            var action = this.operateFlag == "add" ? "Create" : "Edit";
            var url = "/TobaccoGms1/index.php?m=Service&c=GroupInfo&a=" + action;
            var that = this;
            $("#form_group").form('submit', {
                url: url,
                onSubmit: function () {
                    var flag = true;
                    var id = $("#inp_groupid").val();

                    flag = flag && $("#inp_name").textbox('isValid');


                    flag = flag && $("#inp_groupfather").combotree('isValid');

                    if (flag && action == "Update" && id != "1") {
                        flag = flag && !that.checkIsSelfOrChildren(parseInt(id, 10), $("#inp_groupfather").combotree('tree'));
                        if (!flag) {
                            $.messager.alert('提示', '不能选择自己为父节点', 'info');
                            return false;
                        }
                    }

                    flag = flag && $("#inp_limitnum").numberbox('isValid');


                    flag = flag && $("#inp_grouptype").combobox('isValid');


                    var str1 =  $("#inp_grouptype").combotree('getValue');
                    if('--请选择--' == str1){
                        flag = false;
                    }


                    flag = flag && $("#inp_industry").combobox('isValid');

                    var str2 =  $("#inp_industry").combotree('getValue');
                    if('--请选择--' == str2){
                        flag = false;
                    }



                    if (!flag) {
                        $.messager.alert('提示', "数据填写不完整,请检查", 'info');
                        return false;
                    }

                    return flag;
                },
                success: function (data) {
                    var jsonResult = $.parseJSON(data);
                    if (jsonResult.Result) {

                        $("#grid").treegrid('reload');

                        $.messager.show({
                            title: '操作完成',
                            msg: jsonResult.Msg,
                            timeout: 3000,
                            showType: 'slide'
                        });

                        that.formDisplayToggle("div_dialog", false);
                    }
                    else {
                        $.messager.alert('提示', '操作失败: ' + jsonResult.Msg, 'error');
                    }


                }
            });
        },
        //取消操作
        operateCancel: function () {
            this.operateFlag = undefined;
            $("#grid").treegrid('unselectAll');
            this.formDisplayToggle("div_dialog", false);
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
        group.init();
        if (window.parent.guide) {
            window.parent.guide.removeLoading();
        }
    });
    return window.group = window.group || group;
})($, window);