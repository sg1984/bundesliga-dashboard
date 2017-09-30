<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Match extends Model
{
    protected $table = 'matches';
    protected $fillable = [
        'group_id', 'date_time', 'is_finished',
        'is_today', 'home_team_id', 'visitor_team_id',
        'score_home_team', 'score_visitor_team', 'match_id_api',
        'result_updated',
    ];

    protected $dates = ['date_time'];

    protected $casts = [
        'is_finished'       => 'boolean',
        'is_today'          => 'boolean',
        'result_updated'    => 'boolean',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function homeTeam()
    {
        return $this->belongsTo(Team::class, 'home_team_id');
    }

    public function visitorTeam()
    {
        return $this->belongsTo(Team::class, 'visitor_team_id');
    }

    public function scopeFinished($query)
    {
        return $query->where('is_finished', true);
    }

    public function scopeNotFinished($query)
    {
        return $query->where('is_finished', false);
    }

    public function scopeIsToday($query, $is_today = true)
    {
        return $query->where('is_today', $is_today);
    }

    public function scopeByMatchIdFromApi($query, $matchIdFromApi)
    {
        return $query->where('match_id_api', $matchIdFromApi);
    }

    private function hasHomeTeamWon()
    {
        if( $this->getHomeScore() > $this->getVisitorScore() ){

            return true;
        }

        return false;
    }

    private function hasVisitorTeamWon()
    {
        if( $this->getHomeScore() < $this->getVisitorScore() ){

            return true;
        }
        return false;
    }

    public function getDateTimeString()
    {
        return $this->date_time->format('d/m/Y H:i');
    }

    public function getMatchDateFormattedAttribute()
    {
        return $this->date_time->format('d/m/Y');
    }

    public function getMatchTimeFormattedAttribute()
    {
        return $this->date_time->format('H:i');
    }

    public function isFinished()
    {
        return $this->is_finished;
    }

    public function getHomeScore()
    {
        return intval($this->score_home_team);
    }

    public function getVisitorScore()
    {
        return intval($this->score_visitor_team);
    }

    public function matchIsToday()
    {
        return $this->is_today;
    }

    /**
     * Verify if the results from this match was used to update the table of results
     *
     * @return mixed
     */
    public function matchUsedToUpdateResultFromTeams()
    {
        return $this->result_updated;
    }

    /**
     * Create a match with the information from the API
     *
     * @param Group $group
     * @param       $matchFromApi
     * @return Match
     */
    public static function createFromApiData(Group $group, $matchFromApi)
    {
        $homeTeam = Team::query()->byTeamIdFromApi($matchFromApi->Team1->TeamId)->first();
        if( empty($homeTeam) ){
            $homeTeam = Team::createFromApiData($matchFromApi->Team1);
        }

        $visitorTeam = Team::query()->byTeamIdFromApi($matchFromApi->Team2->TeamId)->first();
        if( empty($visitorTeam) ){
            $visitorTeam = Team::createFromApiData($matchFromApi->Team2);
        }

        $match = new self();
        $match->match_id_api = $matchFromApi->MatchID;
        $match->date_time = Carbon::createFromFormat('Y-m-d\TH:i:s', $matchFromApi->MatchDateTime);
        $match->is_today = $match->date_time->isToday();
        $match->is_finished = $matchFromApi->MatchIsFinished;
        $match->homeTeam()->associate($homeTeam);
        $match->visitorTeam()->associate($visitorTeam);
        $match->group()->associate($group);

        if( $match->isFinished() ){
            $match->score_home_team = $matchFromApi->MatchResults[1]->PointsTeam1;
            $match->score_visitor_team = $matchFromApi->MatchResults[1]->PointsTeam2;
        }

        $match->save();

        return $match;
    }

    /**
     * Update the match with information retrieved from API
     *
     * @param $matchFromApi
     * @return $this
     */
    public function updateFromApiData($matchFromApi)
    {
        $this->match_id_api = $matchFromApi->MatchID;
        $this->date_time = Carbon::createFromFormat('Y-m-d\TH:i:s', $matchFromApi->MatchDateTime);
        $this->is_today = $this->date_time->isToday();
        $this->is_finished = $matchFromApi->MatchIsFinished;

        if( $this->isFinished() ){
            $this->score_home_team = $matchFromApi->MatchResults[1]->PointsTeam1;
            $this->score_visitor_team = $matchFromApi->MatchResults[1]->PointsTeam2;
        }

        $this->save();

        return $this;
    }

    /**
     * Analyse the result from the match and update the results table information from the teams
     *
     * @return $this
     */
    public function analyseResultIfFinished()
    {
        if( $this->isFinished() && ! $this->matchUsedToUpdateResultFromTeams() ){

            $resultHomeTeam = $this->homeTeam
                ->getResultFromSeason( $this->group->season );

            $resultVisitorTeam = $this->visitorTeam
                ->getResultFromSeason( $this->group->season );

            if( empty($resultHomeTeam) ){
                $resultHomeTeam = new Result();
                $resultHomeTeam->season()->associate($this->group->season);
                $resultHomeTeam->team()->associate($this->homeTeam);
                $resultHomeTeam->save();
            }

            if( empty($resultVisitorTeam) ){
                $resultVisitorTeam = new Result();
                $resultVisitorTeam->season()->associate($this->group->season);
                $resultVisitorTeam->team()->associate($this->visitorTeam);
                $resultVisitorTeam->save();
            }

            $resultHomeTeam = Result::where('id', $resultHomeTeam->id)->first();
            $resultVisitorTeam = Result::where('id', $resultVisitorTeam->id)->first();

            $scoreHomeTeam = [
                'goals_pro' => 0,
                'goals_against' => 0,
            ];

            $scoreVisitorTeam = [
                'goals_pro' => 0,
                'goals_against' => 0,
            ];

            if( ! empty($resultHomeTeam->goals_pro) ){
                $scoreHomeTeam['goals_pro'] = intval($resultHomeTeam->goals_pro);
            }

            if( ! empty($resultHomeTeam->goals_against) ){
                $scoreHomeTeam['goals_against'] = intval($resultHomeTeam->goals_against);
            }

            if( ! empty($resultVisitorTeam->goals_pro) ){
                $scoreVisitorTeam['goals_pro'] = intval($resultVisitorTeam->goals_pro);
            }

            if( ! empty($resultVisitorTeam->goals_against) ){
                $scoreVisitorTeam['goals_against'] = intval($resultVisitorTeam->goals_against);
            }

            $scoreHomeTeam['goals_pro'] += $this->getHomeScore();
            $scoreHomeTeam['goals_against'] += $this->getVisitorScore();
            $scoreVisitorTeam['goals_pro'] += $this->getVisitorScore();
            $scoreVisitorTeam['goals_against'] += $this->getHomeScore();

            $resultHomeTeam->update([
                'goals_pro'     => $scoreHomeTeam['goals_pro'],
                'goals_against' => $scoreHomeTeam['goals_against'],
                'goals_diff'    => intval($scoreHomeTeam['goals_pro']) - intval($scoreHomeTeam['goals_against']),
            ]);

            $resultVisitorTeam->update([
                'goals_pro'     => $scoreVisitorTeam['goals_pro'],
                'goals_against' => $scoreVisitorTeam['goals_against'],
                'goals_diff'    => intval($scoreVisitorTeam['goals_pro']) - intval($scoreVisitorTeam['goals_against']),
            ]);

            if( $this->hasHomeTeamWon() ){
                $resultHomeTeam->teamWonMatch();
                $resultVisitorTeam->teamLostMatch();
            }
            elseif( $this->hasVisitorTeamWon() ){
                $resultHomeTeam->teamLostMatch();
                $resultVisitorTeam->teamWonMatch();
            }
            else{
                $resultHomeTeam->teamDrawMatch();
                $resultVisitorTeam->teamDrawMatch();
            }

            $resultHomeTeam->save();
            $resultVisitorTeam->save();
            $this->update([
                'result_updated' => true,
            ]);
        }

        $this->save();

        return $this;
    }
}
