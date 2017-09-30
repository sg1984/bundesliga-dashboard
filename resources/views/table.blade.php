@extends('templates.master')

@section('content')
    <div class="position-ref full-height">
        <div class="content">

            @include('templates.navbar')

            <div class="content-wrapper">
                <div class="container-fluid">
                    <!-- Breadcrumbs-->
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            Table
                        </li>
                    </ol>
                    <div class="row">
                        <div class="col-sm-12">
                            <table class="table table-bordered dataTable" id="dataTable" width="100%" cellspacing="0" role="grid" aria-describedby="dataTable_info" style="width: 100%;">
                                <thead>
                                <tr role="row">
                                    <th>#</th>
                                    <th>Team</th>
                                    <th>M</th>
                                    <th>V</th>
                                    <th>D</th>
                                    <th>L</th>
                                    <th>Goals</th>
                                    <th>Points</th>
                                    <th>W/L Ratio</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($results as $key => $result)
                                    <tr role="row">
                                        <td>{{ ++$key }}</td>
                                        <td>{{ $result->team->team_name }}</td>
                                        <td>{{ $result->getNumberOfMatches() }}</td>
                                        <td>{{ $result->won }}</td>
                                        <td>{{ $result->draw }}</td>
                                        <td>{{ $result->lost }}</td>
                                        <td>{{ $result->goals_pro }} : {{ $result->goals_against }}</td>
                                        <td>{{ $result->points }}</td>
                                        <td>{{ number_format($result->getWonRatio(), 2) }}% / {{ number_format($result->getLostRatio(), 2) }}%</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection