<?php
$intChatId = (int)$_GET["chat"];
$arrChatAccess = $objChatController->checkValidChat($intChatId);
if($arrChatAccess['success']) {
    $intValidChatId = $intChatId;
}
?>
<div class="col-md-4">
    <h2>Active chats <span id="countActiveChats"></span></h2>
    <table class="table chatlist">
        <thead>
            <tr>
                <th>Name</th>
                <th>Number of users</th>
            </tr>
        </thead>
        <tbody id="activeChatsList">
        </tbody>
    </table>
    <a href="index.php?page=lobby"><button type="button" class="btn btn-primary pull-right join">Back to lobby</button></a>
</div>
<div class="col-md-8">
    <h2>Chatwindow</h2>
    <?php if($arrChatAccess['success']): ?>
    <div id="chatWindow">
        <ul id="chatMessageList">

        </ul>
    </div>
    <form method="post" onsubmit="sendFormData(event, showResponse)" data-type="Chat" data-action="sendChatMessage" >
        <div class="form-group">
            <textarea id="chatEditor" name="strChatMessage"></textarea>
            <input type="hidden" name="strChatId" value="<?php echo $intValidChatId; ?>" >
        </div>
        <button type="submit" class="btn btn-primary">Send</button>
    </form>



    <script type="text/javascript">
        var intChatRoomId = <?php echo $intValidChatId; ?>;
        var intLastMessageId = 0;
        setInterval(function() {
            updateChatPage();
        }, 500);
    </script>
<?php else: ?>
    <p><? echo $arrChatAccess['strMessage'] ?></p>
<?php endif; ?>
</div>