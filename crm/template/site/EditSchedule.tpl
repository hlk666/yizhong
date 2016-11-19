{include file = "header.tpl"}
{if $postSuccess}
<button type="button" class="btn btn-lg btn-info" style="margin-top:50px;" 
onclick="javascript:location.href='{$domain}schedule';">操作成功，点击查看记录</button>
{else}
<form class="form-horizontal" role="form" method="post">
  <lable><font color="red">{$error}</font></lable>
  <input type="hidden" name="schedule_id" value="{$schedule_id}">
  <div class="form-group">
    <label for="hospital_parent" class="col-sm-2 control-label">选择医院</label>
    <div class="col-sm-10">
      <select class="form-control" name="hospital">
        {section loop=$hospital name=row}
          <option value="{$hospital[row].hospital_id}" {if $hospital[row].hospital_id == $hospital_id}selected="selected"{/if}>{$hospital[row].hospital_name}</option>
        {sectionelse}
        {/section}
      </select>
    </div>
  </div>
  <div class="form-group">
    <label for="hospital_name" class="col-sm-2 control-label">开发阶段</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="stage" name="stage" value="{$stage}" required>
    </div>
  </div>
  <div class="form-group">
    <label for="hospital_name" class="col-sm-2 control-label">最新进展</label>
    <div class="col-sm-10">
      <textarea name="progress" class="form-control" rows="3" required>{$progress}</textarea>
    </div>
  </div>
  <div class="form-group">
    <label for="hospital_name" class="col-sm-2 control-label">相关意见或建议</label>
    <div class="col-sm-10">
      <textarea name="info" class="form-control" rows="3" required>{$info}</textarea>
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <button type="submit" class="btn btn-lg btn-success" name="submit">保存</button>
      <button type="button" class="btn btn-lg btn-primary" style="margin-left:50px" 
        onclick="javascript:history.back();">返回</button>
    </div>
  </div>
</form>
{/if}
{include file = "footer.tpl"}