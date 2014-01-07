<?php
if(isset($_GET["userid"])){
    $user = "get user by id";
} else {
    header( 'Location: index.php?page=404' );
}
?>
<div class="col-md-4">
    <h2>Username</h2>
    <?php // if no picture ?>
    <img src="img/default.jpg" />
</div>
<div class="col-md-8">
    <h2>Userinfo</h2>
    Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.
    <h2>Private chats</h2>
    <?php
    // If no private chats
    echo "This user has no private chats"
    ?>
    <a href="index.php?page=chat&amp;chatobject=null"><button type="button" class="btn btn-primary pull-right join">Start private chat</button></a>


</div>