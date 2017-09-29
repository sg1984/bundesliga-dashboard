<?php

namespace App\Http\Controllers;

use App\Group;
use App\League;
use App\Services\BundesligaApi;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $currentGroup = BundesligaApi::getCurrentGroupFromApi();
        $matches = $currentGroup->getMatches();

        return view('index', compact('matches'));
    }

    public function showGroupMatches($year = null, $group = null)
    {
        $apiService = new BundesligaApi();
        $viewGroup = new Group($apiService->getLeague(), $group);
        $groupMatches = $apiService->getGroupMatchesFromApi($viewGroup);
        $matches = $viewGroup->setMatchesToGroup($groupMatches);

        return view('index', compact('matches'));
    }

    public function allGames()
    {
        $client = New Client();

        $response = $client->get('https://www.openligadb.de/api/getmatchdata/bl1/2017');
        $matches = json_decode($response->getBody()->getContents());

        return view('dashboard', compact('matches'));
    }
}
