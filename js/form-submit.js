var sendFormData = function(objEvent, fcnCallback) {
    objEvent.preventDefault();
    var objForm = objEvent.target;
    var strFormRequest = objEvent.target.id;
    var arrFormData = {};
    for(var i = 0; i < objForm.getElementsByTagName('input').length; i += 1) {
        var objLoopedInput = objForm.getElementsByTagName('input')[i];
        if(objLoopedInput.type != 'submit') {
//            if(objLoopedInput.type === 'checkbox') {
//                arrFormData[objLoopedInput.name] = objLoopedInput.checked;
//            }
//            else {
                arrFormData[objLoopedInput.name] = objLoopedInput.value;
//            }
        }
    }
    customAjax('user', strFormRequest, arrFormData, fcnCallback);

};