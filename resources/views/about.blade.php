@extends('templates.master')

@section('content')
    <div class="position-ref full-height">
        <div class="content">

            @include('templates.navbar')

            <div class="content-wrapper">
                <div class="container-fluid">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            About
                        </li>
                    </ol>
                    <div class="col-12">
                        <p>This is just a sistem that shows the results from current season of Bundesliga.</p>
                        <p>But it looks like the databases are empty :-/</p>
                        <p>
                            The first time that it is loaded, it takes a little bit because it is retrieving the information
                            from an API, puting it in the database and analysing the results from the matches.
                        </p>
                        <p>
                            If you want to load the info from web and start to use it, please <a href="{{ route('load-info') }}">click here</a> or in the menu "Load info".
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection