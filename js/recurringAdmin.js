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

jQuery("#recurring-plan-form").submit(function (e) {
    var planTitile = jQuery("#planTitile").val();
    var planDescription = jQuery("#planDescription").val();
    var RecurrenceType = jQuery("#RecurrenceType").val();
    var FrequencyType = jQuery("#FrequencyType").val();
    var FrequencyValue = jQuery("#FrequencyValue").val();
    var Amount = jQuery("#Amount").val();
    var Currency = jQuery("#Currency").val();
    if (jQuery("#InitialPayment").prop("checked")) {
        var InitialPayment = true;
    } else {
        var InitialPayment = false;
    }
    

    data = {
        action : 'ajax_test',
        planTitile : planTitile,
        planDescription : planDescription,
        RecurrenceType : RecurrenceType,
        FrequencyType : FrequencyType,
        FrequencyValue : FrequencyValue,
        Amount : Amount,
        Currency : Currency,
        InitialPayment : InitialPayment,
    };

    jQuery.post(ajaxurl, data, function(response){
        console.log(data.action);
        alert(response);
    });

    return false;
});