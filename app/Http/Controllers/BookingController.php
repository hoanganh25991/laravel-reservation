<?php

namespace App\Http\Controllers;

use Validator;
use App\Outlet;
use App\Timing;
use App\Session;
use Carbon\Carbon;
use App\Reservation;
use App\Traits\ApiUtils;
use App\Traits\ApiResponse;
use App\Http\Requests\ApiRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Libraries\HoiAjaxCall as Call;
use App\OutletReservationSetting as Setting;
//use App\Libraries\HoiAjaxCall as Call;

class BookingController extends HoiController {

    use ApiUtils;
    use ApiResponse;

    /**
     * Cache filenames
     */
    const DATES_WITH_AVAILABLE_TIME = 'DATES_WITH_AVAILABLE_TIME';
    const SHOULD_UPDATE_DATES_WITH_AVAILABLE_TIME = 'SHOULD_UPDATE_DATES_WITH_AVAILABLE_TIME';

    /** @var  Collection $reserved_reservations */
    public $reserved_reservations;

    /** @var  int $reservation_pax_size */
    public $reservation_pax_size;

    /** @var  bool $recalculate */
    public $recalculate = false;

    /** @var array booking_condition */
    public $booking_condition = null;

    public function bookingPaxSize(){
        return $this->booking_condition['adult_pax'] + $this->booking_condition['children_pax'];
    }

    public function bookingHasChildren(){
        return $this->booking_condition['children_pax'] > 0;
    }

    /**
     * Validate data before search availabe time
     * @param array $condition
     * @return mixed
     */
    public function validateBookingCondition($condition){
        $validator = Validator::make($condition, [
            'outlet_id'    => 'required|numeric',
            'adult_pax'    => 'required|numeric',
            'children_pax' => 'required|numeric'
        ]);
        
        return $validator;
    }

    /**
     * Store customer booking condition in property
     * Easy to access through filter availableTime
     * @param array $condition
     * @see BookingController::validateBookingCondition
     * @see BookingController::availableTime
     */
    public function setUpBookingConditions($condition = []){
        /**
         * Store Outlet in session for reuse as global query scope
         */
        Setting::injectOutletId($condition['outlet_id']);

        $this->booking_condition = $condition;
    }

    public function bookingStillAvailable(ApiRequest $req){
        $this->setUpBookingConditions(
            $req->only([
                'outlet_id',
                'adult_pax',
                'children_pax'
            ])
        );

        $booking_date   = Carbon::createFromFormat('Y-m-d H:i:s', $req->get('reservation_timestamp'), Setting::timezone());
        $available_time = $this->availableTime();

        if(!isset($available_time[$booking_date->format('Y-m-d')])){
            return false;
        }

        $available_time_on_booking_date = $available_time[$booking_date->format('Y-m-d')];

        /** @var Collection $available_chunk */
        $available_chunk =
            $available_time_on_booking_date
                ->filter(function($chunk) use($booking_date){
                    return $chunk->time == $booking_date->format('H:i');
                })->values();

        return $available_chunk->isNotEmpty();
    }

    public function bookingInOverallRange(ApiRequest $req){
        $overall = $req->get('adult_pax') + $req->get('children_pax');

        $settings_config = Setting::settingsConfig();
        $overall_min_pax = $settings_config(Setting::OVERALL_MIN_PAX);
        $overall_pax_pax = $settings_config(Setting::OVERALL_MAX_PAX);

        return ($overall >= $overall_min_pax)
                &&  ($overall <= $overall_pax_pax);
    }

    /**
     * Finding available time from customer booking conditions
     * @return mixed
     */
    public function availableTime(){
        $date_with_available_time = $this->loadDatesWithAvailableTimeFromCache() ?: $this->buildDatesWithAvailableTime();

        /**
         * Change chunk time capacity base on already reservations
         */
        $this->reserved_reservations = Reservation::reservedGroupByDateTimeCapacity();

        $date_with_available_time
            ->each(function($chunks, $date){
                $chunks
                    ->each(function($chunk) use($date){
                        $time = $chunk->time;
                        foreach(Timing::CAPACITY_X as $capacity){
                            $group_name = Reservation::groupNameByDateTimeCapacity($date, $time, $capacity);
                            try{
                                $reserved_cap     = $this->reserved_reservations[$group_name];
                                $chunk->$capacity = $chunk->$capacity - $reserved_cap;
                            }catch(\Exception $e){}
                        }
                    });
            });

        /**
         * Base on user booking size, filter tables are busy
         * Only accpet capacity > 0 as available
         */
        $dates_with_available_time_capacity =
            $date_with_available_time
                ->map->filter(function($chunk){
                    $reservation_pax_size = $this->bookingPaxSize();
                    $chunk->max_pax       = $chunk->max_pax ?: Setting::TIMING_MAX_PAX;
                    $cap_name             = Timing::getCapacityName($reservation_pax_size);

                    $is_cap_available   = ($chunk->$cap_name > 0) && ($chunk->max_pax >= $reservation_pax_size);
                    $is_chilren_allowed = $chunk->children_allowed || !$this->bookingHasChildren();

                    $available          = $is_cap_available && $is_chilren_allowed;

                    return $available;
                })
                ->map->values();

        $default = $this->defaultDatesWithAvailableTime();

        $dates_with_available_time_capacity =
            $default->merge($dates_with_available_time_capacity);

        return $dates_with_available_time_capacity;
    }

    public function loadDatesWithAvailableTimeFromCache(){
        if($this->shouldUseCache()){
            Log::info('Using cache');

            $file_name = static::cacheFilename(static::DATES_WITH_AVAILABLE_TIME);
            $val =  Cache::get($file_name);

            if(is_null($val)){Log::info('Cache DATES_WITH_AVAILABLE_TIME null');}

            return $val;
        }

        return null;
    }

    private function defaultDatesWithAvailableTime(){
        $date_range = Setting::dateRange();

        $default = collect([]);

        $current = $date_range[0]->copy();
        while($current->lte($date_range[1])){
            $default[$current->format('Y-m-d')] = collect([]);
            //increase loop
            $current->addDay();
        }

        return $default;
    }

    /**
     * @return mixed
     */
    public function buildDatesWithAvailableTime(){
        /**
         * Explain step
         * 1. Session fetch from DB
         * @see App\Session::scopeMayAvailableSession
         *
         * 2. Assign date to session
         * @see App\Session::assignDate
         *
         * 3. Filter base on date
         * @see App\Session::availableToBook
         *
         * 4. Group session by date
         *
         * 5. Only get timings of session on each [group by date]
         *
         * Example return of $timing_by_date
         * @example "example/booking-controler_@available-time.html"
         */
        $timings_by_date =
            Session
                ::mayAvailableSession()->get()
                ->map->assignDate()->collapse()
                ->filter->availableToBook()
                ->groupBy(function($session){return $session->date->format('Y-m-d');})
                ->map(function($sessions_by_date){return $sessions_by_date->map->timings->collapse();});

        $return =
            $timings_by_date->map(function($timings_in_date, $date_string){
                /**
                 * Base on arrival time & interval, each timing chunked into small piece
                 */
                $chunks  = $timings_in_date->map->chunk->collapse();

                $ordered_chunks = $chunks->sortBy(function($c){return $this->getMinutes($c->time);})->values();

                /**
                 * Special timing chunk will override on normal one
                 */
                $merged_chunks =
                    $ordered_chunks->reduce(function($carry, $item){
                        /**
                         * Push first item
                         */
                        $pre_item = $carry->last();
                        //should return immediately to prevent call on null of following step
                        if(is_null($pre_item)){
                            $carry->push($item);
                            return $carry;
                        }

                        $alreday_has = $carry->filter(function($last_item)use($item){return $last_item->time == $item->time;})->count() > 0;

                        /**
                         * overlap item is special, so override on pre_item
                         * bcs item sort out by order
                         * 2 item at same time > special item chose
                         */
                        $pre_item = $carry->last();
                        $new_item_is_special_than_pre = ($pre_item->session_type == Session::NORMAL_SESSION
                            && $item->session_type == Session::SPECIAL_SESSION);

                        $overlap_item_is_special = $alreday_has && $new_item_is_special_than_pre;

                        if($overlap_item_is_special)
                            $carry->pop();

                        /**
                         * Decide push item
                         */
                        $push_new = !$alreday_has || $overlap_item_is_special;

                        if($push_new)
                            $carry->push($item);


                        return $carry;
                    }, collect([]));

                /**
                 * Only push item which satisfied its own interval
                 * After chunk with minimum interval, to match special over normal session
                 * Need loop back to get only which statisfied its own interval
                 */
                $fixed_interval_chunks =
                    $merged_chunks->reduce(function($carry, $item){
                        /**
                         * Push first item
                         */
                        $pre_item = $carry->last();
                        //should return immediately to prevent call on null of following step
                        if(is_null($pre_item)){
                            $carry->push($item);
                            return $carry;
                        }

                        /**
                         * Satisfied interval >>> should push
                         */
                        $delta_time_with_pre = abs(Session::getMinutes($pre_item->time) - Session::getMinutes($item->time));
                        $satisfied_interval  = $delta_time_with_pre >= $pre_item->interval_minutes;

                        /**
                         * New item must pushed, when item special than pre_item
                         */
                        $new_item_is_special_than_pre = ($pre_item->session_type == Session::NORMAL_SESSION
                            && $item->session_type == Session::SPECIAL_SESSION);
                        $new_item_must_pushed = $new_item_is_special_than_pre && !$satisfied_interval;

                        if($new_item_must_pushed)
                            $carry->pop();

                        /**
                         * Respect first arrival
                         */
                        $delta_time_with_first_arrival = abs(Session::getMinutes($item->time) - Session::getMinutes($item->first_arrival_time));
                        $respect_first_arrival = ($delta_time_with_first_arrival % $item->interval_minutes) == 0;

                        /**
                         * Check push new
                         */
                        $push_new = ($satisfied_interval || $new_item_must_pushed) && $respect_first_arrival;

                        if($push_new)
                            $carry->push($item);

                        return $carry;
                    }, collect([]));

                /**
                 * Get buffer config
                 */
                /** @var string $buffer_config */
                $buffer_config = Setting::bufferConfig();
                $min_hours_slot_time    = $buffer_config(Setting::MIN_HOURS_IN_ADVANCE_SLOT_TIME);
                $min_hours_session_time = $buffer_config(Setting::MIN_HOURS_IN_ADVANCE_SESSION_TIME);

                $satisfied_prior_time_chunks = $fixed_interval_chunks;

                /**
                 * Compute day time on today with current checking session time
                 */
                $today = Carbon::now(Setting::timezone());
                $today_in_hour = $this->getMinutes($today->format('H:i:s')) / 60;
                $current_date =  Carbon::createFromFormat('Y-m-d', $date_string, Setting::timezone());
                $diff_less_than_a_day = $today->diffInDays($current_date, false) == 0;

                /**
                 * Care on hours, only check for
                 * session different time form current booking less than a day
                 */
                if($diff_less_than_a_day){
                    $satisfied_prior_time_chunks =
                        $fixed_interval_chunks->filter(function($item) use($min_hours_slot_time, $min_hours_session_time, $today_in_hour){
                            $item_in_hour = $this->getMinutes($item->time) / 60;
                            $diff_in_hour = $item_in_hour - $today_in_hour;

                            $satisfied_in_advance_slot_time    = $diff_in_hour >= $min_hours_slot_time;
                            //$satisfied_in_advance_session_time = $diff_in_hour >= $min_hours_session_time;

                            //return $satisfied_in_advance_slot_time
                            //       && $satisfied_in_advance_session_time;

                            return $satisfied_in_advance_slot_time;
                        })->values();
                }


                return $satisfied_prior_time_chunks;
            });

        /**
         * Save cache before move on
         */
        $file_name = static::cacheFilename(static::DATES_WITH_AVAILABLE_TIME);
        Cache::put($file_name, $return, 24 * 60);//expire in day

        return $return;
    }

    /**
     * New special session come on that day
     * Or change on normal session
     * Let to cache not updated
     * Should recalculate
     */
    public function shouldUseCache(){
//        if(env('APP_ENV') != 'production'){
//            return false;
//        }
//
//        $filename  = static::cacheFileName(static::SHOULD_UPDATE_DATES_WITH_AVAILABLE_TIME);
//        $shouldUpdateCache = Cache::pull($filename, false);
//
//        return !$shouldUpdateCache;
        return false;
    }

    /**
     * Compute dates with available time is hard
     * cache if it should be
     * filename consistent by call this function to get
     * @param $key
     * @param null $oulet_id
     * @return string
     */
    
    public static function cacheFilename($key, $oulet_id = null){
        $filename = '';

        switch($key){
            case BookingController::DATES_WITH_AVAILABLE_TIME:
                $today        = Carbon::now(Setting::timezone());
                $outlet_id    = $oulet_id ?: Setting::outletId();
                $today_string = $today->format('Y-m-d');
                $filename     = BookingController::DATES_WITH_AVAILABLE_TIME."_outlet_{$outlet_id}_{$today_string}";
                break;
            case BookingController::SHOULD_UPDATE_DATES_WITH_AVAILABLE_TIME:
                $outlet_id = $oulet_id ?: Setting::outletId();
                $filename  = BookingController::SHOULD_UPDATE_DATES_WITH_AVAILABLE_TIME."_outlet_{$outlet_id}";
                break;
        }

        return $filename;
    }

    /**
     * Booking Form step 1
     * @param ApiRequest $req
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getBookingForm(ApiRequest $req){
        if($req->method() == 'POST'){
            $action_type = $req->get('type');

            switch($action_type){
                default:
                    $data = [];
                    $code = 200;
                    $msg  = Call::AJAX_UNKNOWN_CASE;
                    break;
                /**
                 * Customer query to get available time
                 */
                case Call::AJAX_SEARCH_AVAILABLE_TIME:
                    /* @var Validator $validator*/
                    $validator = $this->validateBookingCondition($req->all());

                    if($validator->fails()){
                        $data = $validator->getMessageBag()->toArray();
                        $code = 422;
                        $msg  = Call::AJAX_BOOKING_CONDITION_VALIDATE_FAIL;
                        break;
                    }

                    /**
                     * Inject customer booking condition into booking controller
                     */
                    $this->setUpBookingConditions($req->all());

                    /**
                     * Compute available time
                     */
                    $available_time = $this->availableTime();

                    /**
                     * @warn need update to has it own statusMsg
                     * rather than implicit tell available time on return
                     */
                    $data = $available_time;
                    $code = 200;
                    $msg  = Call::AJAX_AVAILABLE_TIME_FOUND;
                    break;
                /**
                 * Customer submit complete form
                 * to create reservation
                 */
                case Call::AJAX_SUBMIT_BOOKING:
                    $validator = Reservation::validateOnCRUD($req->all());

                    if($validator->fails()){
                        $data = $validator->getMessageBag()->toArray();
                        $code = 422;
                        $msg  = Call::AJAX_RESERVATION_VALIDATE_FAIL;
                        break;
                    }

                    Setting::injectOutletId($req->get('outlet_id'));

                    /**
                     * If booking out of overall min|max pax
                     */
                    if(!$this->bookingInOverallRange($req)){
                        $data = ['pax' => 'total pax out of overall_range'];
                        $code = 422;
                        $msg  = Call::AJAX_RESERVATION_VALIDATE_FAIL;
                        break;
                    }

                    /**
                     * Recheck if customer with reservation info still available
                     * Customer may search through any condition
                     * But only Submit hit, info send
                     * In that longtime, not sure reservation still available
                     */
                    if(!$this->bookingStillAvailable($req)){
                        $data = [];
                        $code = 422;
                        $msg  = Call::AJAX_RESERVATION_NO_LONGER_AVAILABLE;
                        break;
                    }

                    $reservation = new Reservation($req->all());

                    //Store reservation
                    $reservation->save();

                    /**
                     * Case: Reservation with deposit require
                     */
                    if($reservation->requiredDeposit()){
                        //$deposit      = $reservation->deposit;
                        //$confirm_id   = $reservation->confirm_id;
                        $paypal_token = (new PayPalController)->generateToken();

                        $data = compact('reservation', 'paypal_token');
                        $code = 422;
                        $msg  = Call::AJAX_RESERVATION_REQUIRED_DEPOSIT;
                        break;
                    }

                    /**
                     * Normal case: Reservation created
                     * RESERVED
                     */
                    $confirm_id =  $reservation->confirm_id;

                    //$data = compact('confirm_id');
                    $data = compact('reservation');
                    $code = 200;
                    $msg  = Call::AJAX_RESERVATION_SUCCESS_CREATE;
                    break;
            }

            return $this->apiResponse($data, $code, $msg);
        }

        /**
         * Inject Brand id through route uri
         */
        $brand_id = $req->route()->parameter('brand_id');
        Setting::injectBrandId($brand_id);

        //Handle get
        $outlet  = [];
        $outlets = Outlet::all();
        $overall_min_pax = Setting::DEFAULT_OVERALL_MIN_PAX;
        $overall_max_pax = Setting::DEFAULT_OVERALL_MAX_PAX;

        /**
         * Self pick the first one
         */
        $outlet_x = $outlets->first();

        if($outlet_x){
            Setting::injectOutletId($outlet_x->id);
            $setting_config  = Setting::settingsConfig();
            $overall_min_pax = $setting_config(Setting::OVERALL_MIN_PAX);
            $overall_max_pax = $setting_config(Setting::OVERALL_MAX_PAX);

            $outlet = [
                'id'   => $outlet_x->id,
                'name' => $outlet_x->outlet_name
            ];
        }

        /**
         * Server state
         * Base on that frontend client render
         */
        $state = [
            'base_url'        => $req->url(),
            'outlet'          => $outlet,
            'outlets'         => $outlets,
            'overall_min_pax' => $overall_min_pax,
            'overall_max_pax' => $overall_max_pax,
        ];

        return view('reservations.booking-form', compact('state'));
    }
}
