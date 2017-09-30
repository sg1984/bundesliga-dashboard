<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $table = 'teams';
    protected $fillable = [
        'team_name', 'team_short_name', 'icon_url', 'team_id_api'
    ];

    public function results()
    {
        return $this->hasMany(Result::class);
    }

    public function scopeByTeamIdFromApi($query, $teamIdFromApi)
    {
        return $query->where('team_id_api', $teamIdFromApi);
    }

    public static function createFromApiData($teamInfo)
    {
        return self::create([
            'team_name' => $teamInfo->TeamName,
            'team_short_name' => $teamInfo->ShortName,
            'icon_url' => $teamInfo->TeamIconUrl,
            'team_id_api' => $teamInfo->TeamId,
        ]);
    }

    public function getResultFromSeason(Season $season)
    {
        return $this
            ->results()
            ->bySeason($season)
            ->first();
    }

    public function icon()
    {
        if( empty($this->icon_url) ){
            return '';
        }

        return $this->icon_url;
    }

    public function name()
    {
        if( ! empty( $this->team_short_name ) ){

            return $this->team_short_name;
        }

        return $this->team_name;
    }
}
