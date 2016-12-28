<?php if (!defined('THINK_PATH')) exit();?><div id="div_ftitle" class="ftitle"></div>

<form id="form_sim" method="post">
    <div style="width:50%;float:left;">
        <div class="fitem">
            <label>SIM卡号码：</label>
            <input id="inp_id" name="id" type="hidden"/>
            <input id="inp_simcard_no" type="text" name="simcard_no" style="width: 180px;height: 30px"/>
            <span> * </span>
        </div>

        <div class="fitem">
            <label>IMEI：</label>
            <input id="inp_sim_imei" type="text" name="simcard_imei" style="width: 180px;height: 30px"/>
            <span> * </span>
        </div>

        <div class="fitem">
            <label>运营商：</label>
            <input id="inp_simcard_operator" type="text" name="simcard_operator" style="width: 180px;height: 30px"/>
        </div>

        <div class="fitem">
            <label>套餐流量：</label>
            <input id="inp_sim_flow" type="text" name="simcard_flow" style="width: 180px;height: 30px"/>
        </div>

        <div class="fitem">
            <label>备注：</label>
            <input id="inp_remark" type="text" name="remark" style="width: 180px;height: 30px"/>
        </div>

    </div>
    <div style="width:50%;float:left;">

        <div class="fitem">
            <label>企业车队树：</label>
            <input id="inp_group_id" type="text" name="group_id" style="width: 180px;height: 30px"/>
            <span> * </span>
        </div>

        <div class="fitem">
            <label>网络类型：</label>
            <input id="inp_network_type" type="text" name="simcard_network_type" style="width: 180px;height: 30px"/>
            <span> * </span>
        </div>

        <div class="fitem">
            <label>开卡时间：</label>
            <input id="inp_sim_open_time" type="text" name="simcard_open_time" style="width: 180px;height: 30px"/>
            <span> * </span>
        </div>

        <div class="fitem">
            <label>终端编号：</label>
            <input id="inp_device_id" type="text" name="device_id" style="width: 180px;height: 30px"/>
        </div>

        <div class="fitem" style="margin-top: 15px">
            <label>SIM卡状态：</label>
            <input id="inp_status_yes" type="radio" name="status" checked="checked" value="1"/>启用
            <input id="inp_status_no" type="radio" name="status" value="2"/>停用
        </div>

    </div>
</form>

<script type="text/javascript">

    $(function () {

        if ('<?php echo ($_type); ?>' == '编辑') {
            var _info = JSON.parse('<?php echo ($_info); ?>');
        }

        var url = '<?php echo U("Service/DeviceInfo/getBindDevice");?>&groupID=';

        $('#inp_simcard_no').numberbox({
            required: true,
            validType: 'phone'
        });

        $("#inp_device_id").combobox({
            valueField: 'id',
            textField: 'text',
            editable: false,
            panelHeight: 100
        });


        $('#inp_group_id').combotree({
            url: '<?php echo U("Admin/Function/getGroupInfosToTree");?>',
            required: true,
            editable: false,
            onLoadSuccess: function () {

                if ('<?php echo ($_type); ?>' == '编辑') {
                    url += _info.group_id;
                }
                else {

                    var root = $("#inp_group_id").combotree('tree').tree('getRoot');

                    $("#inp_group_id").combotree('setValue', root.id);

                    url += root.id;
                }


                $.ajax({
                    url: url,
                    type: 'post',
                    dataType: 'json',
                    success: function (data) {

                        if ('<?php echo ($_type); ?>' == '编辑') {
                            var temp = {
                                'id': _info.device_id,
                                'text': _info.device_no
                            };
                            data.push(temp);
                        }

                        $("#inp_device_id").combobox('loadData', data);
                    }
                });

                url = '<?php echo U("Service/DeviceInfo/getBindDevice");?>&groupID=';

            },
            onSelect: function (record) {
                url += record.id;
                $.ajax({
                    url: url,
                    type: 'post',
                    dataType: 'json',
                    success: function (data) {
                        $("#inp_device_id").combobox('loadData', data);
                    }
                });

                url = '<?php echo U("Service/DeviceInfo/getBindDevice");?>&groupID=';
            }
        });


        var TypeData = $.parseJSON('<?php echo ($_dataType); ?>');

        $("#inp_network_type").combobox({
            required: true,
            data: TypeData,
            valueField: 'id',
            textField: 'text',
            editable: false,
            panelHeight: 'auto'
        });

        $('#inp_simcard_operator').textbox({
            validType: 'lettersChineseDigital[20]'
        });

        $('#inp_sim_flow').textbox({
            validType: 'lettersChineseDigital[20]'
        });

        $('#inp_sim_imei').numberbox({
            required: true,
            validType: 'lettersChineseDigital[20]'
        });

        $('#inp_sim_open_time').datebox({required: true});

        $('#inp_remark').textbox({
            multiline: true,
            validType: 'lettersChineseDigital[20]'
        });


        $('#div_ftitle').text('<?php echo ($_type); ?>');

        ////数据正确性校验
        $.extend($.fn.validatebox.defaults.rules, {
            phone: {
                validator: function (value, param) {
                    var rex = /^1[3-8]+\d{9}$/;
                    value = $.trim(value);
                    return rex.test(value);
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
            $("#form_sim").form('clear');

            $("#inp_id").val(_info.id);

            $('#inp_simcard_no').numberbox('setValue', _info.simcard_no);

            $('#inp_sim_imei').numberbox('setValue', _info.simcard_imei);

            $('#inp_network_type').combobox('select', _info.simcard_network_type);

            $('#inp_simcard_operator').textbox('setValue', _info.simcard_operator);

            $('#inp_sim_flow').textbox('setValue', _info.simcard_flow);

            $('#inp_remark').textbox('setValue', _info.remark);


            $("#inp_group_id").combotree('setValue', _info.group_id);
//
            $('#inp_group_id').combotree('readonly');

            $('#inp_device_id').combobox('setValue', _info.device_id);

            $('#inp_sim_open_time').datebox('setValue', _info.simcard_open_time);


            if ('1' == _info.status) {

                $('#inp_status_yes')['0'].checked = true;
            }
            else {

                $('#inp_status_no')['0'].checked = true;
            }


        }

        if ('<?php echo ($_type); ?>' == '编辑') {
            fillForm();
        }
        else {


            $("#inp_network_type").combobox('setValue', '--请选择--');

            var strDate = '';

            var nowDate = new Date(); //实例一个时间对象；
            strDate += (nowDate.getFullYear() + '-');   //获取系统的年；
            strDate += ((nowDate.getMonth() + 1) + '-');   //获取系统月份，由于月份是从0开始计算，所以要加1
            strDate += nowDate.getDate(); // 获取系统日，


            $('#inp_sim_open_time').datebox('setValue', strDate);

        }

    });


</script>