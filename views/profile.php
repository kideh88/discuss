<?php
$strUsername = $_GET['user'];
$arrProfileData = $objUserController->getUserProfile($strUsername);
$blnSameUser = false;
if($_SESSION['blnLoggedIn']) {
    $blnSameUser = ($arrProfileData['intUserId'] === $_SESSION['intUserId']);
}
?>
<?php if($blnSameUser && $_GET['edit']): ?>
    <div class="col-md-6">
        <h2>Edit Profile</h2>
        <form method="post" onsubmit="sendFormData(event, reloadCallback)" data-type="User" data-action="doUpdateProfile" >
            <div class="form-group">
                <label for="profileImage">User Image</label>
                <input type="file" class="form-control" id="profileImage" name="arrImageFile">
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
        <a href="#"><button type="button" class="btn btn-primary pull-right join">Start private chat</button></a>
        <? endif; ?>

    </div>
<? endif; ?>
