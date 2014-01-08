$( document ).ready(function() {
//    $("#continue").click(function(){
//        window.location = "chat.php";
//    });
//    var objLoginForm = document.getElementById('userLogin');
//    objLoginForm.addEventListener('onsubmit', function(event) {
//        sendFormData(event, showResponse);
//    });
//
    var objLogoutLink = document.getElementById('link-log-out');
    if(objLogoutLink) {
        addEvent(objLogoutLink, 'click', function() {
            customAjax('User', 'doUserLogout', null, reloadCallback);
        });
    }

});
