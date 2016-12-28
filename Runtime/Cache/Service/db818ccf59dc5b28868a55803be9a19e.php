<?php if (!defined('THINK_PATH')) exit();?><!--转组功能-->
<div>
    <h3 class="ftitle">批量转组</h3>
    <br>
    <div class="fitem">
        <label>待转设备选择：</label>
        <input id="change_device" style="width: 180px;height: 30px;">
        <span style="color: #CC0000;margin-left: 5px;line-height: inherit;text-align:center;font-weight: bold;">*</span>
    </div>
    <div class="fitem">
        <label>目标单位选择：</label>
        <input id="change_group" style="width: 180px;height: 30px;">
        <span style="color: #CC0000;margin-left: 5px;line-height: inherit;text-align:center;font-weight: bold;">*</span>
    </div>

    <br >
    <br >


</div>


<script>


    $('#change_device').combobox({
        url: "<?php echo U('Service/DeviceInfo/getAllDevice');?>",
        multiple:true,
        valueField: 'id',
        textField: 'text',
        required:true,
        panelHeight: 'auto'
    });
    $('#change_group').combotree({
        url: '<?php echo U("Admin/Function/getGroupInfosToTree");?>',
        required:true,
        panelHeight: 100
    });
</script>