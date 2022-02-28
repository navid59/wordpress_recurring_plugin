function unsubscription() {
    jQuery('#loading').addClass('show');
    jQuery('#unsubscriptionButton').hide();

    var SubscriptionId = jQuery("#Subscription_Id").val();
    var Id = jQuery("#Id").val();

    data = {
        action : 'unsubscription',
        Id : Id,
        SubscriptionId : SubscriptionId,
    }

    jQuery.ajax({
        url: myback.ajax_url,
        type: 'POST',
        dataType: 'json',
        data: data,
        success: function( response ){
            if(response.status) {
                jQuery('#msgBlock').addClass('alert-success');
                jQuery('#alertTitle').html('Congratulation!');
                jQuery('#msgContent').html(response.msg);
                jQuery('#msgBlock').addClass('show');
                jQuery('#loading').removeClass('show');
                jQuery('#unsubscription-form').hide();               
                
                // Refresh page after close Modal
                jQuery('#unsubscriptionRecurringModal').on('hidden.bs.modal', function() {
                    window.location.reload();
                });                
            } else {
                jQuery('#msgBlock').addClass('alert-warning');
                jQuery('#alertTitle').html('Error!');
                jQuery('#msgContent').html(response.msg);
                jQuery('#msgBlock').addClass('show');
                jQuery('#unsubscriptionButton').show();
                jQuery('#loading').removeClass('show');
            }
        },
        error: function( error ){
            jQuery('#msgBlock').addClass('alert-warning');
            jQuery('#alertTitle').html('Error!');
            jQuery('#msgContent').html(response.msg);
            jQuery('#msgBlock').addClass('show');
            jQuery('#unsubscriptionButton').show();
            jQuery('#loading').removeClass('show');
        }
    });
}

function addSubscription() {
    jQuery('#loading').addClass('show');
    jQuery('#addSubscriptionButton').hide();

    var PlanID = jQuery("#planID").val();
    var UserID = jQuery("#username").val();
    var Name = jQuery("#firstName").val();
    var LastName = jQuery("#lastName").val();
    var Email = jQuery("#email").val();
    var Address  = jQuery("#address").val();
    var City = jQuery("#state").val();
    var Tel = jQuery("#tel").val();

    var PlanId = jQuery("#PlanId").val();
    var StartDate =  jQuery("#StartDate").val();
    var EndDate = jQuery("#EndDate").val();

    var Account = jQuery("#cc-number").val();
    var ExpMonth = jQuery("#cc-expiration-month").val();
    var ExpYear = jQuery("#cc-expiration-year").val();
    var SecretCode = jQuery("#cc-cvv").val();

    var ThreeDS = sendClientBrowserInfo();
    
    data = {
        action : 'addNewSubscription',
        PlanID : PlanID,
        UserID : UserID,
        Name : Name,
        LastName : LastName,
        Email : Email,
        Address : Address,
        City : City,
        Tel : Tel,
        PlanId : PlanId,
        StartDate : StartDate,
        EndDate : EndDate,
        Account : Account,
        ExpMonth : ExpMonth,
        ExpYear : ExpYear,
        SecretCode : SecretCode,
        ThreeDS : ThreeDS,
    };

    jQuery.ajax({
        url: myback.ajax_url,
        type: 'POST',
        dataType: 'json',
        data: data,
        success: function( response ){
            if(response.status) {
                jQuery('#msgBlock').addClass('alert-success');
                jQuery('#alertTitle').html('Congratulation!');
                jQuery('#msgContent').html(response.msg);
                jQuery('#msgBlock').addClass('show');
                jQuery('#loading').removeClass('show');

                /** Make form Read only on success */
                jQuery("#planID").prop('readonly', true);
                jQuery("#username").prop('readonly', true);
                jQuery("#firstName").prop('readonly', true);
                jQuery("#lastName").prop('readonly', true);
                jQuery("#email").prop('readonly', true);
                jQuery("#address").prop('readonly', true);
                jQuery("#country").attr("disabled", true); 
                jQuery("#state").attr("disabled", true); 
                jQuery("#tel").prop('readonly', true);
                jQuery("#PlanId").prop('readonly', true);
                jQuery("#StartDate").prop('readonly', true);
                jQuery("#EndDate").prop('readonly', true);
                jQuery("#cc-name").prop('readonly', true);
                jQuery("#cc-number").prop('readonly', true);
                jQuery("#cc-expiration-month").prop('readonly', true);
                jQuery("#cc-expiration-year").prop('readonly', true);
                jQuery("#cc-cvv").prop('readonly', true);

                // Refresh page after close Modal
                jQuery('#recurringModal').on('hidden.bs.modal', function() {
                    window.location.reload();
                });
            } else {
                jQuery('#msgBlock').addClass('alert-warning');
                jQuery('#alertTitle').html('Error!');
                jQuery('#msgContent').html(response.msg);
                jQuery('#msgBlock').addClass('show');
                jQuery('#addSubscriptionButton').show();
                jQuery('#loading').removeClass('show');
            }
        },
        error: function( error ){
            jQuery('#msgBlock').addClass('alert-warning');
            jQuery('#alertTitle').html('Error!');
            jQuery('#msgContent').html(response.msg);
            jQuery('#msgBlock').addClass('show');
            jQuery('#addSubscriptionButton').show();
            jQuery('#loading').removeClass('show');
        }
    });    
}