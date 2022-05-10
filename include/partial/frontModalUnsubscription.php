<?php
echo '
<!-- Unsubscription Modal -->
<div class="modal fade" id="unsubscriptionRecurringModal_'.$planId.'" tabindex="-1" aria-labelledby="unsubscriptionRecurringModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <img src="https://suport.mobilpay.ro/np-logo-blue.svg" width="100" style="padding: 5px 15px 0px 0px;">
                <h2 class="modal-title" id="unsubscriptionRecurringModalLabel">'.$unsubscriptionTitle.'</h2>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">            
                <div class="row">
                    <div class="col-md-12 order-md-1">
                        <form id="unsubscription-form" class="needs-validation">
                            '.__('Are you sure to unsubscribe from ','ntpRp').'
                            '.$planData['Title'].' !?
                            <br>
                            '.__('To unsubscribe click on unsubscribe button.','ntpRp').' '.__('Otherwise close the window','ntpRp').'
                            <hr>
                            <input type="hidden" class="form-control" id="Id" value="'.$subscription[0]->id.'" readonly>
                            <input type="hidden" class="form-control" id="Subscription_Id" value="'.$subscription[0]->Subscription_Id.'" readonly>
                            <button id="unsubscriptionButton" class="btn btn-secondary" type="button" onclick="unsubscription(); return false;">Unsubscribe</button>
                        </form>
                    </div>
                </div>
                <div id="loading" class="d-flex align-items-center fade">
                    <strong>'.__('Loading...','ntpRp').'</strong>
                    <div class="spinner-border ml-auto" role="status" aria-hidden="true"></div>
                </div>
                <div class="alert alert-dismissible fade" id="msgBlock" role="alert">
                    <strong id="alertTitle">!</strong> <span id="msgContent"></span>.
                </div>                                
            </div>
            <div class="modal-footer">
                '.__('Supported by NETOPIA Payments').'
            </div>
        </div>
    </div>
</div>
';
?>