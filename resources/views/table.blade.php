@extends('templates.master')

@section('content')
    <div class="position-ref full-height">
        <div class="content">

            @include('templates.navbar')

            <div class="content-wrapper">
                <div class="container-fluid">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            Table
                        </li>
                    </ol>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="col-12 p-0 m-0 text-right">
                                <small>Updated at {{ $updatedAt }}</small>
                            </div>
                            <table class="table table-results" id="dataTable" width="100%" cellspacing="0" role="grid" aria-describedby="dataTable_info" style="width: 100%;">
                                <thead>
                                <tr role="row">
                                    <th colspan="2"></th>
                                    <th>P</th>
                                    <th>M</th>
                                    <th>V</th>
                                    <th>D</th>
                                    <th>L</th>
                                    <th>G</th>
                                    <th>R</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($results as $key => $result)
                                    <tr role="row">
                                        <td>{{ ++$key }}</td>
                                        <td class="text-left">
                                            <img class="mr-1" src="{{ $result->team->icon() }}" alt="{{ $result->team->team_name }}">
                                            {{ $result->team->team_name }}
                                        </td>
                                        <td><strong>{{ $result->points }}</strong></td>
                                        <td>{{ $result->getNumberOfMatches() }}</td>
                                        <td>{{ $result->won }}</td>
                                        <td>{{ $result->draw }}</td>
                                        <td>{{ $result->lost }}</td>
                                        <td>{{ $result->goals_pro }} : {{ $result->goals_against }}</td>
                                        <td>{{ number_format($result->getWonRatio(), 2) }}% / {{ number_format($result->getLostRatio(), 2) }}%</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <small>
                                P = Points;
                                M = Matches;
                                V = Victories;
                                D = Draws;
                                L = Loss;
                                G = Goals Pro / Against;
                                R = Win / Loss Ratio

                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection