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
     * Show the current group matches
     *
     * @param null $groupId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
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

    /**
     * Call the table with the current result from the Bundesliga
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
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

    /**
     * Shows the about page, with information about the application
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
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

    /**
     * Populate the database
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
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
