
var assignJoinClickEvents = function(strClassName) {
    var objClickElements = document.getElementsByClassName(strClassName);
    for(var i = 0; i < objClickElements.length; i += 1) {
        addEvent(objClickElements[i], 'click', function(objEvent){
            var objElement = objEvent.target;
            if(objElement.nodeName === 'TD') {
                objElement = objElement.parentNode;
            }
            var intChatId = objElement.getAttribute('data-chat-id');
            document.location = 'index.php?page=chat&chat=' + intChatId;
        });
    }
};

var redirectToPrivate = function(objResponse) {
    if(objResponse.success) {
        var intChatId = objResponse.intChatId;
        document.location = 'index.php?page=chat&chat=' + intChatId;
    }
    else {
        showFeedback(objResponse);
    }

};

var updateActiveChats = function(objActiveChatsList, arrActiveChats) {
    objActiveChatsList.innerHTML = '';
    document.getElementById('countActiveChats').innerHTML = '(' + arrActiveChats.length + ')';
    var strHtml = '';
    for(var i = 0; i < arrActiveChats.length; i += 1) {
        strHtml += '<tr class="joinRoom" data-chat-id="' + arrActiveChats[i].intChatId + '">';
        strHtml += '<td>' + arrActiveChats[i].strRoomName + '</td>';
        strHtml += '<td>' + arrActiveChats[i].intUsers + '</td></tr>';
    }
    objActiveChatsList.innerHTML = strHtml;
    assignJoinClickEvents('joinRoom');
};

var updateChatPage = function() {
    var objActiveChatsList = document.getElementById('activeChatsList');
    var objChatList = document.getElementById('chatMessageList');

    customAjax('Chat', 'getPublicChatList', {}, function(objResponse) {
        if(objResponse.success) {
            updateActiveChats(objActiveChatsList, objResponse.arrPublicChats);
        }
    });
    customAjax('Chat', 'updateChatLog', {"intChatId" : intChatRoomId, "intLastMessage" : intLastMessageId}, function(objResponse) {
        if(objResponse.success) {
            updateChatMessages(objChatList, objResponse.arrChatMessages);
        }
    });
};

var updateChatMessages = function(objChatList, objChatMessages) {
    for(var i = 0; i < objChatMessages.length; i += 1) {
        var objListElement = document.createElement('li');
        objListElement.id = objChatMessages[i].id;

        var objTimeSpanElement = document.createElement('span');
        objTimeSpanElement.className = 'chat-timestamp';
        var objDate = new Date(objChatMessages[i].intTime*1000);
        var intHours = objDate.getHours();
        var intMinutes = objDate.getMinutes();
        var intSeconds = objDate.getSeconds();
        intHours = ((intHours < 10) ? ('0' + intHours) : intHours);
        intMinutes = ((intMinutes < 10) ? ('0' + intMinutes) : intMinutes);
        intSeconds = ((intSeconds < 10) ? ('0' + intSeconds) : intSeconds);
        objTimeSpanElement.innerHTML = intHours + ':' + intMinutes + ':' + intSeconds;
        objListElement.appendChild(objTimeSpanElement);

        var objNameSpanElement = document.createElement('span');
        objNameSpanElement.className = 'chat-username';
        objNameSpanElement.innerHTML = objChatMessages[i].strUsername;
        objListElement.appendChild(objNameSpanElement);

        var objTextSpanElement = document.createElement('span');
        objTextSpanElement.className = 'chat-message';
        objTextSpanElement.innerHTML = objChatMessages[i].strMessage;
        objListElement.appendChild(objTextSpanElement);

        objChatList.appendChild(objListElement);
        var objFrame = objChatList.parentNode;
        objFrame.scrollTop = objFrame.scrollHeight;
    }
    intLastMessageId = parseInt(objChatMessages[objChatMessages.length-1].intMessageId);
};

$( document ).ready(function() {
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

    var objButtonLeaveRoom = document.getElementsByClassName('leaveRoom');
    if(objButtonLeaveRoom) {
        for(var i = 0; i < objButtonLeaveRoom.length; i += 1) {
            addEvent(objButtonLeaveRoom[i], 'click', function(objEvent){
                var intChatId = parseInt(objEvent.target.getAttribute('data-chat-id'));
                customAjax('Chat', 'doLeaveRoom', {"intChatId" : intChatId}, reloadCallback);
            });
        }
    }
    var objButtonPrivateRoom = document.getElementsByClassName('joinPrivate');
    if(objButtonPrivateRoom) {
        for(var i = 0; i < objButtonPrivateRoom.length; i += 1) {
            addEvent(objButtonPrivateRoom[i], 'click', function(objEvent){
                var intInvitedId = parseInt(objEvent.target.parentNode.getAttribute('data-user-id'));
                customAjax('Chat', 'doJoinPrivateRoom', {"intUserId" : intInvitedId}, redirectToPrivate);
            });
        }
    }

    var objProfileEditor = $('#profileEditor');
    if(objProfileEditor) {
        objProfileEditor.sceditor({
            plugins: "xhtml",
            style: "css/sceditor/jquery.sceditor.default.min.css",
            toolbarExclude: "cut,copy,paste,pastetext,youtube,ltr,rtl",
            emoticonsRoot: "images/sceditor/",
            height: 200
        });
    }
    var objChatEditor = $('#chatEditor');
    if(objChatEditor) {
        objChatEditor.sceditor({
            plugins: "xhtml",
            style: "css/sceditor/jquery.sceditor.default.min.css",
            toolbar: "bold,italic,underline",
            emoticonsRoot: "images/sceditor/",
            width: 500,
            height: 100
        });
    }

});
