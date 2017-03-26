<?php
$s = App\Session::with('timings')->first();

$t = $s->timings->first();

$s->session_name = 'One more night';

$t->timing_name = 'what the fuck';

$s->save();