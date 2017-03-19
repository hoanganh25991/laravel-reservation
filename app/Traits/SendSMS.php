<?php
namespace App\Traits;

use Illuminate\Support\Facades\Log;

trait SendSMS{
    private function _padTelephone($telephone){
        if(substr($telephone, 0, 3) != "+65"){
            if(substr($telephone, 0, 2) != "65"){
                $telephone = "+65" . $telephone;
            }else{
                $telephone = "+" . $telephone;
            }
        }
        return $telephone;
    }

    private function sendOverHoiio($telephone, $message, $sender_name){
        //pad the phone number
        //$telephone = $this->_padTelephone($telephone);
        //Log::info('Sending SMS');
        Log::info($message);
        if(env('APP_ENV') != 'production'){
            //Log::info('SMS on dev environment, fake return true as success sending');
            return true;
        }

        $hoiioAppId = "n0rwoAWlLNvTZpXo";
        $hoiioAccessToken = "OsiquwPsGPkpXrxV";
        $sendSmsURL = "https://secure.hoiio.com/open/sms/send";
        $fields = array(
            'app_id' => urlencode($hoiioAppId),
            'access_token' => urlencode($hoiioAccessToken),
            'dest' => urlencode($telephone),
            // send SMS to this phone

            'msg' => urlencode($message),
            // message content in SMS
            'sender_name' => $sender_name
        );

        // form up variables in the correct format for HTTP POST
        $fields_string = "";
        foreach($fields as $key => $value){
            $fields_string .= $key . '=' . $value . '&';
        }

        $fields_string = rtrim($fields_string, '&');

        /* initialize cURL */
        $ch = curl_init();

        /* set options for cURL */
        curl_setopt($ch, CURLOPT_URL, $sendSmsURL);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);

        /* execute HTTP POST request */
        $raw_result = curl_exec($ch);
        $result = json_decode($raw_result);     // parse JSON formatted result

        /* close connection */
        curl_close($ch);

        if($result->status == "success_ok"){
            return true;
        }else{
            return false;
        }
    }
}