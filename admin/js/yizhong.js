function unbindDevice(id)
{
    if (confirm('确定要解除该设备ID和医院的绑定么？')) {
        window.location = 'unbind_device.php?id=' + id;
    } else {
        //do nothing.
    }
}
function editHospital(id)
{
    window.location = 'edit_hospital.php?action=edit&id=' + id;
}
function editRelation(id)
{

    window.location = 'edit_relation.php?id=' + id;

}
function editTree(id)
{

    window.location = 'edit_tree.php?id=' + id;

}
function deleteHospital(id)
{
    if (confirm('确定删除该医院相关信息么？')) {
        window.location = 'edit_hospital.php?action=del&id=' + id;
    } else {
        //do nothing.
    }
}