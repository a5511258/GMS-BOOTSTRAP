<table width="100%">
  <tr>
    <th>数据来源:</th>
    <td><select name="extra[type]" class="easyui-combobox" data-options="value:'<?php echo $extra['type']; ?>',editable:false" style="height:30px">
        <option value="1">固定</option>
        <option value="2">可变</option>
        <option value="3">配置</option>
        <option value="4">方法</option>
      </select></td>
  </tr>
  <tr>
    <th></th>
    <td>选择数据来源后，根据类型在下面的参数中输入相应的值<br/>
    1> 固定：例(选项值1:选项名1|选项值2:选项名2)，与可变不一样的是将会直接写入文件,优点是负载相对较小，缺点是每次都需生成文件<br/>
    2> 可变：例(选项值1:选项名1|选项值2:选项名2)，与固定不一样的是将会每次读取数据库，然后解析字段,缺点负载相对较大,优点是不用修改后生成<br/>
    3> 配置：例(二维数组：FIELD_LIST|type|title，一维数组：FIELD_LIST),其中FIELD_LIST是配置名称，其中第一个是方法名，第二个是选项值，第三个是选项名<br/>
    4> 方法：例(Admin/AuthGroup/get_auth_role|id|text)其中第一个是方法名称，其中第一个是方法名，第二个是选项值，第三个是选项名显示值
    </td>
  </tr>
  <tr>
    <th>参数:</th>
    <td><textarea name="extra[option]" class="easyui-textbox" data-options="multiline:true" style="width:300px;height:80px"><?php echo $extra['option']; ?></textarea></td>
  </tr>
  <tr>
    <th>下拉框类型:</th>
    <td><select name="extra[form_type]" class="easyui-combobox" data-options="value:'<?php echo $extra['form_type']; ?>',editable:false" style="height:30px">
        <option value="1">普通列表</option>
        <option value="2">树形列表</option>
      </select></td>
  </tr>
  <tr>
    <th>是否支持多选:</th>
    <td><select name="extra[multiple]" class="easyui-combobox" data-options="value:'<?php echo $extra['multiple']; ?>',editable:false" style="height:30px">
        <option value="0">单选</option>
        <option value="1">多选</option>
      </select></td>
  </tr>
  <tr>
    <th>是否允许手写输入:</th>
    <td><select name="extra[editable]" class="easyui-combobox" data-options="value:'<?php echo $extra['editable']; ?>',editable:false" style="height:30px">
        <option value="false">否</option>
        <option value="true">是</option>
      </select></td>
  </tr>
  <tr>
    <th>是否必填:</th>
    <td><select name="extra[required]" class="easyui-combobox" data-options="value:'<?php echo $extra['required']; ?>',editable:false" style="height:30px">
        <option value="0">否</option>
        <option value="1">是</option>
      </select></td>
  </tr>
</table>