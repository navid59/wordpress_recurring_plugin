<?php
include_once('recurring.php');
include_once('firebase/php-jwt/src/JWT.php');
include_once('firebase/php-jwt/src/SignatureInvalidException.php');
include_once('firebase/php-jwt/src/BeforeValidException.php');
include_once('firebase/php-jwt/src/ExpiredException.php');

use Firebase\JWT\JWT;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;

class IPN {
    // Temporary 
    public $logFile;

    public $posSignatureSet;
    public $hashMethod;
    public $alg;
    public $publicKeyStr;

    // Error code defination
    const E_VERIFICATION_FAILED_GENERAL			= 0x10000101;
    const E_VERIFICATION_FAILED_SIGNATURE		= 0x10000102;
    const E_VERIFICATION_FAILED_NBF_IAT			= 0x10000103;
    const E_VERIFICATION_FAILED_EXPIRED			= 0x10000104;
    const E_VERIFICATION_FAILED_AUDIENCE		= 0x10000105;
    const E_VERIFICATION_FAILED_TAINTED_PAYLOAD	= 0x10000106;
    const E_VERIFICATION_FAILED_PAYLOAD_FORMAT	= 0x10000107;

    public const ERROR_TYPE_NONE 		= 0x00;
    public const ERROR_TYPE_TEMPORARY 	= 0x01;
    public const ERROR_TYPE_PERMANENT 	= 0x02;

    const ERROR_LOAD_X509_CERTIFICATE	= 0x10000001;
	const ERROR_ENCRYPT_DATA			= 0x10000002;

    const RECURRING_ERROR_CODE_NEED_VERIFY  = 0x200; // Need Verify Recurring API Key

    /**
     * available statuses for the purchase class (prcStatus)
     */
    const STATUS_NEW 									= 1;	//0x01; //new purchase status
    const STATUS_OPENED 								= 2;	//OK //0x02; // specific to Model_Purchase_Card purchases (after preauthorization) and Model_Purchase_Cash
    const STATUS_PAID 									= 3;	//OK //0x03; // capturate (card)
    const STATUS_CANCELED 								= 4;	//0x04; // void
    const STATUS_CONFIRMED 								= 5;	//OK //0x05; //confirmed status (after IPN)
    const STATUS_PENDING 								= 6;	//0x06; //pending status
    const STATUS_SCHEDULED 								= 7;	//0x07; //scheduled status, specific to Model_Purchase_Sms_Online / Model_Purchase_Sms_Offline
    const STATUS_CREDIT 								= 8;	//0x08; //specific status to a capture & refund state
    const STATUS_CHARGEBACK_INIT 						= 9;	//0x09; //status specific to chargeback initialization
    const STATUS_CHARGEBACK_ACCEPT 						= 10;	//0x0a; //status specific when chargeback has been accepted
    const STATUS_ERROR 									= 11;	//0x0b; // error status
    const STATUS_DECLINED 								= 12;	//0x0c; // declined status
    const STATUS_FRAUD 									= 13;	//0x0d; // fraud status
    const STATUS_PENDING_AUTH 							= 14;	//0x0e; //specific status to authorization pending, awaiting acceptance (verify)
    const STATUS_3D_AUTH 								= 15;	//0x0f; //3D authorized status, speficic to Model_Purchase_Card
    const STATUS_CHARGEBACK_REPRESENTMENT 				= 16;	//0x10;
    const STATUS_REVERSED 								= 17;	//0x11; //reversed status
    const STATUS_PENDING_ANY 							= 18;	//0x12; //dummy status
    const STATUS_PROGRAMMED_RECURRENT_PAYMENT 			= 19;	//0x13; //specific to recurrent card purchases
    const STATUS_CANCELED_PROGRAMMED_RECURRENT_PAYMENT 	= 20;	//0x14; //specific to cancelled recurrent card purchases
    const STATUS_TRIAL_PENDING							= 21;	//0x15; //specific to Model_Purchase_Sms_Online; wait for ACTON_TRIAL IPN to start trial period
    const STATUS_TRIAL									= 22;	//0x16; //specific to Model_Purchase_Sms_Online; trial period has started
    const STATUS_EXPIRED								= 23;	//0x17; //cancel a not payed purchase 

    
    /**
     * to Verify IPN
     * @return 
     *  - a Json
     */
    public function verifyIPN() {
        $obj = new recurring();

        /** Log Time */
        $logDate = new DateTime();
        $logDate = $logDate->format("y:m:d h:i:s");


        // $publicKeyPath = WP_PLUGIN_DIR . '/netopia-recurring/certificates/'.$obj->getPublicKey();
                    
        /**
        * Default IPN response, 
        * will change if there is any problem
        */
        $outputData = array(
            'errorType'		=> self::ERROR_TYPE_NONE,
            'errorCode' 	=> null,
            'errorMessage'	=> ''
        );

        /**
        *  Fetch all HTTP request headers
        */
        $aHeaders = $this->getApacheHeader();
        file_put_contents($this->logFile, "--- HEADER PASSED --- \n", FILE_APPEND);
        file_put_contents($this->logFile, print_r($aHeaders, true)." \n", FILE_APPEND);
        if(!$this->validHeader($aHeaders)) {
            /**
             * check if header has Apikey
             */
            file_put_contents($this->logFile, "--- Does not have Verification-Token --- \n", FILE_APPEND);
            if(array_key_exists('Apikey', $aHeaders)) {
               $outputData['errorType']	= self::ERROR_TYPE_TEMPORARY;
               $outputData['errorCode']	= self::RECURRING_ERROR_CODE_NEED_VERIFY;
               $outputData['errorMessage']	= 'Need to Validate API Key';

               return $outputData;
            } else {
                /** Log IPN */
                file_put_contents($this->logFile, "[".$logDate."] IPN__header is not an valid HTTP HEADER \n", FILE_APPEND);
                echo 'IPN__header is not an valid HTTP HEADER' . PHP_EOL;
                exit;
            }            
        } else {
            
        }
        file_put_contents($this->logFile, "--- ----------------- ----------------- --- \n", FILE_APPEND);

        /**
        *  fetch Verification-token from HTTP header 
        */
        $verificationToken = $this->getVerificationToken($aHeaders);
        $apikey = $this->getApikey($aHeaders);
        if($verificationToken === null && $apikey === null)
            {
            /** Log IPN */
            file_put_contents($this->logFile, "[".$logDate."] IPN__Verification-token is missing in HTTP HEADER \n", FILE_APPEND);
            echo 'IPN__Verification-token is missing in HTTP HEADER' . PHP_EOL;
            exit;
            }

        file_put_contents($this->logFile, "--- HAS verification Token --- \n", FILE_APPEND);
        /**
        * Analising verification token
        * Just to make sure if Type is JWT & Use right encoding/decoding algorithm 
        * Assign following var 
        *  - $headb64, 
        *  - $bodyb64,
        *  - $cryptob64
        */
        $tks = \explode('.', $verificationToken);
        if (\count($tks) != 3) {
            file_put_contents($this->logFile, "--- Choos,... --- \n", FILE_APPEND);
            throw new \Exception('Wrong_Verification_Token');
            exit;
        }
        list($headb64, $bodyb64, $cryptob64) = $tks;
        $jwtHeader = json_decode(base64_decode(\strtr($headb64, '-_', '+/')));
        
        if($jwtHeader->typ !== 'JWT') {
            throw new \Exception('Wrong_Token_Type');
            exit; 
        }


        /**
        * check if publicKeyStr is defined
        */
        if(isset($this->publicKeyStr) && !is_null($this->publicKeyStr)){
            $publicKey = openssl_pkey_get_public($this->publicKeyStr);
            if($publicKey === false) {
                file_put_contents($this->logFile, 'IPN__public key is not a valid public key'."\n", FILE_APPEND);
                echo 'IPN__public key is not a valid public key' . PHP_EOL; 
                exit;
            }
        } else {
            file_put_contents($this->logFile, 'IPN__Public key missing'."\n", FILE_APPEND);
            echo "IPN__Public key missing" . PHP_EOL; 
            exit;
        }
        
        
        /**
        * Get raw data
        */
        $HTTP_RAW_POST_DATA = file_get_contents('php://input');

        /**
        * The name of the alg defined in header of JWT
        * Just in case we set the default algorithm
        * Default alg is RS512
        */
        if(!isset($this->alg) || $this->alg==null){
            file_put_contents($this->logFile, "IDS_Service_IpnController__INVALID_JWT_ALG \n", FILE_APPEND);
            throw new \Exception('IDS_Service_IpnController__INVALID_JWT_ALG');
            exit;
        }
        $jwtAlgorithm = !is_null($jwtHeader->alg) ? $jwtHeader->alg : $this->alg ; // ???? May need to Compare with Verification-token header // Ask Alex

        
        try {
            JWT::$timestamp = time() * 1000; 
            $objJwt = JWT::decode($verificationToken, $publicKey, array($jwtAlgorithm));
        
            if(strcmp($objJwt->iss, 'NETOPIA Payments') != 0)
                {
                file_put_contents($this->logFile, "IDS_Service_IpnController__E_VERIFICATION_FAILED_GENERAL \n", FILE_APPEND);
                throw new \Exception('IDS_Service_IpnController__E_VERIFICATION_FAILED_GENERAL');
                exit;
                }
            
            /**
             * check active posSignature 
             * check if is in set of signature too
             */
            if(empty($objJwt->aud) || $objJwt->aud[0] != $this->activeKey){
                file_put_contents($this->logFile, "IDS_Service_IpnController__INVALID_SIGNATURE \n", FILE_APPEND);
                throw new \Exception('IDS_Service_IpnController__INVALID_SIGNATURE');
                exit;
            }
        
            if(!in_array($objJwt->aud[0], $this->posSignatureSet,true)) {
                file_put_contents($this->logFile, "IDS_Service_IpnController__INVALID_SIGNATURE_SET \n", FILE_APPEND);
                throw new \Exception('IDS_Service_IpnController__INVALID_SIGNATURE_SET');
                exit;
            }
            
            if(!isset($this->hashMethod) || $this->hashMethod==null){
                file_put_contents($this->logFile, "IDS_Service_IpnController__INVALID_HASH_METHOD \n", FILE_APPEND);
                throw new \Exception('IDS_Service_IpnController__INVALID_HASH_METHOD');
                exit;
            }
            
            /**
             * GET HTTP HEADER
             */
            $payload = $HTTP_RAW_POST_DATA;
            /**
             * validate payload
             * sutable hash method is SHA512 
             */
            $payloadHash = base64_encode(hash ($this->hashMethod, $payload, true ));
            /**
             * check IPN data integrity
             */
        
            if(strcmp($payloadHash, $objJwt->sub) != 0)
                {
                file_put_contents($this->logFile, "IDS_Service_IpnController__E_VERIFICATION_FAILED_TAINTED_PAYLOAD \n", FILE_APPEND);
                throw new \Exception('IDS_Service_IpnController__E_VERIFICATION_FAILED_TAINTED_PAYLOAD', E_VERIFICATION_FAILED_TAINTED_PAYLOAD);
                exit;
                }
        
            try
                {
                $objIpn = json_decode($payload, false);
                file_put_contents($this->logFile, "IPN Object : ".print_r($objIpn, true)." \n", FILE_APPEND);
                }
            catch(\Exception $e)
                {
                file_put_contents($this->logFile, "IDS_Service_IpnController__E_VERIFICATION_FAILED_PAYLOAD_FORMAT \n", FILE_APPEND);
                throw new \Exception('IDS_Service_IpnController__E_VERIFICATION_FAILED_PAYLOAD_FORMAT', E_VERIFICATION_FAILED_PAYLOAD_FORMAT);
                }
            
            switch($objIpn->payment->status)
                {
                case self::STATUS_NEW:
                case self::STATUS_CHARGEBACK_INIT:                  // chargeback initiat
                case self::STATUS_CHARGEBACK_ACCEPT:                // chargeback acceptat
                case self::STATUS_SCHEDULED:
                case self::STATUS_3D_AUTH:
                case self::STATUS_CHARGEBACK_REPRESENTMENT:
                case self::STATUS_REVERSED:
                case self::STATUS_PENDING_ANY:
                case self::STATUS_PROGRAMMED_RECURRENT_PAYMENT:
                case self::STATUS_CANCELED_PROGRAMMED_RECURRENT_PAYMENT:
                case self::STATUS_TRIAL_PENDING:                    //specific to Model_Purchase_Sms_Online; wait for ACTON_TRIAL IPN to start trial period
                case self::STATUS_TRIAL:                            //specific to Model_Purchase_Sms_Online; trial period has started
                case self::STATUS_EXPIRED:                          //cancel a not payed purchase 
                case self::STATUS_OPENED:                           // preauthorizate (card)
                case self::STATUS_PENDING:
                case self::STATUS_ERROR:                            // error
                case self::STATUS_DECLINED:                         // declined
                case self::STATUS_FRAUD:                            // fraud
                    /**
                     * payment status is in fraud, reviw the payment
                     */
                    $outputData['errorType']	= self::ERROR_TYPE_TEMPORARY;
                    $outputData['errorCode']	= null;
                    $outputData['errorMessage']	= 'payment in reviwing';
                break;
                case self::STATUS_PENDING_AUTH:                     // in asteptare de verificare pentru tranzactii autorizate
                    /**
                     * update payment status, last modified date&time in your system
                     */
                    $outputData['errorType']	= self::ERROR_TYPE_TEMPORARY;
                    $outputData['errorCode']	= null;
                    $outputData['errorMessage']	= 'update payment status, last modified date&time in your system';
                break;
                
                case self::STATUS_PAID:                             // capturate (card)
                case self::STATUS_CONFIRMED:
                    /**
                     * payment was confirmed; deliver goods
                     */
                    $orderLog = 'payment was confirmed; deliver goods';
                    // hear, can make Log for $orderLog
                break;
                
                case self::STATUS_CREDIT:                           // capturate si apoi refund
                    /**
                     * a previously confirmed payment eas refinded; cancel goods delivery
                     */
                    $orderLog = 'a previously confirmed payment eas refinded; cancel goods delivery';
                    // hear, can make Log for $orderLog
                break;
                
                case self::STATUS_CANCELED:                         // void
                    /**
                     * payment was cancelled; do not deliver goods
                     */
                    $outputData['errorType']	= self::ERROR_TYPE_TEMPORARY;
                    $outputData['errorCode']	= null;
                    $outputData['errorMessage']	= 'payment was cancelled; do not deliver goods';
                break;
                }
            
        } catch(\Exception $e)
        {
            $outputData['errorType']	= self::ERROR_TYPE_PERMANENT;
            $outputData['errorCode']	= ($e->getCode() != 0) ? $e->getCode() : self::E_VERIFICATION_FAILED_GENERAL;
            $outputData['errorMessage']	= $e->getMessage();
        }

        return $outputData;
    }


    /**
    *  Fetch all HTTP request headers
    */
    public function getApacheHeader() {
        $aHeaders = apache_request_headers();
        return $aHeaders;
    }

    /**
    * if header exist in HTTP request
    * and is a valid header
    * @return bool 
    */
    public function validHeader($httpHeader) {
        if(!is_array($httpHeader)){
            return false;
        } else {
            foreach($httpHeader as $key => $val) {
                if($key == 'Verification-Token') {
                    return true;
                }
            }
            return false;
        }
    }

    /**
    *  fetch Verification-token from HTTP header 
    */
    public function getVerificationToken($httpHeader) {
        foreach($httpHeader as $headerName=>$headerValue)
            {
                if(strcasecmp('Verification-token', $headerName) == 0)
                {
                    $verificationToken = $headerValue;
                    return $verificationToken;
                }
            }
        return null;
    }

    /**
    *  fetch Apikey from HTTP header 
    */
    public function getApikey($httpHeader) {
        foreach($httpHeader as $headerName=>$headerValue)
            {
                if(strcasecmp('Apikey', $headerName) == 0)
                {
                    $apikey = $headerValue;
                    return $apikey;
                }
            }
        return null;
    }

    public function encrypt($x509FilePath)
	    {		
		$publicKey = openssl_pkey_get_public("file://{$x509FilePath}");
		if($publicKey === false)
		{
            file_put_contents($this->logFile, "Error while loading X509 public key certificate! \n", FILE_APPEND);
			$errorMessage = "Error while loading X509 public key certificate! Reason:";
			while(($errorString = openssl_error_string()))
			{
				$errorMessage .= $errorString . "\n";
			}
			throw new Exception($errorMessage, self::ERROR_LOAD_X509_CERTIFICATE);
		}
		return $publicKey;
	}
}