<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HoiModel extends Model
{
    /**
     * The name of the "created at" column.
     *
     * @var string
     */
    const CREATED_AT = 'created_timestamp';

    /**
     * The name of the "updated at" column.
     *
     * @var string
     */
    const UPDATED_AT = 'modified_timestamp';
}
