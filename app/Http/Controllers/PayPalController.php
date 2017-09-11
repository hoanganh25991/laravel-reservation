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

    /** @var  Gateway $gateway */
    public $gateway;
    
    public function __construct(){}
    
    public function initGateway(){
        // Get paypal access token
        $deposit_config = Setting::depositConfig();
        $access_token   = $deposit_config(Setting::PAYPAL_TOKEN);
        // Init gateway
        $this->gateway  = new Gateway([
            'accessToken' => $access_token
        ]);
    }

    public static function validatePaymentRequest(ApiRequest $req){
        $validator =
            Validator::make($req->all(), [
                'confirm_id'          => 'required', // for reservation
                'tokenizationPayload' => 'required', // get client token, to know who he is
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

        $this->initGateway();
        
        $action_type = $req->get('type');

        switch($action_type){

            case Call::AJAX_PAYMENT_REQUEST:

                // Validate what need to handle
                $validator = PayPalController::validatePaymentRequest($req);

                if($validator->fails()){
                    $data = [];
                    $code = 422;
                    $msg  = Call::AJAX_PAYMENT_REQUEST_VALIDATE_FAIL;
                    break;
                }

                $reservation = Reservation::findByConfirmId($req->get('confirm_id'));

                if(is_null($reservation)){
                    $data = [];
                    $code = 422;
                    $msg  = Call::AJAX_PAYMENT_REQUEST_FIND_RESERVATION_FAIL;
                    break;
                }

                $already_paid = $reservation->payment_status == Reservation::PAYMENT_PAID;

                if($already_paid){
                    $data = [];
                    $code = 422;
                    $msg  = Call::AJAX_PAYMENT_REQUEST_RESERVATION_ALREADY_PAID;
                    break;
                }

                $amount = $reservation->deposit;

                $tokenizationPayload = json_decode($req->get('tokenizationPayload'), JSON_NUMERIC_CHECK);

                $paymentMethodNonce  = $tokenizationPayload['nonce'];

                // Call Paypal API to excecute this transaction
                $result =
                    $this->gateway->transaction()->sale([
                        'amount'             => $amount,
                        'paymentMethodNonce' => $paymentMethodNonce,
                    ]);

                if ($result->success) {

                    $transaction_id = $result->transaction->id;

                    // Store payment info
                    // payment_id :               used for API call, to capture later
                    // payment_authorization_id : transaction id as authorization case
                    //
                    // Get paypal details first
                    /** @var Transaction\PayPalDetails $paypal_details */
                    $paypal_details = $result->transaction->paypalDetails;
                    // Store Payment id
                    // payment_id only used for Paypal API to call void|charge...
                    $reservation->payment_id        = $transaction_id;
                    // Payment have many id, this is authorize-payment type, store this one
                    $reservation->payment_authorization_id = $paypal_details->authorizationId;
                    // Store Payment info
                    $reservation->payment_amount    = $amount;
                    $reservation->payment_currency  = $reservation->paypal_currency;
                    $reservation->payment_timestamp = Carbon::now(Setting::timezone());
                    $reservation->payment_status    = Reservation::PAYMENT_PAID;
                    //update status as RESERVED
                    $reservation->status            = Reservation::RESERVED;
                    $reservation->save();

                } else {

                    $msg = "Create authorize payment fail. Reservation confirm id: $reservation->confirm_id";

                    // Try to get exactly message from $result
                    try{

                        $msg .= $result->__get('message');

                    }catch(\Exception $e){}

                    throw new \Exception($msg);
                    //break;
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
     * @throws \Exception
     */
    public static function void($trasaction_id){
        // This action now REQUIRED permission level as administrator
        // Bring logic inside User model it self
        /* @var ReservationUser $user*/
        $user = Auth::user();

        if(!($user && $user->hasMasterReservationsPermissionOnCurrentOutlet())){
            throw new \Exception('Void authorization payment need master reservations permission');
        }

        // Check status to call void or refund
        $paypal_controller = new PayPalController;
        $paypal_controller->initGateway();

        $transaction = $paypal_controller->gateway->transaction()->find($trasaction_id);

        if(is_null($transaction)){
            throw new \Exception('Find transaction fail.');
        }

        switch($transaction->status){

            case Transaction::AUTHORIZED:
                $result = $paypal_controller->gateway->transaction()->void($trasaction_id);
                break;

            case Transaction::SETTLED:
                $result = $paypal_controller->gateway->transaction()->refund($trasaction_id);
                break;

            default:
				// This case happens bcs transaction status now, may be authorization_expired
				// So just self void this transaction in our database
				$result = (object)["success" => true];
                break;
        }

        if(isset($result) && $result->success){

            return true;

        }else{

            $msg = "Void transaction fail. Transaction id $trasaction_id. Status: $transaction->status";

            try{

                $msg .= $result->__get('message');

            }catch(\Exception $e){}

            throw new \Exception($msg);

            //return false;
        }

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
        // This action now REQUIRED permission level as administrator
        // Bring logic inside User model it self
        /* @var ReservationUser $user*/
        $user = Auth::user();

        if(!($user && $user->hasMasterReservationsPermissionOnCurrentOutlet())){
            throw new \Exception('Charge authorization payment need master reservations permission');
        }
        
        // Settle it down to get money
        $paypal_controller = new PayPalController;
        $paypal_controller->initGateway();
        // Call charge API
        $result = $paypal_controller->gateway->transaction()->submitForSettlement($trasaction_id);

        if($result->success){

            return true;

        }else{

            $msg = "Charge transaction fail. Transaction id: $trasaction_id.";

            try {

                $msg .= $result->__get('message');

            }catch(\Exception $e){}

            throw new \Exception($msg);

            //return false;
        }

        return false;
    }
    
    public static function voidBcsCustomerEditReservation($transaction_id){
        try{
            $success = PayPalController::voidWithoutPermission($transaction_id);
        }catch(\Exception $e){
            $success = false;
        }
        
        return $success;
    }
    
    public static function voidWithoutPermission($transaction_id){
        // Check status to call void or refund
        $paypal_controller = new PayPalController;
        $paypal_controller->initGateway();

        $transaction = $paypal_controller->gateway->transaction()->find($transaction_id);

        if(is_null($transaction)){
            throw new \Exception('Find transaction fail.');
        }

        switch($transaction->status){

            case Transaction::AUTHORIZED:
                $result = $paypal_controller->gateway->transaction()->void($transaction_id);
                break;

            case Transaction::SETTLED:
                $result = $paypal_controller->gateway->transaction()->refund($transaction_id);
                break;

            default:
		        // This case happens bcs transaction status now, may be authorization_expired
		        // So just self void this transaction in our database
		        $result = (object)["success" => true];
		        break;
        }

        if(isset($result) && $result->success){

            return true;

        }else{

            $msg = "Void transaction fail. Transaction id $transaction_id. ";

            try{

                $msg .= $result->__get('message');

            }catch(\Exception $e){}

            throw new \Exception($msg);

            //return false;
        }

        return false;
    }

}
