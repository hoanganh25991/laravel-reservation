<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\OutletReservationSetting as Setting;

class ApiRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(){
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        
        return [
            //
        ];
    }
    
    public function fromApiGroup(){
        /**
         * Url means no query parameter
         * Uri is full
         */
        return preg_match('/api/', $this->url());
    }
}
