{include file = "header.tpl"}
{if $noData}
没有符合条件的数据。
{else}
<table class="table table-bordered">
    <thead>
      <tr>
        <th>记录ID</th>
        <th>日期</th>
        <th>医院</th>
        <th>开发阶段</th>
        <th>最新进展</th>
        <th>意见或建议</th>
        <th>修改</th>
        <th>删除</th>
      </tr>
    </thead>
    <tbody>
      {section loop=$schedule name=row}
        <tr>
        <td>{$schedule[row].schedule_id}</td><td>{$schedule[row].create_date}</td>
        <td>{$schedule[row].hospital_name}</td><td>{$schedule[row].stage}</td>
        <td>{$schedule[row].progress}</td><td>{$schedule[row].info}</td>
        <td><button type="button" class="btn btn-xs btn-warning" onclick="javascript:editSchedule({$schedule[row].schedule_id})">修改</button></td>
        <td><button type="button" class="btn btn-xs btn-danger" onclick="javascript:deleteSchedule({$schedule[row].schedule_id})">删除</button></td>
        </tr> 
      {sectionelse}
      {/section}  
    </tbody>
</table>
<div style="text-align:right;">
<ul class="pagination">{$paging}</ul>
<div>
{/if}
{include file = "footer.tpl"}