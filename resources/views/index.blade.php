<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Bundesliga Dashboard</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
</head>
<body>
<div class="position-ref full-height">
    <div class="content">
        <h1 class="">
            Bundesliga Dashboard
        </h1>
        <div class="row">
            <div class="col-4">
                @for($i=1;$i<35;$i++)
                    <p>
                        <a href="{{ route('dashboard.group', [2017, $i]) }}">Group {{ $i }}</a>
                    </p>
                @endfor
            </div>
            <div class="col-8">
                @foreach($matches as $match)
                    <small>{{ $match->getDateTimeString() }}</small>
                    <br>
                    <p>
                        <img src="{{ $match->getHome()->icon() }}" alt="{{ $match->getHome()->name() }}">
                        {{ $match->getHome()->name() }}
                        @if($match->isFinished())
                            <strong>{{ $match->getHomeScore() . ' x ' . $match->getVisitorScore() }}</strong>
                        @else
                            x
                        @endif
                        {{ $match->getVisitor()->name() }}
                        <img src="{{ $match->getVisitor()->icon() }}" alt="{{ $match->getVisitor()->name() }}">
                    </p>
                    <hr>
                @endforeach
            </div>
        </div>
    </div>
</div>
</body>
</html>
