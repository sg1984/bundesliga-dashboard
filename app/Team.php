<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $table = 'teams';
    protected $fillable = [
        'team_name', 'team_short_name', 'icon_url', 'team_id_api'
    ];

    protected $name = null;
    protected $icon = null;

    public function __construct($teamInfo)
    {
        $this->name = $teamInfo->TeamName;
        $this->icon = $teamInfo->TeamIconUrl;
    }

    public function name()
    {
        return $this->name;
    }

    public function icon()
    {
        return $this->icon;
    }
}
