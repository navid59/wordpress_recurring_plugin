<div id="subscriberInfotModal" class="modal fade" tabindex="-1" aria-labelledby="recurringModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
              <h2 class="modal-title" id="nextPaymentModalLabel"><?php echo __('Subscriber Details','ntpRp');?></h2>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">            
              <h6><?php echo __('First Name', 'ntpRp');?> : <span class="font-weight-bold" id="SubscriberInfo_FirstaName"> - </span></h6>
              <h6><?php echo __('Last Name', 'ntpRp');?> : <span class="font-weight-bold" id="SubscriberInfo_LastName"> - </span></h6>
              <h6><?php echo __('User ID', 'ntpRp');?> : <span class="font-weight-bold" id="SubscriberInfo_UserId"> - </span></h6>
              <h6><?php echo __('Tel', 'ntpRp');?> : <span class="font-weight-bold" id="SubscriberInfo_Tel"> - </span></h6>
              <h6><?php echo __('Email', 'ntpRp');?> : <span class="font-weight-bold" id="SubscriberInfo_Email"> - </span></h6>
              <h6><?php echo __('Address', 'ntpRp');?> : <span class="font-weight-bold" id="SubscriberInfo_Address"> - </span></h6>
              <h6><?php echo __('City', 'ntpRp');?> : <span class="font-weight-bold" id="SubscriberInfo_City"> - </span></h6>
              <hr>
              <h6><?php echo __('Plan Title', 'ntpRp');?> : <span class="font-weight-bold" id="SubscriberInfo_PlanTitle"> - </span></h6>
              <h6><?php echo __('Amount', 'ntpRp');?> : <span class="font-weight-bold" id="SubscriberInfo_PlanAmount"> - </span></h6>
              <hr>
              <h6><?php echo __('Start date', 'ntpRp');?> : <span class="font-weight-bold" id="SubscriberInfo_StartDate"> - </span></h6>
              <h6><?php echo __('Status', 'ntpRp');?> : <span class="font-weight-bold" id="SubscriberInfo_Status"> - </span></h6>
              <div class="modal-footer">
                  <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
              </div>
              <div class="alert alert-dismissible fade" id="msgBlock" role="alert">
                  <strong id="alertTitle">!</strong> <span id="msgContent"></span>.
              </div>
            </div>
        </div>
    </div>
</div>