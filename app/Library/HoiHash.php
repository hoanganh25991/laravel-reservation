<?php
namespace App\Library;

use Hashids\Hashids;

class HoiHash {
    protected $hashIds;
    public function __construct(){
        //new Hashids(Setting::HASH_SALT, 7);
        //simple hash, which return same hash value
        $custom_alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $this->hashIds = new Hashids('', 7, $custom_alphabet);
    }

    public function encode($id){
        return $this->hashIds->encode($id);
    }

    public function decode($hash){
        return $this->hashIds->decode($hash)[0];
    }
};