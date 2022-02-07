<?php
$a = new recurringAdmin();
$arrayData = $a->getReportList();


if(isset($arrayData['code']) && ($arrayData['code'] == 11 || $arrayData['code'] == 12)) {
    echo '
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
    <h2 class="alert-heading">'.__('Error! ','ntpRp').'</h2>
    <strong>'.__('Something is wrong, please check your configurations','ntpRp').'</strong> '.'
    <hr>
  <p class="mb-0"><strong>'.__('Details: ','ntpRp').'</strong>'.$arrayData['message'].'</p>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    </div>';
} else {
    foreach($arrayData['paymentHistory']  as $payArchive) {
        echo "<tr>";
            printf("<td>%1s</td>",$payArchive['PaymentLogID']);
            printf("<td>%1s</td>",$payArchive['TransactionID']);
            printf("<td>%1s</td>",$payArchive['PaymentComment']);
            printf("<td>%1s</td>",$payArchive['Label']);
            printf("<td>%1s</td>",$a->getStatusStr('report',$payArchive['Status']));
            printf("<td>%1s</td>",$payArchive['CreatedAt']);
        echo "</tr>";
    }
}
?>