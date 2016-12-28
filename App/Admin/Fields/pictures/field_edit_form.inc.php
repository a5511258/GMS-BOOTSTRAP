<table width="100%">
  <tr>
    <th>图片添加模式:</th>
    <td><select name="extra[updata_type]" style="height:30px;" class="easyui-combobox">
        <option value="0" <?php if($extra['updata_type']=='0'){?>selected="selected"<?php }?>>文本框模式</option>
        <option value="1" <?php if($extra['updata_type']=='1'){?>selected="selected"<?php }?>>混合模式</option>
      </select></td>
  </tr>
</table>
