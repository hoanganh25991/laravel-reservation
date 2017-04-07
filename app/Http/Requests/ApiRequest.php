<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\OutletReservationSetting as Setting;

class ApiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(){
//        return false;
//        $this->uri
        $brand_id = $this->route('brand_id');
        
        if(is_numeric($brand_id)){
            Setting::injectBrandId($brand_id);
        }
        
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
