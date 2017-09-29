<?php

namespace App\Services;

use App\Group;
use App\League;
use Carbon\Carbon;
use GuzzleHttp\Client;

class BundesligaApi
{
    protected $apiClient = null;
    protected $league = null;
    protected $year = null;
    protected $urlApi = null;

    public function __construct( League $league = null, $year = null )
    {
        $this->apiClient = new Client();
        $this->urlApi = config('services.bundesliga.matches');

        if( empty( $year ) ){
            $this->year = Carbon::now()->format('Y');
        }
        else{
            $this->year = $year;
        }

        if( empty($league) ){
            $this->league = new League($this->year);
        }
        else{
            $this->league = $league;
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

    public function getLeagueShortName()
    {
        return $this->getLeague()->getLeague();
    }

    public function getApiClient()
    {
        return $this->apiClient;
    }

    public static function getCurrentGroupFromApi()
    {
        $apiService = new self();
        $currentGroupMatchesFromApi = $apiService->getGroupMatchesFromApi();

        $lastMatchGroup = new Group($apiService->getLeague());
        $lastMatchGroup->setMatchesToGroup($currentGroupMatchesFromApi);

        return $lastMatchGroup;
    }

    public function getGroupMatchesFromApi(Group $group = null)
    {
        $url = config('services.bundesliga.matches') . $this->getLeagueShortName();

        if( ! empty($group) ){
            $url .= '/' . $group->getLeague()->getYear() . '/' . $group->getGroupOrder();
        }

        $response = $this
            ->getApiClient()
            ->get($url)
            ->getBody()
            ->getContents();

        return collect(json_decode($response));
    }
}