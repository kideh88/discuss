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

    var objChatForm = document.getElementById('newPublicChatForm');
    var objButtonHideChatForm = document.getElementById('hideChatForm');
    var objButtonShowChatForm = document.getElementById('showChatForm');
    if(objChatForm && objButtonShowChatForm && objButtonHideChatForm) {
        addEvent(objButtonShowChatForm, 'click', function() {
            hide(objButtonShowChatForm);
            show(objChatForm);
        });
        addEvent(objButtonHideChatForm, 'click', function() {
            hide(objChatForm);
            show(objButtonShowChatForm)
        });
    }
});
