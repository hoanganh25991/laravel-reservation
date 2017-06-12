<?php
namespace App\Traits;

trait CleanString{
    public function clean($string) {
        // Removes special chars
        $no_special = preg_replace('/[^A-Za-z0-9\-\s]/', ' ', $string);
        return trim($no_special);
    }
}
