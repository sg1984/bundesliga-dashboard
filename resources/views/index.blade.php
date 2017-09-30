@extends('templates.master')

@section('content')
    <div class="position-ref full-height">
        <div class="content">

            @include('templates.navbar')

            <div class="content-wrapper">
                <div class="container-fluid">
                    <ol class="breadcrumb {{ $group->hasMatchDay() ? 'bg-info' : '' }}">
                        <li class="breadcrumb-item">
                            Group {{ $group->group_order }} {{ $group->hasMatchDay() ? ' | Match Day' : '' }}
                        </li>
                    </ol>

                    <div class="row">
                        <div class="col-sm-12">
                            <div class="col-12 p-0 m-0 text-right">
                                <small>Updated at {{ $group->getLastUpdatedDateTimeFormatted() }}</small>
                            </div>
                            <table class="table dataTable" id="dataTable" width="100%" cellspacing="0" role="grid" aria-describedby="dataTable_info" style="width: 100%;">
                                <tbody>
                                    @foreach($matchesGrupedByDay as $day => $matches)
                                        <tr>
                                            <td class="p-0" colspan="6"><small>{{ $day }}</small></td>
                                        </tr>
                                        @foreach($matches as $match)
                                            <tr role="row">
                                                <td>{{ $match->match_time_formatted }}</td>
                                                <td class="text-right"> {{ $match->homeTeam->name() }} </td>
                                                <td class="text-left"><img src="{{ $match->homeTeam->icon() }}" alt="{{ $match->homeTeam->name() }}"></td>
                                                <td class="text-center">
                                                    @if($match->isFinished())
                                                        <strong>{{ $match->getHomeScore() . ' x ' . $match->getVisitorScore() }}</strong>
                                                    @else
                                                        x
                                                    @endif
                                                </td>
                                                <td class="text-right"><img src="{{ $match->visitorTeam->icon() }}" alt="{{ $match->visitorTeam->name() }}"></td>
                                                <td class="text-left">{{ $match->visitorTeam->name() }}</td>
                                            </tr>
                                        @endforeach
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