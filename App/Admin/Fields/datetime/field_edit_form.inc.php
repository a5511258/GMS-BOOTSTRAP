<table width="100%">
  <tr>
    <th>表单类型:</th>
    <td><select name="extra[from_type]" class="easyui-combobox">
        <option value="datebox" <?php if($extra['from_type']=='datebox'){?>selected="selected"<?php }?>>日期</option>
        <option value="datetimebox" <?php if($extra['from_type']=='datetimebox'){?>selected="selected"<?php }?>>日期时间</option>
      </select></td>
  </tr>
  <tr>
    <th>是否必填:</th>
    <td><select name="extra[required]" class="easyui-combobox">
        <option value="0" <?php if($extra['required']=='0'){?>selected="selected"<?php }?>>否</option>
        <option value="1" <?php if($extra['required']=='1'){?>selected="selected"<?php }?>>是</option>
      </select></td>
  </tr>
</table>