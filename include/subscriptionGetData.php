<?php
$a = new recurringAdmin();
$arrayData = $a->getSubscriptionList();
// echo "<pre>";
// var_dump($this);
// echo "</pre>";
// die(1);

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
    foreach($arrayData['members']  as $subscription) {
        echo "<tr>";
            printf("<td>%1s</td>",$subscription['Id']);
            printf("<td>%1s %2s</td>",$subscription['Member']['Name'],$subscription['Member']['LastName']);
            printf("<td>%1s</td>",$subscription['Member']['Email']);
            printf("<td>%1s</td>",$subscription['Member']['UserID']);
            printf("<td>%1s</td>",$subscription['Plan']['PlanName']);
            printf("<td>%1s</td>",$subscription['NextPaymentDate']);
            printf("<td>%1s</td>",$subscription['Status']);
            printf("<td>%1s</td>",$subscription['Plan']['StartDate']);
        echo "</tr>";
    }
}
?>