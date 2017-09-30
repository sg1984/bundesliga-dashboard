<?php

namespace App;

use App\Exceptions\NoMatchesOnDatabaseException;
use App\Services\BundesligaApi;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Group extends Model
{
    protected $table = 'groups';

    protected $fillable = [
        'season_id', 'group_order', 'group_id_api'
    ];

    public function season()
    {
        return $this->belongsTo(Season::class);
    }

    public function matches()
    {
        return $this->hasMany(Match::class);
    }

    public function todayMatches()
    {
        return $this->matches()->isToday();
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

        if( empty($nextMatch) ){
            throw new NoMatchesOnDatabaseException('THE DATABASE IS EMPTY!!!');
        }

        $nextGroup = self::query()
            ->where('id', $nextMatch->group_id)
            ->with('matches')
            ->first();

        return $nextGroup;
    }

    public function getGroupOrder()
    {
        return $this->group_order;
    }

    public function hasMatchDay()
    {
        return $this->todayMatches()->count() > 0;
    }

    public function updateInfoFromApi($updateAll = false, $showLog = false)
    {
        DB::beginTransaction();
        try {
            $matchesFromApi = BundesligaApi::getGroupMatchesFromApi($this->season, $this);

            foreach ($matchesFromApi as $matchFromApi) {
                $match = Match::query()->byMatchIdFromApi($matchFromApi->MatchID)->first();
                if ($showLog) {
                    echo 'Updating data from match ' . $match->id . PHP_EOL;
                }

                $match->updateFromApiData($matchFromApi);
                $match->analyseResultIfFinished($updateAll);
            }
            DB::commit();
        }
        catch (\Exception $e) {
            DB::rollback();

            throw $e;
        }

        return $this;
    }

    public function getLastUpdatedDateTimeFormatted()
    {
        $lastUpdatedMatch = $this->matches->sortByDesc('updated_at')->first();

        return $lastUpdatedMatch->updated_at->format('d/m/Y H:i');
    }
}
