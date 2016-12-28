<table width="100%">
  <tr>
    <th>宽度:</th>
    <td><input type="text" name="extra[width]" class="easyui-textbox" style="height:30px;" value="<?php echo $extra['width']; ?>"></td>
  </tr>
  <tr>
    <th>高度:</th>
    <td><input type="text" name="extra[height]" class="easyui-textbox" style="height:30px;" value="<?php echo $extra['height']; ?>" ></td>
  </tr>
  <tr>
    <th>参数:</th>
    <td><select name="extra[config]" class="easyui-combobox" data-options="value:'<?php echo $extra['config']; ?>',editable:false">
        <option value="0">简单模式</option>
        <option value="1">默认模式</option>
      </select></td>
  </tr>
</table>