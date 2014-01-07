<?php
if (isset($_GET["chatobject"])) {
    $user = "get chatrooms by id";
} else {
    header('Location: index.php?page=404');
}
?>
<div class="col-md-4">
    <h2>Active chats</h2>
    <table class="table chatlist">
        <thead>
            <tr>
                <th>Name</th>
                <th>Number of users</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Dummychat 1</td>
                <td>4</td>
            </tr>
            <tr>
                <td>Dummychat 2</td>
                <td>2</td>
            </tr>
            <tr>
                <td>Dummychat 3</td>
                <td>50023</td>
            </tr>
        </tbody>
    </table>
    <a href="index.php?page=lobby">join more chats</a>
</div>
<div class="col-md-8">
    <h2>Chatwindow</h2>
    <?php // include chatroom with ajax ?>
    <div class="chatwindow">
        Dummy: Hey!<br>
        Dummy2: Hey!<br>
        Dummy: WAZZZUUUUUUUPPP!?!??!?!?!?!<br>
        Dummy2: Not much<br>
    </div>
    <textarea class="wysiwyg">WYSIWYG</textarea>
</div>