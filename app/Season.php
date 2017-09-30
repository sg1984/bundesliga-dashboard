<?php

namespace App;

use Carbon\Carbon;
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

    public function results()
    {
        return $this->hasMany(Result::class);
    }

    public static function createFromApiData($firstMatch, $lastMatch)
    {
        return self::create([
            'name'                => $firstMatch->LeagueName,
            'date_first_round'    => Carbon::createFromFormat('Y-m-d\TH:i:s', $firstMatch->MatchDateTime),
            'date_last_round'     => Carbon::createFromFormat('Y-m-d\TH:i:s', $lastMatch->MatchDateTime),
            'league_id_api'       => $firstMatch->LeagueId,
            'league_shortcut_api' => 'bl1',
        ]);
    }

    public function getShortName()
    {
        return $this->league_shortcut_api;
    }

    public function getYear()
    {
        return $this->date_first_round->format('Y');
    }

    public static function getCurrentSeason()
    {
        $year = Carbon::now()->format('Y');
        $yearLike = $year . '%';

        $season = Season::query()
            ->where('date_first_round', 'like', $yearLike)
            ->first();

        if( empty($season) ){
            throw new \Exception('No season found in database');
        }

        return $season;
    }
}
