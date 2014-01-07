<div class="col-md-4">
    <h2>Online users</h2>
    <?php
        // Insert online users
    ?>
    <table class="table">
        <thead>
            <tr><th>Name</th><th></th><th></th><th></th></tr>
        </thead>
        <tbody>
            <?php // foreach online user ?>
            <tr>
                <td><a href="index.php?page=profile&amp;userid=0">Mikkel</a><td>
                <td><a href="index.php?page=chat&amp;chatobject=null"><button type="button" class="btn btn-primary pull-right join">Start private chat</button></a><td>
            </tr>
            <?php // end foreach; ?>
        </tbody>
    </table>
</div>
<div class="col-md-8">
    <h2>Public chats</h2>
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
            <tr>
                <td>Dummy</td>
                <td>4</td>
                <td>Emrah, Kim, Johnny, Mikkel<td>
                <td><a href="index.php?page=chat&amp;chatobject=null"><button type="button" class="btn btn-primary pull-right join">Join</button></a><td>
            </tr>
        </tbody>
    </table>
    <?php // if admin:?>
        <a href="index.php?page=chat&amp;chatobject=null"><button type="button" class="btn btn-primary pull-right join">Start new public chat</button></a>
    <?php //endif ?>
    <div class="clearfix"></div>
    <h2>Private chats</h2>
    <?php
    // If no private chats
    echo "You have no private chats"
    ?>


</div>