$(function(){
    var rgb;
    $("tr").mouseover(function(){
        rgb = $(this).css('background-color');
        $(this).css({
        'backgroundColor':'#5fafcd',
        'color':'#fff'
        });
    });
   $("tr").mouseout(function(){
       $(this).css({
       'backgroundColor':rgb,
       'color':'#000'
       });
    });
})
function regist() {
    if (CheckAddUser() == true) {
        formAddUser.action = 'add_user.php';
        formAddUser.submit();
    }
}
function params() {
    formAddUser.action = 'add_user_params.php';
    formAddUser.submit();
}
function clearAll() {
    formAddUser.action = 'add_user_clear_regist.php';
    formAddUser.submit();
}
function CheckAddUser() {
    if (formAddUser.device.value == "") {
        alert("设备编号不能为空！");
        formAddUser.device.focus();
        return false;
    }
    if (formAddUser.name.value == "") {
        alert("姓名不能为空！");
        formAddUser.name.focus();
        return false;
    }
    var set =/[^\u4e00-\u9fa5]/;
    if (set.test(formAddUser.name.value)) {
        alert("请输入中文姓名！");
        formAddUser.name.focus();
        return false;
    }
    if (formAddUser.name.value.length > 50) {
        alert("输入的姓名过长");
        formAddUser.name.focus();
        return false;
    }
    if (formAddUser.tel.value == "") {
        alert("电话号码不能为空！");
        formAddUser.tel.focus();
        return false;
    }   
    if (formAddUser.tentative_diagnose.value == "") {
        alert("请填写患者症状！");
        formAddUser.tentative_diagnose.focus();
        return false;
    }    
    if (formAddUser.medical_history.value == "") {
        alert("请填写病史！");
        formAddUser.medical_history.focus();
        return false;
    }    
    if (formAddUser.doctor.value == "") {
        alert("登记医生不能为空！");
        formAddUser.doctor.focus();
        return false;
    }
    if (formAddUser.age.value == "") {
        alert("年龄不能为空！");
        formAddUser.age.focus();
        return false;
    }
    if (formAddUser.hours.value == "0" && formAddUser.mode.value != "3") {
        alert("请选择监护时长。");
        formAddUser.hours.focus();
        return false;
    }
    return true;
}
function checkAddDoc() {
    if (formAddDoc.login_name.value == "") {
        alert("登录名不能为空。");
        formAddDoc.login_name.focus();
        return false;
    }
    if (formAddDoc.name.value == "") {
        alert("姓名不能为空。");
        formAddDoc.name.focus();
        return false;
    }
    var set =/[^\u4e00-\u9fa5]/;
    if (set.test(formAddDoc.name.value)) {
        alert("请输入中文姓名。");
        formAddDoc.name.focus();
        return false;
    }
    if (formAddDoc.pwd1.value == "") {
        alert("密码不能为空。");
        formAddDoc.pwd1.focus();
        return false;
    }
    if (formAddDoc.pwd2.value != formAddDoc.pwd1.value) {
        alert("两次输入密码不一致。");
        formAddDoc.pwd1.focus();
        return false;
    }
}
function checkEditDoc() {
    if (formEditDoc.newJobNo.value == "") {
        alert("登录用的名字不能为空。");
        formEditDoc.newJobNo.focus();
        return false;
    }
    if (formEditDoc.name.value == "") {
        alert("姓名不能为空。");
        formEditDoc.name.focus();
        return false;
    }
    var set =/[^\u4e00-\u9fa5]/;
    if (set.test(formEditDoc.name.value)) {
        alert("请输入中文姓名。");
        formEditDoc.name.focus();
        return false;
    }
    if (formEditDoc.pwd1.value != null && formEditDoc.pwd1.value != "") {
        if (formEditDoc.pwd2.value != form1.pwd1.value) {
            alert("两次输入密码不一致。");
            formEditDoc.pwd1.focus();
            return false;
        }
    }
    if (formEditDoc.newJobNo.value==formEditDoc.oldLoginName.value 
            && formEditDoc.name.value==formEditDoc.oldName.value 
            && (formEditDoc.pwd1.value == null || formEditDoc.pwd1.value == "")) {
            alert("您什么都没有修改，不能提交。");
            return false;
    }
    formEditDoc.submitType.value="edit";
    formEditDoc.submit();
}
function deleteDoc() {
    formEditDoc.submitType.value="delete";
    formEditDoc.submit();
}
function loginSubmit(){
    document.getElementById("login_form").submit()
}
function loginReset(){
    document.getElementById("login_form").reset();
}