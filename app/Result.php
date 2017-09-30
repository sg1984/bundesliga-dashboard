<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    const POINTS_WON = 3;
    const POINTS_DRAW = 1;

    protected $table = 'results';
    protected $fillable = [
        'season_id', 'team_id', 'won',
        'lost', 'draw', 'goals_pro', 'goals_against',
        'goals_diff', 'points'
    ];

    public function season()
    {
        return $this->belongsTo(Season::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function scopeBySeason($query, Season $season)
    {
        return $query->where('season_id', $season->id);
    }

    public function scopeByTeam($query, Team $team)
    {
        return $query->where('team_id', $team->id);
    }

    public function getWon()
    {
        if( empty(intval($this->won)) ){
            return 0;
        }

        return intval($this->won);
    }

    public function getLost()
    {
        if( empty(intval($this->lost)) ){
            return 0;
        }

        return intval($this->lost);
    }

    public function getDraw()
    {
        if( empty(intval($this->draw)) ){
            return 0;
        }

        return intval($this->draw);
    }

    public function getPoints()
    {
        if( empty(intval($this->points)) ){
            return 0;
        }

        return intval($this->points);
    }

    public function getNumberOfMatches()
    {
        return $this->getWon() + $this->getLost() + $this->getDraw();
    }

    public function teamWonMatch()
    {
        $won = $this->getWon() + 1;
        $points = $this->getPoints() + Result::POINTS_WON;
        $this->update([
            'won'    => $won,
            'points' => $points,
        ]);
        $this->save();

        return;
    }

    public function teamLostMatch()
    {
        $lost = $this->getLost() + 1;
        $this->update([
            'lost'   => $lost,
        ]);
        $this->save();

        return;
    }

    public function teamDrawMatch()
    {
        $draw = $this->getDraw() + 1;
        $points = $this->getPoints() + Result::POINTS_DRAW;
        $this->update([
            'draw'   => $draw,
            'points' => $points
        ]);
        $this->save();

        return;
    }

    public function getWonRatio()
    {
        return (($this->getWon() + ( $this->getDraw() * 0.5 )) / $this->getNumberOfMatches()) * 100;
    }

    public function getLostRatio()
    {
        return ($this->getLost() / $this->getNumberOfMatches()) * 100;
    }

    public function clearResultsInfo()
    {
        $this->update([
            'won'           => 0,
            'lost'          => 0,
            'draw'          => 0,
            'goals_pro'     => 0,
            'goals_against' => 0,
            'goals_diff'    => 0,
            'points'        => 0,
        ]);
        $this->save();

        return $this;
    }

    public static function resetResultsFromSeason(Season $season)
    {
        $results = $season->results;
        foreach ($results as $result){
            echo 'Reset results from team ' . $result->team->name() . PHP_EOL;
            $result->clearResultsInfo();
        }

        return;
    }
}
