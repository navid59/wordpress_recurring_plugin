<div class="">
    <div>
      <table id="dtBasicExample" class="table" width="100%">
        <thead>
          <tr>
            <th><?php echo __('First Name','ntpRp');?></th>
            <th><?php echo __('Last Name','ntpRp');?></th>
            <th><?php echo __('Email','ntpRp');?></th>
            <th><?php echo __('Phone','ntpRp');?></th>
            <th><?php echo __('UserID','ntpRp');?></th>
            <th><?php echo __('Plan','ntpRp');?></th>
            <th><?php echo __('Amount','ntpRp');?></th>
            <th><?php echo __('Status','ntpRp');?></th>
            <th><?php echo __('Start At','ntpRp');?></th>
            <th><?php echo __('Action','ntpRp');?></th>
          </tr>
        </thead>
        <tbody>
          <?php include_once('subscriptionGetData.php')?>
        </tbody>
        <tfoot>
          <tr>
            <th><?php echo __('First Name','ntpRp');?></th>
            <th><?php echo __('Last Name','ntpRp');?></th>
            <th><?php echo __('Email','ntpRp');?></th>
            <th><?php echo __('Phone','ntpRp');?></th>
            <th><?php echo __('UserID','ntpRp');?></th>
            <th><?php echo __('Plan','ntpRp');?></th>
            <th><?php echo __('Amount','ntpRp');?></th>
            <th><?php echo __('Status','ntpRp');?></th>
            <th><?php echo __('Start At','ntpRp');?></th>
            <th><?php echo __('Action','ntpRp');?></th>
          </tr>
        </tfoot>
      </table>
    </div>
</div>