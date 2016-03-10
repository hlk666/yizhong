function unbindDevice(id)
{
    if (confirm('确定要解除该设备ID和医院的绑定么？')) {
        window.location = 'unbind_device.php?id=' + id;
    } else {
        //do nothing.
    }
}