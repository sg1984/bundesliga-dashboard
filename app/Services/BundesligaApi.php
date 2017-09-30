<?php

namespace App\Services;

use App\Group;
use App\Match;
use App\Result;
use App\Season;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;

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

    protected function callApiService($url)
    {
        return $this
            ->getApiClient()
            ->get($url)
            ->getBody()
            ->getContents();
    }

    public static function getGroupMatchesFromApi(Season $season, Group $group)
    {
        $apiService = new self();

        $url = config('services.bundesliga.matches') . $season->getShortName();
        $url .= '/' . $season->getYear() . '/' . $group->getGroupOrder();

        $response = $apiService->callApiService($url);

        return collect(json_decode($response));
    }

    public static function populateDatabase()
    {
        DB::beginTransaction();
        try {
            $apiService = new self();
            $year = Carbon::now()->format('Y');
            $url = config('services.bundesliga.matches') . 'bl1/' . $year;
            $response = $apiService->callApiService($url);

            $allMatches = collect(json_decode($response));
            $firstMatch = $allMatches->first();
            $lastMatch = $allMatches->last();

            $season = Season::first();
            if (empty($season)) {
                $season = Season::createFromApiData($firstMatch, $lastMatch);
            }

            foreach ($allMatches as $matchFromApi) {
                $groupFromApi = $matchFromApi->Group;
                $group = Group::query()->byGroupIdFromApi($groupFromApi->GroupID)->first();
                if (empty($group)) {
                    $group = Group::createFromApiData($season, $matchFromApi->Group);
                }
                $group->load('season');

                $match = Match::query()->byMatchIdFromApi($matchFromApi->MatchID)->first();
                if (empty($match)) {
                    $match = Match::createFromApiData($group, $matchFromApi);
                }

                $match->analyseResultIfFinished();
            }
            DB::commit();
        }
        catch (Exception $e) {
            DB::rollback();

            throw $e;
        }

        return;
    }

    public static function updateMatchesInfo($showLog = false)
    {
        DB::beginTransaction();
        try {
            $season = Season::getCurrentSeason();
            $apiService = new self();
            Result::resetResultsFromSeason($season);

            $url = config('services.bundesliga.matches') . $season->getShortName() . '/' . $season->getYear();
            $response = $apiService->callApiService($url);

            $allMatches = collect(json_decode($response));

            foreach ($allMatches as $matchFromApi){
                $groupFromApi = $matchFromApi->Group;
                $group = Group::query()->byGroupIdFromApi($groupFromApi->GroupID)->first();
                if (empty($group)) {
                    $group = Group::createFromApiData($season, $matchFromApi->Group);
                }
                $group->load('season');

                $match = Match::query()->byMatchIdFromApi($matchFromApi->MatchID)->first();
                if (empty($match)) {
                    $match = Match::createFromApiData($group, $matchFromApi);
                }

                if ($showLog) {
                    echo 'Updating data from match ' . $match->id . PHP_EOL;
                }

                $match->updateFromApiData($matchFromApi);
                $match->analyseResultIfFinished();
            }

            DB::commit();
        }
        catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }

        return;
    }
}