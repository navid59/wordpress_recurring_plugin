<?php
$a = new recurringAdmin();
$arrayData = $a->getSubscriptionList();
if(isset($arrayData['code']) && ($arrayData['code'] == 11 || $arrayData['code'] == 12)) {
    echo '
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
    <h2 class="alert-heading">'.__('Error! ','ntpRp').'</h2>
    <strong>'.$arrayData['message'].'</strong> '.'
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    </div>';
} else {
    foreach($arrayData['members']  as $subscription) {
        echo "<tr>";
            printf("<td>%1s</td>",$subscription['First_Name']);
            printf("<td>%1s</td>",$subscription['Last_Name']);
            printf("<td>%1s</td>",$subscription['Email']);
            printf("<td>%1s</td>",$subscription['Tel']);
            printf("<td>%1s</td>",$subscription['UserID']);
            printf("<td>%1s</td>",$subscription['PlanId']);
            $nextPaymentDate = new DateTime($subscription['NextPaymentDate']); 
            printf("<td>%1s</td>",$nextPaymentDate->format('Y-m-d'));
            printf("<td>%1s</td>",$a->getStatusStr('subscription',$subscription['Status']));
            printf('<td><button type="button" class="btn btn-secondary" onclick="subscriptionHistory('.$subscription['Subscription_Id'].')" style="margin-right:5px;"><i class="fa fa-history"></i></button>');
            printf('<button type="button" class="btn btn-success" onclick="subscriptionDetails('.$subscription['Subscription_Id'].')" style="margin-right:5px;"><i class="fa fa-info"></i></button>');
            printf('<button type="button" class="btn btn-info" onclick="subscriptionNextPayment('.$subscription['Subscription_Id'].')"><i class="fa fa-credit-card"></i></button></td>');
        echo "</tr>";
    }
}
?>