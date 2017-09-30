<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $table = 'groups';

    protected $fillable = [
        'season_id', 'group_order', 'group_id_api',
        'date_start', 'date_end',
    ];

    protected $dates = [
        'date_start', 'date_end'
    ];

    public function season()
    {
        return $this->belongsTo(Season::class);
    }

    public function matches()
    {
        return $this->hasMany(Match::class);
    }

    public static function createFromApiData(Season $season, $groupInfoFromApi)
    {
        $group = new self([
            'group_order'  => $groupInfoFromApi->GroupOrderID,
            'group_id_api' => $groupInfoFromApi->GroupID,
        ]);
        $group->season()->associate($season);
        $group->save();

        return $group;
    }

    public function scopeByGroupIdFromApi($query, $groupIdFromApi)
    {
        return $query->where('group_id_api', $groupIdFromApi);
    }

    public static function getNextGroup()
    {
        $nextMatch = Match::query()
            ->notFinished()
            ->first();

        $nextGroup = self::query()
            ->where('id', $nextMatch->group_id)
            ->with('matches')
            ->first();

        return $nextGroup;
    }
}
