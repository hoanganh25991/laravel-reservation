<?php
namespace App\Library;

use Hashids\Hashids;

class HoiHash {
    protected $hashIds;
    public function __construct(){
        //new Hashids(Setting::HASH_SALT, 7);
        //simple hash, which return same hash value
        $this->hashIds = new Hashids('', 7);
    }

    public function encode($id){
        return $this->hashIds->encode($id);
    }

    public function decode($hash){
        return $this->hashIds->decode($hash)[0];
    }
};