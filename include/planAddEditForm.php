<div class="jumbotron">
    <form id="recurring-plan-form" method="post">
        <div class="form-group">
            <label for="planTitile">Plan Title</label>
            <input type="text" class="form-control" id="planTitile" placeholder="<?php echo __('Your plan title', 'ntpRp');?>">
        </div>
        <div class="form-group">
            <label for="planDescription">Plan Description</label>
            <textarea class="form-control" id="planDescription" rows="3" placeholder="<?php echo __('The short description of Plan', 'ntpRp');?>"></textarea>
        </div>
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="RecurrenceType"><?php echo __('Recurrence Type','ntpRp'); ?></label>
                <select id="RecurrenceType" class="form-control">
                    <option selected><?php echo __('Choose...','ntpRp'); ?></option>
                    <option value="Dynamic"><?php echo __('Dynamic','ntpRp'); ?></option>
                    <option value="Fix"><?php echo __('Fix','ntpRp'); ?></option>
                </select>
            </div>
            <div class="form-group col-md-4">
                <label for="FrequencyType"><?php echo __('Frequency Type','ntpRp'); ?></label>
                <select id="FrequencyType" class="form-control">
                    <option selected><?php echo __('Choose...','ntpRp'); ?></option>
                    <option value="Day"><?php echo __('Daily','ntpRp'); ?></option>
                    <option value="Month"><?php echo __('Monthly','ntpRp'); ?></option>
                    <option value="Year"><?php echo __('Yearly','ntpRp'); ?></option>
                </select>
            </div>
            <div class="form-group col-md-4">
                <label for="FrequencyValue"><?php echo __('Frequency Value','ntpRp'); ?></label>
                <input type="text" class="form-control" id="FrequencyValue" placeholder="<?php echo __('Your plan Frequency Value', 'ntpRp');?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="Amount"><?php echo __('Amount','ntpRp'); ?></label>
                <input type="text" class="form-control" id="Amount" placeholder="<?php echo __('The plan value', 'ntpRp');?>">
            </div>
            <div class="form-group col-md-4">
                <label for="Currency"><?php echo __('Currency','ntpRp'); ?></label>
                <select id="Currency" class="form-control">
                    <option selected><?php echo __('Choose...','ntpRp'); ?></option>
                    <option value="RON"><?php echo __('RON','ntpRp'); ?></option>
                </select>
            </div>
            <div class="form-group col-md-4">
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
                <button type="submit" class="btn btn-primary"><?php echo __('Submmit', 'ntpRp');?></button>
            </div>
            <div class="form-group col-md-8">
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <strong>Holy guacamole!</strong> You should check in on some of those fields below.
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Holy guacamole!</strong> You should check in on some of those fields below.
                    <button type="submit" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>