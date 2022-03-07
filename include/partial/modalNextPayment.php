<div id="nextPaymentModal" class="modal fade" tabindex="-1" aria-labelledby="recurringModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
              <h2 class="modal-title" id="nextPaymentModalLabel"><?php echo __('Payment Schedule','ntpRp');?></h2>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div id="">
                <?php
                echo __('Next payment schedule for ', 'ntpRp');
                ?>
                <strong>
                    <span id="subscriberName"></span>
                </strong>
              </div>        
              <div>
                <h5>Date : YYYY</h5>
                <h5>Status : xxxx</h5>
              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
              </div>
            </div>
        </div>
    </div>
</div>