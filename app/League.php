<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class League extends Model
{
    const FIRST = 'bl1';
    const SECOND = 'bl2';
    const THIRD = 'bl3';

    protected $league = null;
    protected $year = null;

    public function __construct($year = null, $league = League::FIRST)
    {
        $this->league = $league;
        if( empty( $year ) ){
            $this->year = Carbon::now()->format('Y');
        }
        else{
            $this->year = $year;
        }
    }

    public function getLeague()
    {
        return $this->league;
    }

    public function getYear()
    {
        return $this->year;
    }

    public function setYear($value)
    {
        $this->year = $value;
    }

    public static function getAllMatches()
    {
        $league = new self();
        $url = config('services.bundesliga.matches') . $league->getLeague() . '/' . $league->getYear();
        $response = $league
            ->getApiClient()
            ->get($url)
            ->getBody()
            ->getContents();

        $allMatches = json_decode($response);

        return $allMatches;
    }

    public function getGroupMatchesFromApi(Group $group = null)
    {
        $league = new self();
        $url = config('services.bundesliga.matches') . $league->getLeague();

        if(!empty($group)){
            $url .= '/' . $group;
        }

        $response = $league
            ->getApiClient()
            ->get($url)
            ->getBody()
            ->getContents();

        return collect(json_decode($response));
    }

    public static function getLastMatchGroup()
    {
        $league = new self();
        $url = config('services.bundesliga.matches') . $league->getLeague();
        $response = $league
            ->getApiClient()
            ->get($url)
            ->getBody()
            ->getContents();

        $matchGroupInfo = collect(json_decode($response));

        $firstMatch = $matchGroupInfo->first();
        $groupFirstMatch = $firstMatch->Group;

        $dateUTC = $firstMatch->MatchDateTimeUTC;
        $date = Carbon::createFromFormat(\DateTime::ATOM ,$dateUTC);
        $league->setYear($date->format('Y'));

        $lastMatchGroup = new Group(
            $league,
            $groupFirstMatch->GroupOrderID
        );

        return $lastMatchGroup;
    }
}
