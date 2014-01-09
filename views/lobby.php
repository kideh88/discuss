<?php
$arrUserList = $objUserController->getOnlineUsers();
$arrPublicChatList = $objChatController->getPublicChatList();
$arrPublicChatList = $arrPublicChatList['arrPublicChats'];
?>
<div class="col-md-4">

    <h2>Online users (<? echo count($arrUserList); ?>)</h2>

    <table class="table">
        <thead>
            <tr><th>Name</th><th></th><th></th><th></th></tr>
        </thead>
        <tbody>
            <?php foreach($arrUserList as $arrUser): ?>
            <tr>
                <td><a href="index.php?page=profile&amp;user=<? echo $arrUser['user_name']?>"><? echo $arrUser['user_name'] ?></a><td>
                <td><button type="button" class="btn btn-primary pull-right join">Start private chat</button><td>
            </tr>
            <? endforeach; ?>
        </tbody>
    </table>
</div>
<div class="col-md-8">
    <h2>Public chats (<? echo count($arrPublicChatList); ?>)</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Number of users</th>
                <th>Users<th>
                <th><th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($arrPublicChatList as $arrPublicRoom): ?>
            <tr>
                <td><? echo $arrPublicRoom['strRoomName']?></td>
                <td><? echo $arrPublicRoom['intUsers']?></td>
                <td><? echo $arrPublicRoom['strUsers']?><td>
                <td><a href="index.php?page=chat&amp;chat=<? echo $arrPublicRoom['intChatId']?>"><button type="button" class="btn btn-primary pull-right join">Join</button></a><td>
            </tr>
        <? endforeach; ?>
        </tbody>
    </table>
    <?php if($_SESSION['blnLoggedIn'] === true): ?>
        <div id="newPublicChatForm">
            <form method="post" onsubmit="sendFormData(event, showFeedback)" data-type="Chat" data-action="doStartPublicChat" >
                <div class="form-group">
                    <label for="roomName">Enter a chat room name:</label>
                    <input type="text" required class="form-control" id="roomName" name="strRoomName" placeholder="Name">
                </div>
                <button type="submit" class="btn btn-primary">Create</button>
            </form>
            <button type="button" id="hideChatForm" class="btn btn-primary pull-right join">Cancel</button>
        </div>
        <button type="button" id="showChatForm" class="btn btn-primary pull-right join">Start new public chat</button>
    <?php endif ?>
    <div class="clearfix"></div>
    <h2>Private chats</h2>
    <?php
    // If no private chats
    echo "You have no private chats"
    ?>


</div>