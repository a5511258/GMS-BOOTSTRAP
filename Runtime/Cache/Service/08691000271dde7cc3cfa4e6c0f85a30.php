<?php if (!defined('THINK_PATH')) exit();?><div id="div_ftitle" class="ftitle"></div>

<form id="form_device" method="post">
    <div style="width:50%;float:left;">
        <div class="fitem">
            <label>终端编号：</label>
            <input id="inp_device_id" name="id" type="hidden"/>
            <input id="inp_device_no" type="text" name="device_no"
                   style="width: 180px;height: 30px"/>
            <span> * </span>
        </div>

        <div class="fitem">
            <label>条码：</label>
            <input id="inp_bar_code" type="text" name="bar_code"
                   style="width: 180px;height: 30px"/>
            <span> * </span>
        </div>


        <div class="fitem">
            <label>设备类型：</label>
            <input id="inp_device_type" type="text" name="device_type"
                   style="width: 180px;height: 30px"/>
            <span> * </span>
        </div>


        <div class="fitem">
            <label>通道数：</label>
            <input id="inp_channel_num" type="text" name="channel_num"
                   style="width: 180px;height: 30px"/>
            <span> * </span>
        </div>
        <div class="fitem">
            <label>制造商：</label>
            <input id="inp_manufacturer" type="text" name="manufacturer" maxlength="80"
                   style="width: 180px;height: 30px"/>
        </div>


    </div>
    <div style="width:50%;float:left;">
        <div class="fitem">
            <label>企业车队树：</label>
            <input id="inp_group_id" type="text" name="group_id"
                   style="width: 180px;height: 30px"/>
            <span> * </span>
        </div>
        <div class="fitem">
            <label>绑定车牌号码：</label>
            <input id="inp_vehicle_id" type="text" name="vehicle_id"
                   style="width: 180px;height: 30px"/>
        </div>
        <div class="fitem">
            <label>绑定SIM卡号码：</label>
            <input id="inp_simCard_id" type="text" name="simcard_id"
                   style="width: 180px;height: 30px"/>
        </div>
        <div class="fitem">
            <label>是否视频：</label>
            <input id="inp_is_video_yes" type="radio" name="is_video" checked="checked" value="1"/>是
            <input id="inp_is_video_no" type="radio" name="is_video" value="0"/>否
        </div>

        <div class="fitem">
            <label>设备状态：</label>
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

        var url = '<?php echo U("Service/SimcardInfo/getBindSimCard");?>&groupID=';

        $('#inp_device_no').textbox({
            required: true
        });

        $("#inp_simCard_id").combobox({
            valueField: 'id',
            textField: 'text',
            editable: false,
            panelHeight: 100
        });



        $("#inp_group_id").combotree({
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
                                'id': _info.simcard_id,
                                'text': _info.simcard_no
                            };
                            data.push(temp);
                        }

                        $("#inp_simCard_id").combobox('loadData', data);
                    }
                });

                url = '<?php echo U("Service/SimcardInfo/getBindSimCard");?>&groupID=';

            },
            onSelect: function (record) {
                url += record.id;
                $.ajax({
                    url: url,
                    type: 'post',
                    dataType: 'json',
                    success: function (data) {
                        $("#inp_simCard_id").combobox('loadData', data);
                    }
                });

                url = '<?php echo U("Service/SimcardInfo/getBindSimCard");?>&groupID=';
            }
        });


        var TypeData = $.parseJSON('<?php echo ($_dataType); ?>');


        var ChannelData = $.parseJSON('<?php echo ($_dataChannel); ?>');


        $("#inp_device_type").combobox({
            required: true,
            data: TypeData,
            valueField: 'id',
            textField: 'text',
            editable: false,
            panelHeight: 'auto'
        });


        $("#inp_channel_num").combobox({
            required: true,
            data: ChannelData,
            valueField: 'id',
            textField: 'text',
            editable: false,
            panelHeight: 'auto'
        });

        $("#inp_vehicle_id").combobox({

            valueField: 'id',
            textField: 'text',
            editable: false,
            panelHeight: 100
        });




        $('#inp_bar_code').textbox({
            required: true,
            validType: 'lettersChineseDigital[20]'
        });

        $('#inp_manufacturer').textbox({
            validType: 'lettersChineseDigital[20]'
        });


        $('#div_ftitle').text('<?php echo ($_type); ?>');

        ////数据正确性校验
        $.extend($.fn.validatebox.defaults.rules, {
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
            $("#form_device").form('clear');

            $("#inp_device_id").val(_info.id);

            $('#inp_device_no').textbox('setValue', _info.device_no);

            $('#inp_bar_code').textbox('setValue', _info.bar_code);

            $('#inp_device_type').combobox('select', _info.device_type);

            $('#inp_channel_num').combobox('select', _info.channel_num);

            $('#inp_manufacturer').textbox('setValue', _info.manufacturer);

            $("#inp_group_id").combotree('setValue', _info.group_id);
//
            $('#inp_group_id').combotree('readonly');

            $('#inp_vehicle_id').combobox('setValue', '');

            $('#inp_simCard_id').combobox('setValue', _info.simcard_id);


            if ('1' == _info.is_video) {

//                $("#rdo1").attr("checked","checked");

                $('#inp_is_video_yes')['0'].checked = true;
            }
            else {

                $('#inp_is_video_no')['0'].checked = true;
//                $('#inp_is_video_no').attr("checked","checked");
            }

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

            $("#inp_device_type").combobox('setValue', '--请选择--');
            $("#inp_channel_num").combobox('setValue', '--请选择--');
        }


    })


</script>