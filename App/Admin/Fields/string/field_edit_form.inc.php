<table width="100%">
  <tr>
    <th>是否必填:</th>
    <td><select name="extra[required]" class="easyui-combobox">
        <option value="0" <?php if($extra['required']=='0'){?>selected="selected"<?php }?>>否</option>
        <option value="1" <?php if($extra['required']=='1'){?>selected="selected"<?php }?>>是</option>
      </select></td>
  </tr>
</table>