{include file = "header.tpl"}
{if $noData}
没有符合条件的数据。
{else}
<table class="table table-bordered">
    <thead>
      <tr>
        <th>ID</th>
        <th>姓名</th>
        <th>电话</th>
        <th>客户推动记录</th>
        <th>修改</th>
        <th>删除</th>
      </tr>
    </thead>
    <tbody>
      {section loop=$user name=row}
        <tr>
        <td>{$user[row].user_id}</td>
        <td>{$user[row].real_name}</td>
        <td>{$user[row].tel}</td>
        <td><a href="{$domain}schedule?user={$user[row].user_id}">查看记录</a></td>
        <td><button type="button" class="btn btn-xs btn-warning" onclick="javascript:editUser({$user[row].user_id})">修改</button></td>
        <td><button type="button" class="btn btn-xs btn-danger" onclick="javascript:deleteUser({$user[row].user_id})">删除</button></td>
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