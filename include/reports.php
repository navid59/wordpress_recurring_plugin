<div class="">
    <div>
      <table id="reportDtBasic" class="table" width="100%">
        <thead>
          <tr>
            <th>Report ID</th>
            <th>User</th>
            <th>Plan</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Date</th>
            <!-- <th>PaymentComment</th> -->
            <!--<th>TransactionID</th> -->
          </tr>
        </thead>
        <tbody>
          <?php include_once('reportGetData.php')?>
        </tbody>
        <tfoot>
          <tr>
            <th>Report ID</th>
            <th>User</th>
            <th>Plan</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Date</th>
            <!-- <th>PaymentComment</th> -->
            <!--<th>TransactionID</th> -->
          </tr>
        </tfoot>
      </table>
    </div>
</div>