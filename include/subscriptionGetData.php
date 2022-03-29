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
            $startDate = new DateTime($subscription['StartDate']);
            printf("<td>%1s - %2s - %3s - %4s<br> %5s <br> %6s</td>",$subscription['Title'],$subscription['Amount'],$startDate->format('Y-m-d'),'TMP TMP','TmpPlan 2- tmp Amount- here will disply up to 3 plan','TmpPlan 3- tmp Amount- here will disply up to 3 plan');
            printf("<td>%1s</td>",'Rand Count'.rand(1,3));
            // printf("<td>%1s</td>",$subscription['Amount']);
            printf("<td>%1s</td>",$a->getStatusStr('subscription',$subscription['status']));
            printf('<td><button type="button" class="btn btn-secondary" onclick="subscriptionHistory('.$subscription['Subscription_Id'].')" style="margin-right:5px;" title="'.__('Subscriber history','ntpRp').'"><i class="fa fa-history"></i></button>');
            printf('<button type="button" class="btn btn-success" onclick="subscriptionDetails(\''.$subscription['UserID'].'\')" style="margin-right:5px;"  title="'.__('Subscriber Info','ntpRp').'"><i class="fa fa-info"></i></button>');
           // printf('<button type="button" class="btn btn-info" onclick="subscriptionNextPayment('.$subscription['Subscription_Id'].',\''.$subscription['First_Name'].' '.$subscription['Last_Name'].'\')" title="'.__('Subscriber next payment','ntpRp').'"><i class="fa fa-credit-card"></i></button></td>');
        echo "</tr>";
    }
}
?>