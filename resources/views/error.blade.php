@extends('templates.master')

@section('content')
    <div class="position-ref full-height">
        <div class="content">

            @include('templates.navbar')

            <div class="content-wrapper">
                <div class="container-fluid">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            We had a problem...
                        </li>
                    </ol>
                    <div class="col-12">
                        <p>I know, you heard this before, but this never happen with me before...</p>
                        <p>And by this, I mean:</p>
                        <h3 class="bg-danger text-center p-2">{{ $error }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection