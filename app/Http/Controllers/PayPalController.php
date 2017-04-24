<?php
namespace App\Http\Controllers;

use App\Brand;
use Carbon\Carbon;
use App\Reservation;
use Braintree\Gateway;
use App\ReservationUser;
use Braintree\Transaction;
use App\Traits\ApiResponse;
use App\Http\Requests\ApiRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Libraries\HoiAjaxCall as Call;
use Illuminate\Support\Facades\Validator;
use App\OutletReservationSetting as Setting;


class PayPalController extends HoiController{

    use ApiResponse;
    
    public $gateway;
    
    public function __construct(){
        /**
         * Controller construct is not reliable to run ANYTHING
         * WHY???
         * BCS Route parse action string > bind (controller, action)
         * As list url > action
         * >>> controller construct init before any route parse run 
         */
    }
    
    public function initGateway(){
        //$outlet_id      = Setting::outletId();
        $setting_config = Setting::settingsConfig();
        $access_token   = $setting_config(Setting::PAYPAL_TOKEN);
        $this->gateway  = new Gateway([
            'accessToken' => $access_token
        ]);
    }

    public static function validatePaymentRequest(ApiRequest $req){
        $validator =
            Validator::make($req->all(), [
                'confirm_id'          => 'required', //for reservation
                'tokenizationPayload' => 'required'
            ]);

        return $validator;
    }

    public function generateToken(){
        $this->initGateway();
        $clientToken = $this->gateway->clientToken()->generate();

        return $clientToken;
    }

    /**
     * Handle payment request from customer
     * Deposit required when reservation pax over threshold
     * @see App\OutletReservationSetting::DEPOSIT_THRESHOLD_PAX
     * @param ApiRequest $req
     * @return $this
     * @throws \Exception
     */
    public function handlePayment(ApiRequest $req){
        //test end point
        if($req->method() == 'GET'){
            $this->initGateway();
            
            return 'Gateway success init';
        }
        
        $this->initGateway();
        
        $action_type = $req->get('type');

        switch($action_type){
            case Call::AJAX_PAYMENT_REQUEST:

                /**
                 * Validate what need to handle
                 */
                $validator = PayPalController::validatePaymentRequest($req);

                if($validator->fails()){
                    $data = [];
                    $code = 422;
                    $msg  = Call::AJAX_PAYMENT_REQUEST_VALIDATE_FAIL;
                    break;
                }

                /**
                 * Make payment
                 * as pending to capture
                 */

                //Find out reservation to build up info
                $reservation_id = Setting::hash()->decode($req->get('confirm_id'));

                /** @var Reservation $reservation */
                $reservation    = Reservation::find($reservation_id);

                if(is_null($reservation)){
                    $data = [];
                    $code = 422;
                    $msg  = Call::AJAX_PAYMENT_REQUEST_FIND_RESERVATION_FAIL;
                    break;
                }

                $amount = $reservation->deposit;

                $tokenizationPayload = json_decode($req->get('tokenizationPayload'), JSON_NUMERIC_CHECK);

                $paymentMethodNonce  = $tokenizationPayload['nonce'];

                $result =
                    $this->gateway->transaction()->sale([
                        'amount'             => $amount,
                        'paymentMethodNonce' => $paymentMethodNonce,
                    ]);

                if ($result->success) {
                    $transaction_id = $result->transaction->id;

                    /**
                     * Store payment id
                     * To capture later
                     */
                    $reservation->payment_id        = $transaction_id;
                    $reservation->payment_amount    = $amount;
                    $reservation->payment_timestamp = Carbon::now(Setting::timezone());
                    $reservation->payment_status    = Reservation::PAYMENT_PAID;
                    //update status as RESERVED
                    $reservation->status            = Reservation::RESERVED;
                    $reservation->save();

                } else {
                    $err  = var_export($result->errors);
                    $data = $err;
                    $code = 422;
                    $msg  = Call::AJAX_PAYMENT_REQUEST_TRANSACTION_FAIL;
                    break;
                }

                //everything is fine
                $data = compact('reservation');
                $code = 200;
                $msg  = Call::AJAX_PAYMENT_REQUEST_SUCCESS;
                break;

            default:
                $data = [];
                $code = 200;
                $msg  = Call::AJAX_UNKNOWN_CASE;
                break;
        }

        return $this->apiResponse($data, $code, $msg);
    }

    /**
     * Wrapper function to check administrator role
     * @return bool
     * @throws \Exception
     */
    public static function administratorRoleRequired(){
        /** @var ReservationUser $user */
        $user = Auth::user();
        $msg = "Only administrator can void/charge. ";

        if(is_null($user)){
            $msg .= "No loggined user found. ";

            throw new \Exception($msg);
        }

        if(!$user->isAdministrator()){
            $msg .= "Current user: $user->display_name. ";
            $msg .= "Permission level: $user->role. ";

            throw new \Exception($msg);
        }
        
        return true;
    }

    /**
     * Refund a transaction
     * This means that if the transaction still not settle down
     * >>> VOID IT
     * >>> TOTALLY SETTLE DOWN > REFUND
     * @param $trasaction_id
     * @return bool
     * @throws \Exception
     */
    public static function void($trasaction_id){
        /** This action now REQUIRED permission level as administrator */
        try{
            PayPalController::administratorRoleRequired();
        }catch(\Exception $e){
            throw $e;
        }

        /**
         * Check status to call void or refund
         */
        $paypal_controller = new PayPalController;
        $paypal_controller->initGateway();
        try{
            $transaction = $paypal_controller->gateway->transaction()->find($trasaction_id);

            switch($transaction->status){
                case Transaction::AUTHORIZED:
                    $result = $paypal_controller->gateway->transaction()->void($trasaction_id);
                    break;
                case Transaction::SETTLED:
                    $result = $paypal_controller->gateway->transaction()->refund($trasaction_id);
                    break;
                default:
                    break;
            }

            if(isset($result) && $result->success){
                return true;
            }else{
                //log error
                Log::info('fail refund');
                return false;
            }
        //exception throw when no transaction found
        }catch(\Exception $e){
            //log or do sth with error
            Log::info('fail find transaction');
            return false;
        }
        //debug here to review transaction

        return false;
    }

    /**
     * Charge a transaction or capture|settle down it
     * A transaction when create for help customer can get refund
     * Default status as pending
     * @param $trasaction_id
     * @return bool
     * @throws \Exception
     */
    public static function charge($trasaction_id){
        /** This action now REQUIRED permission level as administrator */
        try{
            PayPalController::administratorRoleRequired();
        }catch(\Exception $e){
            throw $e;
        }
        
        /**
         * Settle it down to get money
         */
        $paypal_controller = new PayPalController;
        $paypal_controller->initGateway();
        $result = $paypal_controller->gateway->transaction()->submitForSettlement($trasaction_id);

        if($result->success){
            return true;
        }else{
            $error = var_export($result->errors);
            Log::info('charge fail');
            return false;
            //log or do sth
        }

        return false;
    }

}
