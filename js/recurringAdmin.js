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
        // alert('1:'+planTitile);
        // alert('2:'+planDescription);
        // alert('3:'+RecurrenceType);
        // alert('4:'+FrequencyType);
        // alert('5:'+FrequencyValue);
        // alert('6:'+Amount);
        // alert('7:'+Currency);
        // alert('8:'+InitialPayment);
        // alert('9:'+GracePeriod);
        // alert('10:'+jsonResponse.msg);
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

// Example starter JavaScript for disabling form submissions if there are invalid fields
// (function() {
//     'use strict';
//     window.addEventListener('load', function() {
//       // Fetch all the forms we want to apply custom Bootstrap validation styles to
//       var forms = document.getElementsByClassName('needs-validation');
//       // Loop over them and prevent submission
//       var validation = Array.prototype.filter.call(forms, function(form) {
//         form.addEventListener('submit', function(event) {
//           if (form.checkValidity() === false) {
//             event.preventDefault();
//             event.stopPropagation();
//           }
//           form.classList.add('was-validated');
//         }, false);
//       });
//     }, false);
//   })();