<div class="jumbotron">
    <form id="recurring-plan-form" method="post" class="was-validated">
        <div class="form-group">
            <label for="planTitile">Plan Title</label>
            <input type="text" class="form-control" id="planTitile" placeholder="<?php echo __('Your plan title', 'ntpRp');?>" required>
            <div class="valid-feedback"><?php echo __('Valid plan title.','ntpRp'); ?></div>
            <div class="invalid-feedback"><?php echo __('Please fill out plan title.','ntpRp'); ?></div>
        </div>
        <div class="form-group">
            <label for="planDescription">Plan Description</label>
            <textarea class="form-control" id="planDescription" rows="3" placeholder="<?php echo __('The short description of Plan', 'ntpRp');?>" required></textarea>
            <div class="valid-feedback"><?php echo __('Valid plan description.','ntpRp'); ?></div>
            <div class="invalid-feedback"><?php echo __('Please fill out plan description.','ntpRp'); ?></div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="RecurrenceType"><?php echo __('Recurrence Type','ntpRp'); ?></label>
                <select id="RecurrenceType" class="form-control" required>
                    <option value=""><?php echo __('Choose...','ntpRp'); ?></option>
                    <option value="Dynamic" selected><?php echo __('Dynamic','ntpRp'); ?></option>
                    <option value="Fix"><?php echo __('Fix','ntpRp'); ?></option>
                </select>
                <div class="valid-feedback"><?php echo __('Valid recurrence type.','ntpRp'); ?></div>
                <div class="invalid-feedback"><?php echo __('Please choose recurrence type.','ntpRp'); ?></div>
            </div>
            <div class="form-group col-md-4">
                <label for="FrequencyType"><?php echo __('Frequency Type','ntpRp'); ?></label>
                <select id="FrequencyType" class="form-control" required>
                    <option value=""><?php echo __('Choose...','ntpRp'); ?></option>
                    <option value="Day"><?php echo __('Daily','ntpRp'); ?></option>
                    <option value="Month" selected><?php echo __('Monthly','ntpRp'); ?></option>
                    <option value="Year"><?php echo __('Yearly','ntpRp'); ?></option>
                </select>
                <div class="valid-feedback"><?php echo __('Valid frequency type.','ntpRp'); ?></div>
                <div class="invalid-feedback"><?php echo __('Please choose frequency type.','ntpRp'); ?></div>
            </div>
            <div class="form-group col-md-4">
                <label for="FrequencyValue"><?php echo __('Frequency Value','ntpRp'); ?></label>
                <input type="text" class="form-control" id="FrequencyValue" placeholder="<?php echo __('Your plan Frequency Value', 'ntpRp');?>" required>
                <div class="valid-feedback"><?php echo __('Valid plan frequency value.','ntpRp'); ?></div>
                <div class="invalid-feedback"><?php echo __('Please fill out plan frequency value.','ntpRp'); ?></div>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-3">
                <label for="Amount"><?php echo __('Amount','ntpRp'); ?></label>
                <input type="text" class="form-control" id="Amount" placeholder="<?php echo __('The plan value', 'ntpRp');?>" required>
                <div class="valid-feedback"><?php echo __('Valid plan amount.','ntpRp'); ?></div>
                <div class="invalid-feedback"><?php echo __('Please fill out plan amount.','ntpRp'); ?></div>
            </div>
            <div class="form-group col-md-3">
                <label for="Currency"><?php echo __('Currency','ntpRp'); ?></label>
                <select id="Currency" class="form-control" required>
                    <option value=""><?php echo __('Choose...','ntpRp'); ?></option>
                    <option value="RON" selected><?php echo __('RON','ntpRp'); ?></option>
                </select>
                <div class="valid-feedback"><?php echo __('Valid Currency.','ntpRp'); ?></div>
                <div class="invalid-feedback"><?php echo __('Please choose Currency.','ntpRp'); ?></div>
            </div>
            <div class="form-group col-md-3">
                <label for="GracePeriod"><?php echo __('Grace Period','ntpRp'); ?></label>
                <input type="text" class="form-control" id="GracePeriod" placeholder="<?php echo __('The grace period', 'ntpRp');?>" required>
                <div class="valid-feedback"><?php echo __('Valid plan grace period.','ntpRp'); ?></div>
                <div class="invalid-feedback"><?php echo __('Please fill out plan grace period.','ntpRp'); ?></div>
            </div>
            <div class="form-group col-md-3">
                <label for="InitialPayment"><?php echo __('InitialPayment','ntpRp'); ?></label><br>
                <p>
                    <input type="checkbox" class="form-control" id="InitialPayment" value="true">
                    <?php echo __('check if payment at subscription time, is mandatory.','ntpRp');?>
                </p>
            </div>
        </div>
        <hr>
        <div class="form-row">
            <div class="form-group col-md-4">
                <button type="submit" id="addPlan" class="btn btn-primary"><?php echo __('Submmit', 'ntpRp');?></button>
            </div>
            <div class="form-group col-md-8">
                <div class="alert alert-dismissible fade" id="msgBlock" role="alert">
                    <strong id="alertTitle">!</strong> <span id="msgContent"></span>.
                </div>
            </div>
        </div>
    </form>
</div>