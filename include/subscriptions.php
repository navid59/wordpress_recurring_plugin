<div class="">
    <div>
      <table id="dtBasicExample" class="table" width="100%">
        <thead>
          <tr>
            <th>Full Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>UserID</th>
            <th>Plan</th>
            <th>Next Payment</th>
            <th>Status</th>
            <th>Start At</th>
            <th>History</th>
          </tr>
        </thead>
        <tbody>
          <?php include_once('subscriptionGetData.php')?>
        </tbody>
        <tfoot>
          <tr>
            <th>Full Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>UserID</th>
            <th>Plan</th>
            <th>Next Payment</th>
            <th>Status</th>
            <th>Start At</th>
            <th>History</th>
          </tr>
        </tfoot>
      </table>
    </div>
</div>