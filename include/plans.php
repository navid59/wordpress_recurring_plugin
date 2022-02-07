<div class="">
    <div>
      <table id="dtBasicExample" class="table" width="100%">
        <thead>
          <tr>
            <th>Plan ID</th>
            <th>Title</th>
            <th>Amount</th>
            <th>Description</th>
            <th>Frequency</th>
            <th>Grace Period</th>
            <th>Initial Paymen</th>
            <th>CreatedAt</th>
          </tr>
        </thead>
        <tbody>
          <?php include_once('planGetData.php')?>
        </tbody>
        <tfoot>
          <tr>
            <th>Plan ID</th>
            <th>Title</th>
            <th>Amount</th>
            <th>Description</th>
            <th>Frequency</th>
            <th>Grace Period</th>
            <th>Initial Paymen</th>
            <th>CreatedAt</th>
          </tr>
        </tfoot>
      </table>
    </div>
</div>