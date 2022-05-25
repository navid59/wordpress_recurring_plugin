<?php
die();
if($isLoggedIn) {
    return null;
} else {
return '
<hr class="mb-4">
<h4 class="mb-3">'.__('Personal information').'</h4>
<div class="row">
    <div class="col-md-6 mb-3">
        <label for="firstName">'.__('First name','ntpRp').'</label>
        <input type="text" class="form-control" id="firstName" placeholder="" value="'.$current_user->first_name.'" required>
        <div class="valid-feedback">
        '.__('Looks good!').'
        </div>
        <div class="invalid-feedback">
            '.__('Valid first name is required.','ntpRp').'
        </div>
    </div>
    <div class="col-md-6 mb-3">
        <label for="lastName">'.__('Last name','ntpRp').'</label>
        <input type="text" class="form-control" id="lastName" placeholder="" value="'.$current_user->last_name.'" required>
        <div class="valid-feedback">
        '.__('Looks good!').'
        </div>
        <div class="invalid-feedback">
            '.__('Valid last name is required.','ntpRp').'
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8 mb-9">
        <label for="address">'.__('Address','ntpRp').'</label>
        <input type="text" class="form-control" id="address" placeholder="'.__('Subscription address, Ex. Main street, Floor, Nr,... ','ntpRp').'" required>
        <div class="invalid-feedback">
            '.__('Please enter your shipping address.','ntpRp').'
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <label for="email">'.__('Email','ntpRp').'</label>
        <input type="email" class="form-control" id="email" placeholder="you@example.com" value="'.$current_user->user_email.'" required>
        <div class="invalid-feedback">
            '.__('Please enter a valid email address for shipping updates.','ntpRp').'
        </div>
    </div>
</div>    

<div class="row">
    <div class="col-md-5 mb-3">
        <label for="tel">'.__('Tel','ntpRp').'</label>
        <input type="text" class="form-control" id="tel" placeholder="" required>
        <div class="invalid-feedback">
            '.__('Phone required.','ntpRp').'
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <label for="country">'.__('Country','ntpRp').'</label>
        <select class="custom-select d-block w-100" id="country" required>
        <option value="">Choose...</option>
        <option value="642">Romania</option>
        </select>
        <div class="invalid-feedback">
            '.__('Please select a valid country.','ntpRp').'
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <label for="state">'.__('State','ntpRp').'</label>
        <select class="custom-select d-block w-100" id="state" name="state" required>'
        .getJudete().
        '</select>
        <div class="invalid-feedback">
            '.__('Please provide a valid state.','ntpRp').'
        </div>
    </div>                        
</div>';
}
?>