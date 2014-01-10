<?php
$strUsername = $_GET['user'];
$arrProfileData = $objUserController->getUserProfile($strUsername);
$blnSameUser = false;
if($_SESSION['blnLoggedIn']) {
    $blnSameUser = ($arrProfileData['intUserId'] === $_SESSION['intUserId']);
    if($blnSameUser && isset($_POST['blnUpdateProfile'])) {
        $strUserText = UserInputHelper::cleanUserInput($_POST['strText'], 'profile');
        $arrParameters['strText'] = $strUserText;
        $arrUpdateData = $objUserController->doUpdateProfile($arrParameters);
        $arrProfileData = $objUserController->getUserProfile($strUsername);
    }
}
$arrPrivateChatList = $objChatController->getPrivateChatList();
$arrPrivateChatList = $arrPrivateChatList['arrPrivateChats'];
?>
<?php if($blnSameUser && $_GET['edit']): ?>
    <div class="col-md-6">
        <h2>Edit Profile</h2>
        <form enctype="multipart/form-data" method="post" action="index.php?page=profile&user=<?php echo $_SESSION['strUsername'] ?>&edit=1" >
            <div class="form-group">
                <div id="editprofileImageLeft">
                    <label for="profileImage">User Image</label>
                    <input type="file" class="form-control" id="profileImage" name="image" >
                </div>
                <input type="hidden" name="blnUpdateProfile" value="1" />
                <img src="images/users/<? echo $arrProfileData['strProfileImage']?>" id="editProfileImage" />
            </div>
            <div class="form-group">
                <label for="profileText">About you</label>
                <textarea class="form-control" id="profileEditor" rows="4" cols="50" name="strText"><? echo $arrProfileData['strProfileText']?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Save</button>
        </form>
    </div>
<? else: ?>
    <div class="col-md-4">
        <h2><? echo $arrProfileData['strUsername']?></h2>
        <img src="images/users/<? echo $arrProfileData['strProfileImage']?>" />
    </div>
    <div class="col-md-8">
        <h2>Userinfo</h2>
        <p><? echo $arrProfileData['strProfileText']?></p>
        <h2>Private chats</h2>
        <?php if(!$_SESSION['blnLoggedIn']): ?>
            <p>You need to log in to see private chats</p>
        <? else: ?>
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
                <?php foreach($arrPrivateChatList as $arrPrivateRoom): ?>
                    <?php if($arrPrivateRoom['blnIsMember'] === true): ?>
                        <tr>
                            <td><? echo $arrPrivateRoom['strRoomName']?></td>
                            <td><? echo $arrPrivateRoom['intUsers']?></td>
                            <td><? echo $arrPrivateRoom['strUsers']?><td>
                            <td>
                                <a href="#" class="leaveRoom"><button type="button" data-chat-id="<? echo $arrPrivateRoom['intChatId']?>" class="btn btn-primary pull-right join">Leave</button></a>
                                <a href="index.php?page=chat&amp;chat=<? echo $arrPrivateRoom['intChatId']?>"><button type="button" class="btn btn-primary pull-right join">Join</button></a>
                            <td>
                        </tr>
                    <? endif; ?>
                <? endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        <?php if (!$blnSameUser && 0 !== $arrProfileData['intUserId']): ?>
        <button type="button" class="btn btn-primary pull-right join">Start private chat</button>
        <? endif; ?>

        <?php if($blnSameUser): ?>
        <a href="index.php?page=profile&user=<?php echo $_SESSION['strUsername'] ?>&edit=1"><button type="button" class="btn btn-primary pull-right join">Edit Profile</button></a>
        <? endif; ?>

    </div>
<? endif; ?>
