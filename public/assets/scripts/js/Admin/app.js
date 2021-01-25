$(function() {
    var serialNumber=$('#User_serialNumber').val();
    if(serialNumber){
        // $('#User_serialNumber').closest('.content-panel').find('.email-user').hide();
    }else{
        $('#User_serialNumber').closest('.serial-user').hide();
    }
});