<?php
    class recurringFront extends recurring {
        function getPlan($planId){
            $url = BASE_URL_RECURRING_API.'plan/status';
            $data = array(
                'Signature' => self::getSignature(),
                "PlanId" => $planId+0
            );
        
            $postData = json_encode($data);
        
            $resultData = self::getData($url, $postData);
            return $resultData;
        }

        function setSubscription($formData){
            $url = BASE_URL_RECURRING_API.'subscription'; 
            // $planData = array(
            //     "MerchantSignature" => self::getSignature(),
            //     "Title" => $formData['Title'],
            //     "RecurrenceType" => $formData['RecurrenceType'],
            //     "Frequency" => [
            //         "Type" => $formData['Frequency']['Type'],
            //         "Value" => $formData['Frequency']['Value']+0 // Is added to ZERO to have result as INT
            //     ],
            //     "Description"=> $formData['Description'],
            //     "GracePeriod"=> $formData['GracePeriod']+0, // Is added to ZERO to have result as INT
            //     "Amount"=> $formData['Amount']+0, // Is added to ZERO to have result as FLOAT
            //     "Currency"=> $formData['Currency'],
            //     "InitialPayment" => $formData['InitialPayment'],
            //     "Taxable"=> false 
            // );
    
            $postData = json_encode($formData);
        
            $resultData = self::getData($url, $postData);
            return $resultData;
        }
    }
?>