<?php

namespace App\Services;

use App\Group;
use App\Match;
use App\Season;
use Carbon\Carbon;
use GuzzleHttp\Client;

class BundesligaApi
{
    protected $apiClient = null;
    protected $urlApi = null;

    public function __construct()
    {
        $this->apiClient = new Client();
        $this->urlApi = config('services.bundesliga.matches');
    }

    public function getApiClient()
    {
        return $this->apiClient;
    }

//    public static function getCurrentGroupFromApi()
//    {
//        $apiService = new self();
//        $currentGroupMatchesFromApi = $apiService->getGroupMatchesFromApi();
//
//        $lastMatchGroup = new Group($apiService->getLeague());
//        $lastMatchGroup->setMatchesToGroup($currentGroupMatchesFromApi);
//
//        return $lastMatchGroup;
//    }
//
//    public function getGroupMatchesFromApi(Group $group = null)
//    {
//        $url = config('services.bundesliga.matches') . $this->getLeagueShortName();
//
//        if( ! empty($group) ){
//            $url .= '/' . $group->getLeague()->getYear() . '/' . $group->getGroupOrder();
//        }
//
//        $response = $this
//            ->getApiClient()
//            ->get($url)
//            ->getBody()
//            ->getContents();
//
//        return collect(json_decode($response));
//    }

    public static function populateDatabase()
    {
        $apiService = new self();
        $year = Carbon::now()->format('Y');
        $url = config('services.bundesliga.matches') . 'bl1/' . $year;

        echo 'Getting all matches from the API...' . PHP_EOL;
        $response = $apiService
            ->getApiClient()
            ->get($url)
            ->getBody()
            ->getContents();

        $allMatches = collect(json_decode($response));
        $firstMatch = $allMatches->first();
        $lastMatch = $allMatches->last();

        echo count($allMatches) . ' matches returned...' . PHP_EOL;

        $season = Season::first();
        if( empty($season) ){
            $season = Season::createFromApiData($firstMatch, $lastMatch);
        }
        echo 'Updating info from season ' . $season->name . PHP_EOL;

        foreach ($allMatches as $matchFromApi){
            $groupFromApi = $matchFromApi->Group;
            $group = Group::query()->byGroupIdFromApi($groupFromApi->GroupID)->first();
            if( empty($group) ){
                $group = Group::createFromApiData($season, $matchFromApi->Group);
            }
            $group->load('season');

            $match = Match::query()->byMatchIdFromApi($matchFromApi->MatchID)->first();
            if( empty($match) ){
                $match = Match::createFromApiData($group, $matchFromApi);
            }
//            echo 'Updating data from match ' . $match->id . PHP_EOL;

            $match->analyseResultIfFinished();
        }
    }
}