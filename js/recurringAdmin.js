function myFunction() {
    alert('Is NETOPIA Payments Recurring action 3333333333333333333')
}

jQuery(document).ready(function () {
    jQuery('#dtBasicExample').DataTable();
    jQuery('.dataTables_length').addClass('bs-select');
});


function getSubscriptions() {
    alert('Reload Data by AJAX - Admin - not implimented!')
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
    jQuery('#msgEdit').html('Edit plan '+ planId +'- by AJAX - Admin!');
    jQuery('#editPlanModal').modal('toggle');
    jQuery('#editPlanModal').modal('show');
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
        }else {
            jQuery('#msgBlock').addClass('alert-warning');
            jQuery('#alertTitle').html('Error!');
            jQuery('#msgContent').html(jsonResponse.msg);
            jQuery('#msgBlock').addClass('show');
        }
    });

    return false;
});