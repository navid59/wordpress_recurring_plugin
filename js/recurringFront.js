jQuery(document).ready(function () {
    jQuery('#frontAccountMyPaymentHistory').click(function() {
        jQuery('#frontAccountMysubscription').removeClass('active');
        jQuery('#frontAccountMyPaymentHistory').addClass('active');
        jQuery('#frontAccountDetails').removeClass('active');
        jQuery('#frontAccountLogout').removeClass('active');
        
        data = {
            action : 'getMyHistory'
        }
    
        jQuery.ajax({
            url: frontAjax.ajax_url,
            type: 'POST',
            dataType: 'json',
            data: data,
            success: function( response ){
                if(response.status) {      
                    // console.log(response);
                    jQuery('#ntpAccountBody').html(response.data);
 
                    const rows = response.histories.map(history => {
                        const tr = jQuery('<tr></tr>');
                        tr.append(jQuery('<td></td>').text(history.CreatedAt.split(' ')[0]));
                        tr.append(jQuery('<td></td>').text(history.Title + ' - ' + history.Amount));
                        // tr.append(jQuery('<td></td>').text(history.TransactionID));
                        // tr.append(jQuery('<td></td>').text(history.Comment));
                        tr.append(jQuery('<td></td>').text(history.Status));
                        return tr;
                    });
                    // console.log(rows);
                    jQuery("#mySubscriberPaymentHistoryList").html(rows);               
                } else {
                    jQuery('#ntpAccountBody').html(response.msg);
                }
            },
            error: function( error ){
                jQuery('#ntpAccountBody').html(response.msg);
            }
        });
    });

    jQuery('#frontAccountMysubscription').click(function() {
        jQuery('#frontAccountMysubscription').addClass('active');
        jQuery('#frontAccountMyPaymentHistory').removeClass('active');
        jQuery('#frontAccountDetails').removeClass('active');
        jQuery('#frontAccountLogout').removeClass('active');

        data = {
            action : 'getMySubscriptions'
        }
    
        jQuery.ajax({
            url: frontAjax.ajax_url,
            type: 'POST',
            dataType: 'json',
            data: data,
            success: function( response ){
                if(response.status) {                    
                    jQuery('#ntpAccountBody').html(response.data);
                    
                } else {
                    jQuery('#ntpAccountBody').html(response.msg);
                }
            },
            error: function( error ){
                jQuery('#ntpAccountBody').html(response.msg);
            }
        });
    });


    jQuery('#frontAccountLogout').click(function() {
        jQuery('#frontAccountMysubscription').removeClass('active');
        jQuery('#frontAccountMyPaymentHistory').removeClass('active');
        jQuery('#frontAccountDetails').removeClass('active');
        jQuery('#frontAccountLogout').addClass('active');

        data = {
            action : 'logoutAccount'
        };
    
        jQuery.ajax({
            url: frontAjax.ajax_url,
            type: 'POST',
            dataType: 'json',
            data: data,
            success: function( response ){
                if(response.status) {
                    location.replace(response.redirectUrl);
                } else {
                    //
                }
            },
            error: function( error ){
                //
            }
        });
    });


    jQuery('#frontAccountDetails').click(function() {
        jQuery('#frontAccountMysubscription').removeClass('active');
        jQuery('#frontAccountMyPaymentHistory').removeClass('active');
        jQuery('#frontAccountDetails').addClass('active');
        jQuery('#frontAccountLogout').removeClass('active');
        
        data = {
            action : 'getMyAccountDetails'
        }
    
        jQuery.ajax({
            url: frontAjax.ajax_url,
            type: 'POST',
            dataType: 'json',
            data: data,
            success: function( response ){
                if(response.status) {                    
                    jQuery('#ntpAccountBody').html(response.data);
                    
                } else {
                    jQuery('#ntpAccountBody').html(response.msg);
                }
            },
            error: function( error ){
                jQuery('#ntpAccountBody').html(response.msg);
            }
        });
    });

    jQuery('.add-subscription-form').on('submit', addSubscription);
    jQuery('.unsubscription-form').on('submit', unsubscription);
    jQuery('.verify-action-form').on('submit', VerifyAuthAction);
});


function unsubscription(e) {
    e.preventDefault(); // to stop Submit Event
    var form = jQuery(this);
    var formId = form.attr('id');

    var SubscriptionId = jQuery('#'+formId).find('input[name=Subscription_Id]').val();  
    var Id = jQuery('#'+formId).find('input[name=Id]').val();
    var planId = jQuery('#'+formId).find('input[name=planId]').val();

    jQuery('#loading'+SubscriptionId).addClass('show');
    jQuery('#unsubscriptionButton'+planId).hide();
    
    data = {
        action : 'unsubscription',
        Id : Id,
        SubscriptionId : SubscriptionId,
    }

    console.log('SubscriptionId : '+SubscriptionId);
    jQuery.ajax({
        url: frontAjax.ajax_url,
        type: 'POST',
        dataType: 'json',
        data: data,
        success: function( response ){
            if(response.status) {
                jQuery('#msgBlock'+SubscriptionId).addClass('alert-success');
                jQuery('#alertTitle'+SubscriptionId).html('Congratulation!');
                jQuery('#msgContent'+SubscriptionId).html(response.msg);
                jQuery('#msgBlock'+SubscriptionId).addClass('show');
                jQuery('#loading'+SubscriptionId).removeClass('show');
                jQuery('#'+formId).hide();               
                
                // Refresh page after close Modal
                jQuery('.unsubscriptionRecurringModal').on('hidden.bs.modal', function() {
                    window.location.reload();
                });                
            } else {
                jQuery('#msgBlock'+SubscriptionId).addClass('alert-warning');
                jQuery('#alertTitle'+SubscriptionId).html('Error!');
                jQuery('#msgContent'+SubscriptionId).html(response.msg);
                jQuery('#msgBlock'+SubscriptionId).addClass('show');
                jQuery('#unsubscriptionButton'+planId).show();
                jQuery('#loading'+SubscriptionId).removeClass('show');
            }
        },
        error: function( error ){
            jQuery('#msgBlock'+SubscriptionId).addClass('alert-warning');
            jQuery('#alertTitle'+SubscriptionId).html('Error!');
            jQuery('#msgContent'+SubscriptionId).html(response.msg);
            jQuery('#msgBlock'+SubscriptionId).addClass('show');
            jQuery('#unsubscriptionButton'+planId).show();
            jQuery('#loading'+SubscriptionId).removeClass('show');
        }
    });
}

function unsubscriptionMyAccount() {
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
        url: frontAjax.ajax_url,
        type: 'POST',
        dataType: 'json',
        data: data,
        success: function( response ){
            if(response.status) {
                jQuery('#myAccountMsgBlock').addClass('alert-success');
                jQuery('#myAccountAlertTitle').html('Congratulation!');
                jQuery('#myAccountMsgContent').html(response.msg);
                jQuery('#myAccountMsgBlock').addClass('show');
                jQuery('#loading').removeClass('show');
                jQuery('#unsubscription-form').hide();               
                
                // Refresh page after close Modal
                jQuery('#unsubscriptionMyAccountModal').on('hidden.bs.modal', function() {
                    window.location.reload();
                });                
            } else {
                jQuery('#myAccountMsgBlock').addClass('alert-warning');
                jQuery('#myAccountAlertTitle').html('Error!');
                jQuery('#myAccountMsgContent').html(response.msg);
                jQuery('#myAccountMsgBlock').addClass('show');
                jQuery('#unsubscriptionButton').show();
                jQuery('#loading').removeClass('show');
            }
        },
        error: function( error ){
            jQuery('#myAccountMsgBlock').addClass('alert-warning');
            jQuery('#myAccountAlertTitle').html('Error!');
            jQuery('#myAccountMsgContent').html(response.msg);
            jQuery('#myAccountMsgBlock').addClass('show');
            jQuery('#unsubscriptionButton').show();
            jQuery('#loading').removeClass('show');
        }
    });
}

function addSubscription(e) {   
    e.preventDefault(); // to stop Submit Event
    var form = jQuery(this);
    var formId = form.attr('id');

    var PlanID = jQuery('#'+formId).find('input[name=planID]').val();
    var UserID = jQuery('#'+formId).find('input[name=username]').val();
    var Pass = jQuery('#'+formId).find('input[name=password]').val();
    var Name = jQuery('#'+formId).find('input[name=firstName]').val();
    var LastName = jQuery('#'+formId).find('input[name=lastName]').val();
    var Email = jQuery('#'+formId).find('input[name=email]').val();
    var Address  = jQuery('#'+formId).find('input[name=address]').val();
    var City = jQuery('#'+formId).find('input[name=state]').val();
    var Tel = jQuery('#'+formId).find('input[name=tel]').val();
    

    var PlanId = PlanID;//jQuery("#PlanId").val();
    var StartDate =  jQuery('#'+formId).find('input[name=StartDate]').val();
    var EndDate = jQuery('#'+formId).find('input[name=EndDate]').val();

    var Account = jQuery('#'+formId).find('input[name=cc-number]').val();
    var ExpMonth = jQuery('#'+formId).find('input[name=cc-expiration-month]').val();
    var ExpYear = jQuery('#'+formId).find('input[name=cc-expiration-year]').val();
    var SecretCode = jQuery('#'+formId).find('input[name=cc-cvv]').val();

    var ThreeDS = sendClientBrowserInfo();

    var BackUrl = jQuery('#backUrl'+PlanID).val();
    
    jQuery('#loading'+PlanID).addClass('show');
    jQuery('#addSubscriptionButton'+PlanID).hide();

    if(!luhn_validate(Account)) {
        jQuery('#msgBlock'+PlanID).addClass('alert-warning');
        jQuery('#alertTitle'+PlanID).html('Error!');
        jQuery('#msgContent'+PlanID).html('Your card is not a valid card, please use a valid one!');
        jQuery('#msgBlock'+PlanID).addClass('show');
        jQuery('#addSubscriptionButton'+PlanID).show();
        jQuery('#loading'+PlanID).removeClass('show');
    } else {
        data = {
            action : 'addNewSubscription',
            PlanID : PlanID,
            UserID : UserID,
            Pass : Pass,
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
            BackUrl : BackUrl,
        };
    
        
        jQuery.ajax({
            url: frontAjax.ajax_url,
            type: 'POST',
            dataType: 'json',
            data: data,
            success: function( response ){
                if(response.status) {
                    jQuery('#msgBlock'+PlanID).addClass('alert-success');
                    jQuery('#alertTitle'+PlanID).html('Congratulation!');
                    jQuery('#msgContent'+PlanID).html(response.msg);
                    jQuery('#msgBlock'+PlanID).addClass('show');
                    jQuery('#loading'+PlanID).removeClass('show');
    
                    /** Make form Read only on success */
                    jQuery('#'+formId).find('input[name=planID]').prop('readonly', true);
                    jQuery('#'+formId).find('input[name=username]').prop('readonly', true);
                    jQuery('#'+formId).find('input[name=firstName]').prop('readonly', true);
                    jQuery('#'+formId).find('input[name=lastName]').prop('readonly', true);
                    jQuery('#'+formId).find('input[name=email]').prop('readonly', true);
                    jQuery('#'+formId).find('input[name=address]').prop('readonly', true);
                    jQuery('#'+formId).find('input[name=country]').attr("disabled", true); 
                    jQuery('#'+formId).find('input[name=state]').attr("disabled", true); 
                    jQuery('#'+formId).find('input[name=tel]').prop('readonly', true);
                    jQuery('#'+formId).find('input[name=PlanId]').prop('readonly', true);
                    jQuery('#'+formId).find('input[name=StartDate]').prop('readonly', true);
                    jQuery('#'+formId).find('input[name=EndDate]').prop('readonly', true);
                    jQuery('#'+formId).find('input[name=cc-name]').prop('readonly', true);
                    jQuery('#'+formId).find('input[name=cc-number]').prop('readonly', true);
                    jQuery('#'+formId).find('input[name=cc-expiration-month]').prop('readonly', true);
                    jQuery('#'+formId).find('input[name=cc-expiration-year]').prop('readonly', true);
                    jQuery('#'+formId).find('input[name=cc-cvv]').prop('readonly', true);
    
                    // Refresh page after close Modal
                    jQuery('.recurringModal').on('hidden.bs.modal', function() {
                        window.location.reload();
                    });
                } else {
                    console.log(response.detail);
                    /**
                     * If need auth 
                     */
                    if(response.detail.PaymentCode == "100") {
                        let actionUrl = response.detail.PaymentDetails.ThreeDS.url;
                        let paReq     = response.detail.PaymentDetails.ThreeDS.paReq;
                        let backUrl   = response.detail.PaymentDetails.ThreeDS.backUrl;
                        let authenticationToken   = response.detail.PaymentDetails.AuthenticationToken;
                        let ntpID   = response.detail.PaymentDetails.NtpID;
                        
                        jQuery('#3DSAuthorizeForm'+PlanID).attr('action', actionUrl);
                        jQuery('#paReq'+PlanID).val(paReq);
                        jQuery('#backUrl'+PlanID).val(backUrl);

                        jQuery('#msgBlock'+PlanID).addClass('alert-warning');
                        jQuery('#alertTitle'+PlanID).html('Warning!');
                        jQuery('#msgContent'+PlanID).html(response.msg);
                        jQuery('#msgBlock'+PlanID).addClass('show')
                        jQuery('#msgContent'+PlanID).append('You will redirect to your bank. Please not close the page');

                                                
                        /** Call set cookies */
                        setCookieVerifyAuthOnNewSubscription(PlanID, authenticationToken, ntpID);
                        

                        jQuery('#loading'+PlanID).removeClass('show');
                        jQuery('#spinner'+PlanID).removeClass('d-none');
                        /**
                         * Redirect to bank for 3DSAuthorize
                         */
                        jQuery('#3DSAuthorizeForm'+PlanID).submit();


                    } else {
                        jQuery('#msgBlock'+PlanID).addClass('alert-warning');
                        jQuery('#alertTitle'+PlanID).html('Error!');
                        jQuery('#msgContent'+PlanID).html(response.msg);
                        jQuery('#msgBlock'+PlanID).addClass('show');
                        jQuery('#addSubscriptionButton'+PlanID).show();
                        jQuery('#loading'+PlanID).removeClass('show');
                    }                    
                }
            },
            error: function( error ){
                jQuery('#msgBlock'+PlanID).addClass('alert-warning');
                jQuery('#alertTitle'+PlanID).html('Error!');
                jQuery('#msgContent'+PlanID).html(response.msg);
                jQuery('#msgBlock'+PlanID).addClass('show');
                jQuery('#addSubscriptionButton'+PlanID).show();
                jQuery('#loading'+PlanID).removeClass('show');
            }
        });
    }
}

function setCookieVerifyAuthOnNewSubscription(PlanID, AuthenticationToken, NtpID) {
    console.log(PlanID);
    console.log(AuthenticationToken);
    console.log(NtpID);
    
    data = {
        action : 'cookieVerifyAuth',
        PlanID : PlanID,
        AuthenticationToken : AuthenticationToken,
        NtpID : NtpID,                          
    };

    jQuery.ajax({
        url: frontAjax.ajax_url,
        type: 'POST',
        dataType: 'json',
        data: data,
        success: function( response ){
            if(response.status) {
                console.log('cookies set for 3DS, success');
            } else {
                console.log('cookies set for 3DS, Failed!');
            }
        },
        error: function( error ){
            console.log('call set cookies for 3DS, Failed!');
        }
    });
}

function VerifyAuthAction(e) {
    e.preventDefault(); // to stop Submit Event
    var form = jQuery(this);
    var formId = form.attr('id');

    var ntpRpAuthenticationToken = jQuery('#'+formId).find('input[name=ntpRpAuthenticationToken]').val();
    var ntpRpNtpID = jQuery('#'+formId).find('input[name=ntpRpNtpID]').val();
    var ntpRpPaRes = jQuery('#'+formId).find('input[name=ntpRpPaRes]').val();

    console.log(ntpRpAuthenticationToken);
    console.log(ntpRpNtpID);
    console.log(ntpRpPaRes);

    data = {
        action : 'doVerifyAuth',
        authenticationToken : ntpRpAuthenticationToken,
        ntpID : ntpRpNtpID,
        paRes : ntpRpPaRes,
    };


    jQuery.ajax({
        url: frontAjax.ajax_url,
        type: 'POST',
        dataType: 'json',
        data: data,
        success: function( response ){
            if(response.status) {
                /////////
                jQuery('#msgBlock-'+formId).addClass('alert-success');
                jQuery('#alertTitle-'+formId).html('Warning!');
                jQuery('#msgContent-'+formId).html(response.msg);
                jQuery('#loading-'+formId).removeClass('show');
                jQuery('#msgBlock-'+formId).addClass('show')
                jQuery('#msgContent-'+formId).append('Verify Auth - progress is complited.');
                /////////
            } else {
                ////////
                jQuery('#msgBlock-'+formId).addClass('alert-warning');
                jQuery('#alertTitle-'+formId).html('Warning!');
                jQuery('#msgContent-'+formId).html(response.msg);
                jQuery('#loading-'+formId).removeClass('show');
                jQuery('#msgBlock-'+formId).addClass('show')
                jQuery('#msgContent-'+formId).append('Verify Auth is failed!. Please try again');
                ////////
            }
        },
        error: function( error ){
            jQuery('#msgBlock-'+formId).addClass('alert-danger');
            jQuery('#alertTitle-'+formId).html('Error!');
            jQuery('#msgContent-'+formId).html(response.msg);
            jQuery('#loading-'+formId).removeClass('show');
            jQuery('#msgBlock-'+formId).addClass('show')
        }
    });

}

function frontSubscriptionNextPayment(subscriptionId, palanId, subscriberName) {
    jQuery('#subscriberName').html(subscriberName);

    getNextPaymentData = {
        action : 'getMyNextPayment',
        subscriptionId: subscriptionId,
    }

    jQuery.ajax({
        url: frontAjax.ajax_url,
        type: 'POST',
        dataType: 'json',
        data: getNextPaymentData,
        success: function( response ){
            if(response.status) {
                if(response.data.isValid) {
                    jQuery("#nextPaymentStatus").html('Active');
                } else {
                    jQuery("#nextPaymentStatus").html('Inactive');
                }
                
                jQuery("#nextPaymentDate").html(response.data.nextPayment);
                
            } else {
                jQuery('#msgBlock').addClass('alert-warning');
                jQuery('#alertTitle').html('Error!');
                jQuery('#msgContent').html(response.msg);
                jQuery('#msgBlock').addClass('show');
            }
        },
        error: function( error ){
            jQuery('#msgBlock').addClass('alert-warning');
            jQuery('#alertTitle').html('Error!');
            jQuery('#msgContent').html(response.msg);
            jQuery('#msgBlock').addClass('show');
        }
    });

    jQuery('#nextPaymentModal').modal('toggle');
    jQuery('#nextPaymentModal').modal('show');
}



jQuery(document).on("click", ".unsubscriptionMyAccounButton", function () {
    var planTitle = jQuery(this).data('plantitle');
    var userId = jQuery(this).data('userid'); 
    var subscriptionId = jQuery(this).data('subscriptionid');

    console.log('planTitle : ' + planTitle);
    console.log('userId : ' + userId);
    console.log('subscriptionId : ' + subscriptionId);

    jQuery('#PlanTitle').html(planTitle);
    jQuery('#Id').val(userId);
    jQuery('#Subscription_Id').val(subscriptionId);

});


function updateMyAccountDetails() {
    var SubscriptionId = jQuery("#SubscriptionId").val();
    var UserID = jQuery("#username").val();
    var Pass = jQuery("#password").val();
    var Name = jQuery("#firstName").val();
    var LastName = jQuery("#lastName").val();
    var Email = jQuery("#email").val();
    var Address  = jQuery("#address").val();
    var City = jQuery("#state").val();
    var Tel = jQuery("#tel").val();
   
    data = {
        action : 'updateSubscriberAccountDetails',
        SubscriptionId : SubscriptionId,
        UserID : UserID,
        Pass : Pass,
        Name : Name,
        LastName : LastName,
        Email : Email,
        Address : Address,
        City : City,
        Tel : Tel,
    };

    jQuery.ajax({
        url: frontAjax.ajax_url,
        type: 'POST',
        dataType: 'json',
        data: data,
        success: function( response ){
            if(response.status) {
                jQuery('#myAccountForm').hide();
                jQuery('#msgBlock').addClass('alert-success');
                jQuery('#alertTitle').html('Congratulation!');
                jQuery('#msgContent').html(response.msg);
                jQuery('#msgBlock').addClass('show');
                jQuery('#loading').removeClass('show');
            } else {
                jQuery('#msgBlock').addClass('alert-warning');
                jQuery('#alertTitle').html('Error!');
                jQuery('#msgContent').html(response.msg);
                jQuery('#myAccountGoToHome').hide();
                jQuery('#msgBlock').addClass('show');
                jQuery('#addSubscriptionButton').show();
                jQuery('#loading').removeClass('show');
            }
        },
        error: function( error ){
            jQuery('#msgBlock').addClass('alert-warning');
            jQuery('#alertTitle').html('Error!');
            jQuery('#msgContent').html(response.msg);
            jQuery('#myAccountGoToHome').hide();
            jQuery('#msgBlock').addClass('show');
            jQuery('#addSubscriptionButton').show();
            jQuery('#loading').removeClass('show');
        }
    });
}

/* luhn_checksum
 * Implement the Luhn algorithm to calculate the Luhn check digit.
 * Return the check digit.
 */
function luhn_checksum(code) {
    var len = code.length
    var parity = len % 2
    var sum = 0
    for (var i = len-1; i >= 0; i--) {
        var d = parseInt(code.charAt(i))
        if (i % 2 == parity) { d *= 2 }
        if (d > 9) { d -= 9 }
        sum += d
    }
    return sum % 10
}

/* luhn_validate
 * Return true if specified code (with check digit) is valid.
 */
function luhn_validate(fullcode) {
    return luhn_checksum(fullcode) == 0
}