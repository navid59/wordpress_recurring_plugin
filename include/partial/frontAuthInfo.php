<?php
if($isLoggedIn) {
    return null;
} else {
return '
<hr class="mb-4">
<h4 class="mb-3">'.__('Auth information').'</h4>
<div class="row">
    <div class="col-md-6 mb-3">
        <label for="username">'.__('Username','ntpRp').'</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text">@</span>
            </div>
            <input type="text" class="form-control" id="username" placeholder="Username" value="'.$current_user->user_login.'" required>
            <div class="invalid-feedback" style="width: 100%;">
            '.__('Your username is required.','ntpRp').'
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-3">
        <label for="password">'.__('Password','ntpRp').'</label>
        <input type="password" class="form-control" id="password" required>
        <div class="invalid-feedback">
            '.__('Please enter a valid password.','ntpRp').'
        </div>
    </div>
</div>
';
}
?>