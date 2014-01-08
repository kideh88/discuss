var sendFormData = function(objEvent, fcnCallback) {
    objEvent.preventDefault();
    var objForm = objEvent.target;
    var strFormRequest = objEvent.target.getAttribute('data-action');
    var strController = objEvent.target.getAttribute('data-type');
    var arrFormData = {};
    for(var i = 0; i < objForm.getElementsByTagName('input').length; i += 1) {
        var objLoopedInput = objForm.getElementsByTagName('input')[i];
        if(objLoopedInput.type != 'submit') {
            if(objLoopedInput.type === 'checkbox') {
                arrFormData[objLoopedInput.name] = objLoopedInput.checked;
            }
            else {
                arrFormData[objLoopedInput.name] = objLoopedInput.value;
            }
        }
    }
    customAjax(strController, strFormRequest, arrFormData, fcnCallback);

};

var reloadCallback = function(objResponse) {
    if(objResponse.success) {
        location.reload();
    }
    else {
        showFeedback(objResponse);
    }
};

var showFeedback = function(objResponse) {
    var strMessage = objResponse.message;
    if(objResponse.attempts) {
        strMessage = strMessage + objResponse.attempts;
    }
    alert(strMessage);
//        console.log(objResponse);
};