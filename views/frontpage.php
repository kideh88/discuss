<div class="col-md-6">
    <h2>Sign in</h2>
    <form method="post" onsubmit="sendFormData(event, reloadCallback)" data-type="User" data-action="doUserLogin" >
        <div class="form-group">
            <label for="LoginUsername">Username</label>
            <input type="text" required class="form-control" id="LoginUsername" name="strUsername" placeholder="Username">
        </div>
        <div class="form-group">
            <label for="LoginPassword">Password</label>
            <input type="password" required class="form-control" id="LoginPassword" name="strPassword" placeholder="Password">
        </div>
        <div class="checkbox">
            <label>
                <input type="checkbox" name="blnRemember" > Remember me
            </label>
        </div>
        <button type="submit" class="btn btn-primary">Login</button>
    </form>
</div>
<div class="col-md-6">
    <h2>Sign up</h2>
    <form method="post" onsubmit="sendFormData(event, showFeedback)" data-type="User" data-action="doUserRegister" >
        <div class="form-group">
            <label for="RegisterEmail">Email address</label>
            <input type="email" required class="form-control" id="RegisterEmail" name="strEmail" placeholder="Email">
        </div>
        <div class="form-group">
            <label for="RegisterUsername">Username</label>
            <input type="text" required class="form-control" id="RegisterUsername" name="strUsername" placeholder="Username">
        </div>
        <div class="form-group">
            <label for="RegisterPassword">Password</label>
            <input type="password" required class="form-control" id="RegisterPassword" name="strPassword" placeholder="Password">
        </div>
        <div class="form-group">
            <label for="RegisterConfirmPassword">Confirm Password</label>
            <input type="password" required class="form-control" id="RegisterConfirmPassword" name="strConfirmPassword" placeholder="Confirm Password">
        </div>
        <button type="submit" class="btn btn-primary">Register</button>
    </form>
    <span>Password meets requirements: <span id="result"></span></span>
        <div>
            <div id="lowercase">At least 2 lowercase characters</div>
            <div id="uppercase">At least 2 uppercase characters</div>
            <div id="numbers">At least 2 numbers</div>
            <div id="specialchars">At least 2 special characters</div>
            <div id="length">Password should be minimum 8 characters</div>
        </div>
</div>