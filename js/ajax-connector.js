function customAjax(strController, strMethod, objParams, callback) {
    var xmlHttp;

    if(window.XMLHttpRequest) {
        // code for IE7+, Firefox, Chrome, Opera, Safari
        xmlHttp = new XMLHttpRequest();
    }
    else {
        // code for IE6, IE5
        xmlHttp = new ActiveXObject('Microsoft.XMLHTTP');
    }
    xmlHttp.onreadystatechange = function() {
        if(4 == xmlHttp.readyState && 200 == xmlHttp.status && '' != xmlHttp.responseText) {
            try {
                xmlHttp.responseJSON = JSON.parse(xmlHttp.responseText);
            }
            catch(objException) {
                xmlHttp.responseJSON = {
                    "success" : false
                    , "message": "JSON Parse error " + objException.message
                };
            }
            if(callback) {
                callback(xmlHttp.responseJSON);
            }
            else {
                return xmlHttp.responseJSON;
            }
        }
        return false;
    };
    xmlHttp.open('POST', 'helpers/Bridge.helper.php', true);
    xmlHttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    var jsonString = "jsonString=" + JSON.stringify({
        "controller" : strController
        , "method" : strMethod
        , "parameters" : objParams
    });
    xmlHttp.send(jsonString);
}