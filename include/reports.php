<div class="">
    <div>
      <table id="dtBasicExample" class="table" width="100%">
        <thead>
          <tr>
            <th>Report ID</th>
            <th>TransactionID</th>
            <th>PaymentComment</th>
            <th>Label</th>
            <th>Status</th>
            <th>CreatedAt</th>
          </tr>
        </thead>
        <tbody>
          <?php include_once('reportGetData.php')?>
        </tbody>
        <tfoot>
          <tr>
            <th>Report ID</th>
            <th>TransactionID</th>
            <th>PaymentComment</th>
            <th>Label</th>
            <th>Status</th>
            <th>CreatedAt</th>
          </tr>
        </tfoot>
      </table>
    </div>
</div>