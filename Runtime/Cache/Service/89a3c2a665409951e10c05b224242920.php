<?php if (!defined('THINK_PATH')) exit();?><div id="div_ftitle" class="ftitle"></div>

<form id="form_group" method="post">


    <div style="width:50%;float:left;">
        <div class="fitem">
            <label>名称：</label>
            <input id="inp_groupid" name="group_id" type="hidden"/>
            <input id="inp_name" type="text" name="group_name"
                   style="width: 180px;height: 30px"/>
            <span> * </span>
        </div>
        <div id="div_father" class="fitem">
            <label>上级名称：</label>
            <input id="hd_groupfatherlevel" type="hidden" name="id_level"/>
            <input id="inp_groupfather" type="text" name="parent_id"
                   style="width: 180px;height: 30px"/>
            <span> * </span>
        </div>
        <div class="fitem">
            <label>限制车辆数：</label>
            <input id="inp_limitnum" type="text"
                   name="limit_num" style="width: 180px;height: 30px"/>
            <span> * </span>
        </div>
        <div class="fitem">
            <label>负责人：</label>
            <input id="inp_responsibility_people" type="text" name="responsibility_people"
                   style="width: 180px;height: 30px"/>
        </div>
        <div class="fitem">
            <label>联系电话：</label>
            <input id="inp_telno" type="text" name="tel_no" maxlength="20" style="width: 180px;height: 30px"/>
        </div>


    </div>


    <div style="width:50%;float:left;">
        <div class="fitem">
            <label>组织类型：</label>
            <input id="inp_grouptype" type="text" name="group_type" style="width: 180px;height: 30px"/>
            <span> * </span>
        </div>
        <div class="fitem">
            <label>所属行业：</label>
            <input id="inp_industry" type="text" name="industry" style="width: 180px;height: 30px"/>
            <span> * </span>
        </div>

        <div class="fitem" style="height: 60px">
            <label>地址：</label>
            <input id="inp_address" type="text" name="address" maxlength="100" style="width: 180px;height: 60px;"/>
        </div>
        <div class="fitem" style="height: 60px">
            <label>描述：</label>
            <input id="inp_desc" name="description" maxlength="180" style="width: 180px;height: 60px"/>
        </div>
    </div>


</form>

<script type="text/javascript">

    $(function () {
        $('#inp_name').textbox({
            required: true,
            validType: 'minLength[3]'
        });

        $("#inp_groupfather").combotree({
            value:1,
            url: '<?php echo U("Admin/Function/getGroupInfosToTree");?>',
            required: true,
            editable: false
        });

        var TypeData = $.parseJSON('<?php echo ($_dataType); ?>');

       // console.log(TypeData);

        var IndustryData = $.parseJSON('<?php echo ($_dataIndustry); ?>');

       // console.log(IndustryData);

        $("#inp_grouptype").combobox({
            required: true,
            data: TypeData,
            valueField: 'id',
            textField: 'text',
            editable: false,
            panelHeight: 'auto'
        });


        $("#inp_industry").combobox({
            required: true,
            data: IndustryData,
            valueField: 'id',
            textField: 'text',
            editable: false,
            panelHeight: 100
        });

        $('#inp_limitnum').numberbox({
            max: 99999,
            min: 1,
            required: true
        });


        $('#inp_responsibility_people').textbox({
            validType: 'lettersChineseDigital[20]'
        });

        $('#inp_telno').textbox({
            validType: 'phone'
        });

        $('#inp_address').textbox({
            multiline: true,
            validType: 'lettersChineseDigital[100]'
        });

        $('#inp_desc').textbox({
            multiline: true,
            validType: 'lettersChineseDigital[100]'
        });

        $('#div_ftitle').text('<?php echo ($_type); ?>');

        ////数据正确性校验
        $.extend($.fn.validatebox.defaults.rules, {
            minLength: {
                validator: function (value, param) {
                    var reg = /^[\s\u4E00-\u9FA5A-Za-z0-9-_]*$/;
                    value = $.trim(value);
                    return reg.test(value) && value.length >= param;
                },
                message: '请至少输入{0}个有效字符'
            },
            phone: {
                validator: function (value, param) {
                    var rex = /^1[3-8]+\d{9}$/;
                    //var rex=/^(([0\+]\d{2,3}-)?(0\d{2,3})-)(\d{7,8})(-(\d{3,}))?$/;
                    //区号：前面一个0，后面跟2-3位数字 ： 0\d{2,3}
                    //电话号码：7-8位数字： \d{7,8
                    //分机号：一般都是3位数字： \d{3,}
                    //这样连接起来就是验证电话的正则表达式了：/^((0\d{2,3})-)(\d{7,8})(-(\d{3,}))?$/
                    var rex2 = /^((0\d{2,3})-)(\d{7,8})(-(\d{3,}))?$/;
                    value = $.trim(value);
                    return rex.test(value) || rex2.test(value);
                },
                message: '号码有误，请重填'
            }, //字母汉字数字
            lettersChineseDigital: {
                validator: function (value, param) {
                    var reg = /^[\s\u4E00-\u9FA5A-Za-z0-9-_]*$/;
                    value = $.trim(value);
                    return reg.test(value) && value.length <= param && value.length > 0;
                },
                message: '输入内容有误'
            }
        });

        //填充表单
        function fillForm() {

            $("#form_group").form('clear');

            $("#inp_groupid").val('<?php echo ($_info["group_id"]); ?>');

            $("#inp_name").textbox('setValue', '<?php echo ($_info["group_name"]); ?>');

            $("#inp_grouptype").combobox('select', '<?php echo ($_info["group_type"]); ?>');

            $("#inp_industry").combobox('select', '<?php echo ($_info["industry"]); ?>');
//

            $("#hd_groupfatherlevel").val('<?php echo ($_info["id_level"]); ?>');


            if (0 == '<?php echo ($_info["parent_id"]); ?>') {

                $("#inp_groupfather").combotree({readonly: true});
            }
            else {
                $("#inp_groupfather").combotree({readonly: false});
            }

            $("#inp_groupfather").combotree('setValue', '<?php echo ($_info["parent_id"]); ?>' == 0 ? '根节点' : '<?php echo ($_info["parent_id"]); ?>');

            $('#inp_responsibility_people').textbox('setValue','<?php echo ($_info["responsibility_people"]); ?>')

            $("#inp_limitnum").numberbox('setValue', '<?php echo ($_info["limit_num"]); ?>');
            $("#inp_telno").textbox('setValue', '<?php echo ($_info["tel_no"]); ?>');
            $("#inp_address").textbox('setValue', '<?php echo ($_info["address"]); ?>');
            $("#inp_desc").textbox('setValue', '<?php echo ($_info["description"]); ?>');

        }

        if ('<?php echo ($_type); ?>' == '编辑') {
            fillForm();
        }
        else{

            $("#inp_grouptype").combobox('select', '--请选择--');

            $("#inp_industry").combobox('select', '--请选择--');
        }


    })


</script>