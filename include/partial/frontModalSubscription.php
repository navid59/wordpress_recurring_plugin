<?php
$userInfo = include_once('frontMemberInfo.php');
$authInfo = include_once('frontAuthInfo.php');
$cardInfo = include_once('frontCardInfo.php');
echo '
<!-- Modal -->
<div class="modal fade" id="recurringModal_'.$planId.'" tabindex="-1" aria-labelledby="recurringModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <img src="https://suport.mobilpay.ro/np-logo-blue.svg" width="100" style="padding: 5px 15px 0px 0px;">
                <h2 class="modal-title" id="recurringModalLabel">'.$modalTitle.'</h2>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">            
                <div class="row">
                    <div class="col-md-12 order-md-1">
                    <h4 class="mb-3">'.__('Subscription detail','ntpRp').'</h4>
                    <form id="subscription-form" class="needs-validation">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="custom-control custom-checkbox">
                                    <h3><b>'.$planData['Title'].'</b></h3>
                                    <h4>'.$planData['Description'].'</h4>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="card mb-4 box-shadow">
                                    <div class="card-header">
                                        <h4 class="my-0 font-weight-normal">'.__('Amount','ntpRp').'</h4>
                                    </div>
                                    <div class="card-body">
                                        <h3 class="card-title pricing-card-title">'.$planData['Amount'].' '.$planData['Currency'].' <small class="text-muted">/ '.$planData['Frequency']['Value'].' '.$planData['Frequency']['Type'].'</small></h3>
                                        <input type="hidden" class="form-control" id="planID" value="'.$planId.'">
                                    </div>
                                </div>
                            </div>
                        </div>
                        '.
                        $userInfo
                        .
                        $authInfo
                        .'
                        <hr class="mb-4">
                        '.
                        $cardInfo
                        .'
                        <hr class="mb-4">
                        <button id="addSubscriptionButton" class="btn btn-primary btn-lg btn-block" type="submit">Continue to checkout</button>
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
                '.__('Supported by NETOPIA Payments','ntpRp').'
            </div>
        </div>
    </div>
</div>';
?>