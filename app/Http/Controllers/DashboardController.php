<?php

namespace App\Http\Controllers;

use App\Group;
use App\Result;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($groupId = null)
    {
        if( ! empty($groupId) ){
            $group = Group::where('id', $groupId)
                ->with('matches')
                ->first();
        }

        if( empty($group) || empty($groupId) ){
            $group = Group::getNextGroup();
        }
        $matches = $group->matches;
        $allGroups = Group::all()->pluck('id');

        return view('index', compact('matches','allGroups','group'));
    }

    public function showTable()
    {
        $allGroups = Group::all()->pluck('id');
        $results = Result::query()
            ->with('team')
            ->orderBy('points', 'desc')
            ->orderBy('goals_diff', 'desc')
            ->orderBy('goals_pro', 'desc')
            ->orderBy('goals_against')
            ->get();

        return view('table',compact('results', 'allGroups'));
    }
}
