<?php
namespace App\Traits;

use App\BrandCredit;
use App\Exceptions\SMSException;
use Illuminate\Support\Facades\Log;

trait SendSMS{
    /**
     * Send SMS through Hoiio
     * @param $telephone
     * @param $message
     * @param $sender_name
     * @return bool
     */
    public function sendOverNexmo($telephone, $message, $sender_name){
        $success_sent = $this->_sendMessage($telephone, $message, $sender_name);

        /**
         * When success sent
         * Update credit to keep track
         */
        if($success_sent){
            $this->callUpdateBrandCredit();
        }

        /**
         * Resturn back sent sms status
         */
        return $success_sent;
    }
    public function _sendMessage($telephone, $message, $sender_name){
        if(is_null(env('NEXMO_KEY')) || is_null(env('NEXMO_SECRET'))){
            throw new SMSException('NEXMO_KEY or NEXMO_SECRET not set in .env');
        }

        if(env('APP_ENV') != 'production'){
            return true;
        }

        $telephone = $this->removePlusSign($telephone);

        $url = 'https://rest.nexmo.com/sms/json?' . http_build_query(
                [
                    'api_key'    => env('NEXMO_KEY'),
                    'api_secret' => env('NEXMO_SECRET'),
                    'to'         => $telephone,
                    'from'       => $sender_name,
                    'text'       => $message
                ]
            );

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);   //don't verify SSL server
        $response = curl_exec($ch);

        //Decode the json object you retrieved when you ran the request.
        $decoded_response = json_decode($response, true);

        /**
         * This check is fast check for ONE MESSAGE
         */
//        foreach ( $decoded_response['messages'] as $message ) {
//            if ($message['status'] == 0) {
//                error_log("Success " . $message['message-id']);
//            } else {
//                error_log("Error {$message['status']} {$message['error-text']}");
//            }
//        }
        $sent_message = $decoded_response['messages'][0];
        
        if($sent_message['status'] == 0){
            return true;
        }else{
            return $response;
        }
    }

    public function removePlusSign($telephone){
        if(substr($telephone, 0, 1) === '+'){
            return substr($telephone, 1);
        }

        return $telephone;
    }

    /**
     * Save send sms credit in DB
     */
    public function callUpdateBrandCredit(){
        (new BrandCredit)->updateSMSCredit();
    }
}