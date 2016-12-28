<table width="100%">
  <tr>
    <th>选项:</th>
    <td><textarea name="extra[option]" class="easyui-textbox" style="width:300px;height:80px" data-options="multiline:true"><?php echo $extra['option']; ?></textarea></td>
  </tr>
  <tr>
    <th>是否支持多选:</th>
    <td><select name="extra[multiple]" class="easyui-combobox" style="height:30px;" data-options="value:'<?php echo $extra['multiple']; ?>',editable:false">
        <option value="0">单选</option>
        <option value="1">多选</option>
      </select></td>
  </tr>
</table>