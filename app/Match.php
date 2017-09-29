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
        'score_home_team', 'score_home_team', 'match_id_api',
    ];

    protected $dates = ['date_time'];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function homeTeam()
    {
        return $this->hasOne(Team::class, 'home_team_id');
    }

    public function visitorTeam()
    {
        return $this->hasOne(Team::class, 'visitor_team_id');
    }

    protected $home = null;
    protected $visitor = null;
    protected $dateTime = null;
    protected $scoreHome = null;
    protected $scoreVisitor = null;
    protected $isFinished = false;

    public function __construct($matchInfo)
    {
        $this->setDateTime($matchInfo->MatchDateTimeUTC);
        $this->home = new Team($matchInfo->Team1);
        $this->visitor = new Team($matchInfo->Team2);
        if( $matchInfo->MatchIsFinished ){
            $this->isFinished = $matchInfo->MatchIsFinished;
            $this->scoreHome = $matchInfo->MatchResults[1]->PointsTeam1;
            $this->scoreVisitor = $matchInfo->MatchResults[1]->PointsTeam2;
        }
    }

    public function getDateTimeString()
    {
        return $this->dateTime->format('d/m/Y H:i');
    }

    public function getHome()
    {
        return $this->home;
    }

    public function getVisitor()
    {
        return $this->visitor;
    }

    public function getHomeScore()
    {
        return $this->scoreHome;
    }

    public function getVisitorScore()
    {
        return $this->scoreVisitor;
    }

    public function isFinished()
    {
        return $this->isFinished;
    }

    public function setDateTime($dateTimeUTC, $format = \DateTime::ATOM)
    {
        $this->dateTime = Carbon::createFromFormat($format ,$dateTimeUTC);
    }
}
