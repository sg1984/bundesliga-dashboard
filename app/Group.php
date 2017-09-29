<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Group extends Model
{
    protected $table = 'groups';

    protected $fillable = [
        'season_id', 'group_order', 'group_id_api'
    ];

    protected $league = null;
    protected $groupOrder = null;
    protected $matches = null;

    public function season()
    {
        return $this->belongsTo(Season::class);
    }

    public function matches()
    {
        return $this->hasMany(Match::class);
    }

    public function __construct(League $league, $groupOrder = null)
    {
        $this->league = $league;
        if( ! empty($groupOrder) ){
            $this->groupOrder = $groupOrder;
        }
    }

    public function getLeague()
    {
        return $this->league;
    }

    public function getGroupOrder()
    {
        return $this->groupOrder;
    }

    public function getNextGroup()
    {
        return new Group(
            $this->league,
            $this->groupOrder + 1
        );
    }

    public function getPreviousGroup()
    {
        return new Group(
            $this->league,
            $this->groupOrder - 1
        );
    }

    public function getMatches()
    {
        return $this->matches;
    }

    public function getMatchesFromApi()
    {
        $apiUrl = config('services.bundesliga.matches');
        $leagueYear = $this->league->getYear();
        $url = $apiUrl . $this->league->getLeague() . '/' . $leagueYear . '/'  . $this->groupOrder;
        $response = $this
            ->league
            ->getApiClient()
            ->get($url)
            ->getBody()
            ->getContents();

        return collect(json_decode($response));
    }

    public function setMatchesToGroup($collectionOfMatches)
    {
        $matches = [];
        foreach ($collectionOfMatches as $matchInfo){
            if( empty($this->groupOrder) ){
                $this->groupOrder = $matchInfo->Group->GroupOrderID;
            }

            $matches[] = new Match($matchInfo);
        }

        $this->matches = collect($matches);
        return $this->matches;
    }
}
