<?php
$a = new recurringAdmin();
$arrayData = $a->getPlanList();

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
            // printf("<td>%1s</td>",$plan['Id']);
            printf("<td>%1s</td>",$plan['Title']);
            printf("<td>%1s %2s</td>",$plan['Amount'],$plan['Currency']);
            printf("<td>%1s</td>",$plan['Description']);
            printf("<td>%1s / %2s</td>",$plan['Frequency_Type'], $plan['Frequency_Value']);
            printf("<td>%1s</td>",$plan['Grace_Period']);
            printf("<td>%1s</td>",$plan['Initial_Paymen'] === 'true' ? "Yes" : "No");
            $date = new DateTime($plan['CreatedAt']);
            printf("<td>%1s</td>",$date->format('Y-m-d'));
            printf('<td><button type="button" class="btn btn-success" onclick="copyPlan('.$plan['Plan_Id'].',\''.$plan['Title'].'\')" style="margin-right:5px;" title="'.__('copy Short code','ntpRp').'"><i class="fa fa-code"></i></button>');
            printf('<button type="button" class="btn btn-secondary" onclick="editPlan('.$plan['Plan_Id'].')" style="margin-right:5px;" title="'.__('Edit plan','ntpRp').'"><i class="fa fa-pencil"></i></button>');
            printf('<button type="button" class="btn btn-danger" onclick="delPlan('.$plan['Plan_Id'].');"  title="'.__('Delete plan','ntpRp').'"><i class="fa fa-trash"></i></button></td>');
        echo "</tr>";
    }
}
?>