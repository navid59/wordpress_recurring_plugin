function addSubscription() {
    // alert('Is NETOPIA Payments Recurring - action - add new subscription');
    
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


    data = {
        // action : 'addNewSubscription',
        action : 'getsomething',
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
    };

    // alert('PlanID : '+ PlanID);
    // alert('action'+ data.action);
    // alert('UserID'+ UserID);
    // alert('Name'+ Name);
    // alert('LastName'+ LastName);
    // alert('Email'+ Email);
    // alert('Address'+ Address);
    // alert('City'+ City);
    // alert('Tel'+ Tel);
    // alert('PlanId'+ PlanId);
    alert('StartDate'+ StartDate);
    // alert('EndDate'+ EndDate);
    // alert('Account'+ Account);
    // alert('ExpMonth'+ ExpMonth);
    // alert('ExpYear'+ ExpYear);
    // alert('SecretCode'+ SecretCode);


    // console.log('Continue to checkout');
    jQuery.ajax({
        url: myback.ajax_url,
        type: 'POST',
        dataType: 'json',
        data: data,
        success: function( response ){
            console.log("This is Navid response...");
            console.log(response);
            if(response.status) {
                jQuery('#msgBlock').addClass('alert-success');
                jQuery('#alertTitle').html('Congratulation!');
                jQuery('#msgContent').html(response.msg);
                jQuery('#msgBlock').addClass('show');
            } else {
                jQuery('#msgBlock').addClass('alert-warning');
                jQuery('#alertTitle').html('Error!');
                jQuery('#msgContent').html(response.msg);
                jQuery('#msgBlock').addClass('show');
            }
        },
        error: function( error ){
            // console.log('AJAX error NAVID callback....');
            // console.log(error);
            jQuery('#msgBlock').addClass('alert-warning');
            jQuery('#alertTitle').html('Error!');
            jQuery('#msgContent').html(response.msg);
            jQuery('#msgBlock').addClass('show');
        }
    });

    
}