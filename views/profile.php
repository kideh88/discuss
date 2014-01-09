<?php
$strUsername = $_GET['user'];
$arrProfileData = $objUserController->getUserProfile($strUsername);
$blnSameUser = false;
if($_SESSION['blnLoggedIn']) {
    $blnSameUser = ($arrProfileData['intUserId'] === $_SESSION['intUserId']);
    if($blnSameUser && isset($_POST['blnUpdateProfile'])) {
        $strUserText = UserInputHelper::clean($_POST['strText']);
        $arrParameters['strText'] = $strUserText;
        $arrUpdateData = $objUserController->doUpdateProfile($arrParameters);
        $arrProfileData = $objUserController->getUserProfile($strUsername);
    }
}
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
<textarea class="form-control" rows="4" cols="50" id="profileText" name="strText"><? echo $arrProfileData['strProfileText']?></textarea>
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
        <p>
            <?php
            // If no private chats
            echo "This user has no private chats"
            ?>
        </p>
        <?php if (!$blnSameUser && 0 !== $arrProfileData['intUserId']): ?>
        <button type="button" class="btn btn-primary pull-right join">Start private chat</button>
        <? endif; ?>

        <?php if($blnSameUser): ?>
        <a href="index.php?page=profile&user=<?php echo $_SESSION['strUsername'] ?>&edit=1"><button type="button" class="btn btn-primary pull-right join">Edit Profile</button></a>
        <? endif; ?>

    </div>
<? endif; ?>
