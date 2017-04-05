<?php
namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Reservation;
use Braintree\Gateway;
use App\Traits\ApiResponse;
use App\Http\Requests\ApiRequest;
use App\Libraries\HoiAjaxCall as Call;
use Illuminate\Support\Facades\Validator;
use App\OutletReservationSetting as Setting;


class PayPalController extends HoiController{

    use ApiResponse;
    
    protected $gateway;
    
    public function __construct(){
        //get token from config
        $access_token = env('PAYPAL_ACCESS_TOKEN');
        
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
        $clientToken = $this->gateway->clientToken()->generate();

        return $clientToken;
    }


    /**
     * Handle payment request from customer
     * Deposit required when reservation pax over threshold
     * @see App\OutletReservationSetting::DEPOSIT_THRESHOLD_PAX
     * @param ApiRequest $req
     * @return $this
     */
    public function handlePayment(ApiRequest $req){
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
                $data = [];
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

}
