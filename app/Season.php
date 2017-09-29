<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Season extends Model
{
    protected $table = 'seasons';

    protected $fillable = [
        'name', 'date_first_round', 'date_last_round',
        'league_id_api', 'league_shortcut_api'
    ];

    protected $dates = [
        'date_first_round', 'date_last_round'
    ];

    public function groups()
    {
        return $this->hasMany(Group::class);
    }
}
