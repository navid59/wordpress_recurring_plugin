<?php
$a = new recurringAdmin();
$arrayData = $a->getPlanList();

// echo "<pre>";
// var_dump($arrayData);
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
    foreach($arrayData['plans']  as $plan) {
        echo "<tr>";
            printf("<td>%1s</td>",$plan['Id']);
            printf("<td>%1s</td>",$plan['Title']);
            printf("<td>%1s %2s</td>",$plan['Amount'],$plan['Currency']);
            printf("<td>%1s</td>",$plan['Description']);
            printf("<td>%1s / %2s</td>",$plan['Frequency']['Type'], $plan['Frequency']['Value']);
            printf("<td>%1s</td>",$plan['GracePeriod']);
            printf("<td>%1s</td>",$plan['InitialPayment'] ? "Yes" : "No");
            printf("<td>%1s</td>",$plan['CreatedAt']);
        echo "</tr>";
    }
}
?>