jQuery(document).ready(function () {
    jQuery('#dtBasicExample').DataTable();
    jQuery('.dataTables_length').addClass('bs-select');
});


function subscriptionHistory(subscriptionId) {
    alert('History of subscription with ID :' + subscriptionId);
    jQuery('#subscriberHistorytModal').modal('toggle');
    jQuery('#subscriberHistorytModal').modal('show');
}

function subscriptionDetails(subscriptionId) {
    alert('Details of subscription with ID :' + subscriptionId);
    jQuery('#subscriberInfotModal').modal('toggle');
    jQuery('#subscriberInfotModal').modal('show');
}

function subscriptionNextPayment(subscriptionId, subscriberName) {
    jQuery('#subscriberName').html(subscriberName);
    jQuery('#nextPaymentModal').modal('toggle');
    jQuery('#nextPaymentModal').modal('show');
}

function delPlan(planId) {
    jQuery('#msgDelete').html('Are you sure you want to delete the plan ?');
    jQuery('#planId').val(planId);
    jQuery('#deletePlanModal').modal('toggle');
    jQuery('#deletePlanModal').modal('show');


    jQuery("#deletePlan").click(function (e) {
        var planId = jQuery("#planId").val();
        if (jQuery("#unsubscribe").prop("checked")) {
            var unsubscribe = true;
        } else {
            var unsubscribe = false;
        }
        
        if (jQuery("#conditions").prop("checked")) {
            var acceptedConditions = true;
        } else {
            var acceptedConditions = false;
        }

        data = {
            action : 'delPlan',
            planId : planId,
            unsubscribe : unsubscribe,
        };

        if(acceptedConditions) {
            jQuery.post(ajaxurl, data, function(response){
                console.log(data.action);
                jsonResponse = JSON.parse(response);
                if(jsonResponse.status) {
                    jQuery('#msgBlock').addClass('alert-success');
                    jQuery('#alertTitle').html('Congratulation!');
                    jQuery('#msgContent').html(jsonResponse.msg);
                    jQuery('#msgBlock').addClass('show');

                    // Refresh page after close Modal
                    jQuery('#deletePlanModal').on('hidden.bs.modal', function() {
                        window.location.reload();
                    });
                } else {
                    jQuery('#msgBlock').addClass('alert-warning');
                    jQuery('#alertTitle').html('Error!');
                    jQuery('#msgContent').html(jsonResponse.msg);
                    jQuery('#msgBlock').addClass('show');
                }
                console.log(jsonResponse);
            });
        } else {
            jQuery('#msgBlock').addClass('alert-warning');
            jQuery('#alertTitle').html('Error!');
            jQuery('#msgContent').html('You should to accept terms & conditions!');
            jQuery('#msgBlock').addClass('show');
        }
    });
    return false;
}

function editPlan(planId) {  
    jQuery("#editPlanId").val(planId);
    
    getPlanData = {
        action : 'getPlanInfo',
        planIdentity: planId,
    }
    jQuery.post(ajaxurl, getPlanData, function(response){
        console.log(getPlanData.action);
        jsonResponse = JSON.parse(response);
        if(jsonResponse.status) {
            jQuery("#editPlanTitile").val(jsonResponse.data.Title);
            jQuery("#editPlanDescription").val(jsonResponse.data.Description);
            jQuery("#editRecurrenceType").val(jsonResponse.data.RecurrenceType);
            jQuery("#editFrequencyType").val(jsonResponse.data.Frequency.Type);
            jQuery("#editFrequencyValue").val(jsonResponse.data.Frequency.Value);
            jQuery("#editAmount").val(jsonResponse.data.Amount);
            jQuery("#editCurrency").val(jsonResponse.data.Currency);
            jQuery("#editGracePeriod").val(jsonResponse.data.GracePeriod);
            
            if(jsonResponse.data.InitialPayment === true) {
                jQuery('#editInitialPayment').prop( "checked", true );
            }else {
                jQuery('#editInitialPayment').prop( "checked", false );
            }
        } else {
            // Alert &  exit
        }
    });


    jQuery('#editPlanModal').modal('toggle');
    jQuery('#editPlanModal').modal('show');

    jQuery("#editPlan").click(function (e) {
        var planId = jQuery("#editPlanId").val();
        var planTitile = jQuery("#editPlanTitile").val();
        var planDescription = jQuery("#editPlanDescription").val();
        var RecurrenceType = jQuery("#editRecurrenceType").val();
        var FrequencyType = jQuery("#editFrequencyType").val();
        var FrequencyValue = jQuery("#editFrequencyValue").val();
        var Amount = jQuery("#editAmount").val();
        var Currency = jQuery("#editCurrency").val();
        var GracePeriod = jQuery("#editGracePeriod").val();
        if (jQuery("#editInitialPayment").prop("checked")) {
            var InitialPayment = true;
        } else {
            var InitialPayment = false;
        }
        if (jQuery("#editConditions").prop("checked")) {
            var acceptedConditions = true;
        } else {
            var acceptedConditions = false;
        }

        data = {
            action : 'editPlan',
            planId : planId,
            planTitile : planTitile,
            planDescription : planDescription,
            RecurrenceType : RecurrenceType,
            FrequencyType : FrequencyType,
            FrequencyValue : FrequencyValue,
            Amount : Amount,
            Currency : Currency,
            GracePeriod : GracePeriod,
            InitialPayment : InitialPayment,
            TermAndConditionAccepted : acceptedConditions,
        };
        
        if(acceptedConditions) {
            jQuery.post(ajaxurl, data, function(response){
                console.log(data.action);
                jsonResponse = JSON.parse(response);
                console.log(jsonResponse);
                if(jsonResponse.status) {
                    jQuery('#editMsgBlock').addClass('alert-success');
                    jQuery('#editAlertTitle').html('Congratulation!');
                    jQuery('#editMsgContent').html(jsonResponse.msg);
                    jQuery('#editMsgBlock').addClass('show');

                    // Disable after success update
                    jQuery("#editPlanTitile").prop('readonly', true);
                    jQuery("#editPlanDescription").prop('readonly', true);
                    jQuery("#editRecurrenceType").attr("disabled", true); 
                    jQuery("#editFrequencyType").attr("disabled", true); 
                    jQuery("#editFrequencyValue").prop('readonly', true);
                    jQuery("#editAmount").prop('readonly', true);
                    jQuery("#editCurrency").attr("disabled", true); 
                    jQuery("#editGracePeriod").prop('readonly', true);
                    jQuery("#editInitialPayment").prop('readonly', true);
                    jQuery("#editConditions").prop('readonly', true);
                    
                    // Remove submit button after success
                    jQuery("#editPlan").hide();

                    // Refresh page after close Modal
                    jQuery('#editPlanModal').on('hidden.bs.modal', function() {
                        window.location.reload();
                    });
                }else {
                    jQuery('#editMsgBlock').addClass('alert-warning');
                    jQuery('#editAlertTitle').html('Error!');
                    jQuery('#editMsgContent').html(jsonResponse.msg);
                    jQuery('#editMsgBlock').addClass('show'); 
                }
            });
        } else {
            jQuery('#editMsgBlock').addClass('alert-warning');
            jQuery('#editAlertTitle').html('Error!');
            jQuery('#editMsgContent').html('You should to accept terms & conditions!');
            jQuery('#editMsgBlock').addClass('show');
        }

    });
    
    return false;
}

function copyPlan(planId, planTitile) {
    var shortCode = '[NTP-Recurring plan_id='+ planId +' button="Subscribe" title="'+ planTitile +'"]';
    navigator.clipboard.writeText(shortCode);
    jQuery('#shortcode').html(shortCode);
    jQuery('#copyShortCodeModal').modal('toggle');
    jQuery('#copyShortCodeModal').modal('show');
}

jQuery("#recurring-plan-form").submit(function (e) {
    var planTitile = jQuery("#planTitile").val();
    var planDescription = jQuery("#planDescription").val();
    var RecurrenceType = jQuery("#RecurrenceType").val();
    var FrequencyType = jQuery("#FrequencyType").val();
    var FrequencyValue = jQuery("#FrequencyValue").val();
    var Amount = jQuery("#Amount").val();
    var Currency = jQuery("#Currency").val();
    var GracePeriod = jQuery("#GracePeriod").val();
    if (jQuery("#InitialPayment").prop("checked")) {
        var InitialPayment = true;
    } else {
        var InitialPayment = false;
    }
    

    data = {
        action : 'addPlan',
        planTitile : planTitile,
        planDescription : planDescription,
        RecurrenceType : RecurrenceType,
        FrequencyType : FrequencyType,
        FrequencyValue : FrequencyValue,
        Amount : Amount,
        Currency : Currency,
        GracePeriod : GracePeriod,
        InitialPayment : InitialPayment,
    };

    jQuery.post(ajaxurl, data, function(response){
        console.log(data.action);
        jsonResponse = JSON.parse(response);
        if(jsonResponse.status) {
            jQuery('#msgBlock').addClass('alert-success');
            jQuery('#alertTitle').html('Congratulation!');
            jQuery('#msgContent').html(jsonResponse.msg);
            jQuery('#msgBlock').addClass('show');

            jQuery('#addPlan').html('Add new plan');
            document.getElementById("recurring-plan-form").reset();
            

        }else {
            jQuery('#msgBlock').addClass('alert-warning');
            jQuery('#alertTitle').html('Error!');
            jQuery('#msgContent').html(jsonResponse.msg);
            jQuery('#msgBlock').addClass('show');
        }
    });

    return false;
});