<?php
namespace App\Http\Controllers;

use App\Brand;
use Carbon\Carbon;
use App\Reservation;
use Braintree\Gateway;
use Braintree\Transaction;
use App\Traits\ApiResponse;
use App\Http\Requests\ApiRequest;
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
        $brand_id = Setting::brandId();
        /** @var Brand $brand */
        $brand    = Brand::find($brand_id);

        if(is_null($brand)){
            throw new \Exception("Paypal can not find brand with id $brand_id");
        }

        $access_token = $brand->paypal_token;

        if(is_null($access_token)){
            throw new \Exception('Paypal access token not found');
        }

        $this->gateway = new Gateway([
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

    public function testBrandIdInjected(){
        $this->initGateway();

        return "Gateway init";
    }

    /**
     * Handle payment request from customer
     * Deposit required when reservation pax over threshold
     * @see App\OutletReservationSetting::DEPOSIT_THRESHOLD_PAX
     * @param ApiRequest $req
     * @return $this
     */
    public function handlePayment(ApiRequest $req){
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
     * Refund a transaction
     * This means that if the transaction still not settle down
     * >>> VOID IT
     * >>> TOTALLY SETTLE DOWN > REFUND
     * @param $trasaction_id
     * @return bool
     */
    public static function refund($trasaction_id){
        /**
         * Check status to call void or refund
         */
        $paypal_controller = new PayPalController;
        $paypal_controller->initGateway();
        try{
            $transaction = $paypal_controller->gateway->transaction()->find($trasaction_id);

            switch($transaction->escrowStatus){
                case Transaction::ESCROW_HOLD_PENDING:
                    $result = $paypal_controller->gateway->transaction()->void($trasaction_id);
                    break;
                case Transaction::ESCROW_RELEASED:
                    $result = $paypal_controller->gateway->transaction()->refund($trasaction_id);
                    break;
//                default:
//                    $result = (object)[
//                        'success' => false
//                    ];
//                    break;
            }

            if($result->success){
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
     */
    public static function charge($trasaction_id){
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
