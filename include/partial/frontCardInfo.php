<?php
return '
<h4 class="mb-3">'.__('Payment information', 'ntpRp').'</h4>
<div class="row">
    <div class="col-md-6 mb-3">
        <label for="cc-name">Name on card</label>
        <input type="text" class="form-control" id="cc-name" placeholder="" required>
        <small class="text-muted">Full name as displayed on card</small>
        <div class="invalid-feedback">
            Name on card is required
        </div>
    </div>
    <div class="col-md-6 mb-3">
        <label for="cc-number">Credit card number</label>
        <input type="text" class="form-control" id="cc-number" placeholder="" required>
        <div class="invalid-feedback">
            Credit card number is required
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-3 mb-3">
        <label for="cc-expiration-month">'.__('Expiration Month','ntpRp').'</label>
        <input type="text" class="form-control" id="cc-expiration-month" placeholder="" required>
        <div class="invalid-feedback">
            Expiration date required
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <label for="cc-expiration-year">'.__('Expiration Year','ntpRp').'</label>
        <input type="text" class="form-control" id="cc-expiration-year" placeholder="" required>
        <div class="invalid-feedback">
            Expiration date required
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <label for="cc-expiration">CVV</label>
        <input type="text" class="form-control" id="cc-cvv" placeholder="" required>
        <div class="invalid-feedback">
            Security code required
        </div>
    </div>
    <div class="col-md-3 mb-3">
        &nbsp;
    </div>
</div>
';
?>