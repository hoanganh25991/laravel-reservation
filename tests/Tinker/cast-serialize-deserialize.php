<?php
$r = App\Reservation::first();

$r_json = $r->toJson();

$r_data = json_decode($r_json, true);

