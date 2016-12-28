<table width="100%">
  <tr>
    <th>是否必填:</th>
    <td><select name="extra[required]" class="easyui-combobox">
        <option value="0" selected="selected">否</option>
        <option value="1">是</option>
      </select></td>
  </tr>
  <tr>
    <th>符号:</th>
    <td><select name="extra[unsifned]" class="easyui-combobox">
        <option value="0" selected="selected">无</option>
        <option value="1">有</option>
      </select></td>
  </tr>
  <tr>
    <th>取值范围:</th>
    <td>最小值:<input type="text" name="extra[min]" class="easyui-numberbox" style="width:40px; height:30px;"> - 最大值:<input type="text" name="extra[max]" class="easyui-numberbox" style="width:40px; height:30px;"></td>
  </tr>
  <tr>
    <th>最大精度:</th>
    <td><input type="text" name="extra[precision]" class="easyui-numberbox" style="height:30px;" value="0" data-options="min:0,precision:0"></td>
  </tr>
  <tr>
    <th>小数分隔符符号:</th>
    <td><input type="text" name="extra[decimalSeparator]" class="easyui-textbox" style="height:30px;" value="."></td>
  </tr>
  <tr>
    <th>千位分隔符符号:</th>
    <td><input type="text" name="extra[groupSeparator]" class="easyui-textbox" style="height:30px;" value=","></td>
  </tr>
  <tr>
    <th>前缀字符串:</th>
    <td><input type="text" name="extra[prefix]" class="easyui-textbox" style="height:30px;" value=""> 例如货币符号：$</td>
  </tr>
  <tr>
    <th>后缀字符串:</th>
    <td><input type="text" name="extra[suffix]" class="easyui-textbox" style="height:30px;" value=""> 例如货币符号：元</td>
  </tr>
</table>