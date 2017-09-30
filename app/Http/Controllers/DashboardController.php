<?php

namespace App\Http\Controllers;

use App\Exceptions\NoMatchesOnDatabaseException;
use App\Group;
use App\Result;
use App\Services\BundesligaApi;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($groupId = null)
    {
        try {
            if (!empty($groupId)) {
                $group = Group::where('id', $groupId)
                    ->with('matches')
                    ->first();
            }

            if (empty($group) || empty($groupId)) {
                $group = Group::getNextGroup();
            }

            if( $group->hasMatchDay() ){
                $group = $group->updateInfoFromApi();
            }

            $matchesGrupedByDay = $group->matches->groupBy('match_date_formatted');
            $allGroups = Group::all()->pluck('id');

            return view('index', compact('matchesGrupedByDay', 'allGroups', 'group'));
        }
        catch(NoMatchesOnDatabaseException $e){

            return redirect()->route('about');
        }
        catch (\Exception $e) {
            $error = $e->getMessage();

            return view('error', compact('error'));
        }
    }

    public function showTable()
    {
        try {
            $allGroups = Group::all()->pluck('id');
            $results = Result::query()
                ->with('team')
                ->orderBy('points', 'desc')
                ->orderBy('goals_diff', 'desc')
                ->orderBy('goals_pro', 'desc')
                ->orderBy('goals_against')
                ->get();

            $updatedAt = $results->first()->updated_at->format('d/m/Y H:i');

            return view('table', compact('results', 'allGroups','updatedAt'));
        }
        catch (\Exception $e) {
            $error = $e->getMessage();

            return view('error', compact('error'));
        }
    }

    public function about()
    {
        try {
            return view('about', ['loadInfo' => true]);
        }
        catch (\Exception $e) {
            $error = $e->getMessage();

            return view('error', compact('error'));
        }
    }

    public function loadInfo()
    {
        try {
            BundesligaApi::populateDatabase();

            return redirect()->route('dashboard');
        }
        catch (\Exception $e) {
            $error = $e->getMessage();

            return view('error', compact('error'));
        }
    }
}
