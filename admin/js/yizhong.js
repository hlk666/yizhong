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
function move_patient(id, from, to, jump)
{
    /*
    if (confirm('确定要移动监护【id = ' + id + '】么？') {
        $.post("http://101.200.174.235/api/analytics_move_data", 
            {patient_id:id, hospital_from:from, hospital_to:to, operator:1},
            function(result){
                alert(result);
            });
        window.location = jump;
    } else {
        //do nothing.
    }
    */
    var x = document.getElementById('patient');
    alert(x);
}
function loadProvince(currentProvince)
{
    var tempFragment = document.createDocumentFragment();
    for(var i=0;i<proData_.length;i++){
        var option=document.createElement("option");
        option.setAttribute("value",proData_[i].pk);
        option.innerHTML=proData_[i].pv;
        if (proData_[i].pk == currentProvince) {
            option.setAttribute('selected', 'selected');
        }
        tempFragment.appendChild(option);
    }
    //使用dom碎片，减少对dom的重复操作
    proS.appendChild(tempFragment);
}
function loadCity(currentCity){
    if(proS.value==0){
        return;
    }else{
        cityS.innerHTML="<option value='0'>请选择</option>";
        var hasFound=false;
        var tempFragment = document.createDocumentFragment();
        for(var i=0;i<cityData_.length;i++){
            if(proS.value==cityData_[i].pk){
                hasFound=true;
                var option=document.createElement("option");
                option.setAttribute("value",cityData_[i].ck);
                option.innerHTML=cityData_[i].cv;
                if (cityData_[i].ck == currentCity) {
                    option.setAttribute('selected', 'selected');
                }
                tempFragment.appendChild(option);
                
            }else{
                //需要查找的数据都是放一块的，如果匹配过，又出现不匹配，那么之后数据肯定都是不匹配的。可以pass掉，减少循环次数
                if(hasFound){
                    break;
                }
            }
        }
        cityS.appendChild(tempFragment);
    }
}
function getHosName(id) {
    htmlobj = $.ajax({url : "hosName.php?id=" + id, async : false});
    $("#title").html(htmlobj.responseText);
}
function checkUser(user) {
    htmlobj = $.ajax({url : "checkUser.php?user=" + user, async : false});
    $("#check_user").html(htmlobj.responseText);
}