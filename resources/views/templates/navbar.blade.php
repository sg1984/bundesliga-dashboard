<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top" id="mainNav">
    <a class="navbar-brand" href="{{ route('dashboard') }}">Bundesliga Dashboard</a>
    <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="navbar-nav navbar-sidenav" id="exampleAccordion">
                @if(isset($loadInfo))
                    <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Dashboard">
                        <a class="nav-link" href="{{ route('load-info') }}">
                            <i class="fa fa-fw fa-dashboard"></i>
                            <span class="nav-link-text">Load Info</span>
                        </a>
                    </li>
                @else
                    <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Dashboard">
                        <a class="nav-link" href="{{ route('table') }}">
                            <i class="fa fa-fw fa-dashboard"></i>
                            <span class="nav-link-text">Table</span>
                        </a>
                    </li>
                    @if(isset($allGroups))
                        <li class="nav-item" title="Menu Levels">
                            <a class="nav-link">
                                <span class="nav-link-text">Rounds</span>
                            </a>
                        </li>
                        <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Rounds">
                            <div class="row m-0">
                                @foreach($allGroups as $groupId)
                                    <div class="col-3 text-center {{ (isset($group) && $group->id == $groupId) ? 'bg-info' : '' }}">
                                        <a class="nav-link" href="{{ route('dashboard', [$groupId]) }}">
                                            <span class="nav-link-text">{{ $groupId }}</span>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </li>
                    @endif
                @endif
            </ul>
        </div>
</nav>