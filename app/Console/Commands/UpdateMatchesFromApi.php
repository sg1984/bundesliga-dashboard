<?php

namespace App\Console\Commands;

use App\Services\BundesligaApi;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateMatchesFromApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'matches:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the matches info from database with information from API';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        DB::beginTransaction();
        try {
            BundesligaApi::updateMatchesInfo(true);
            DB::commit();
        }
        catch (\Exception $e) {
            DB::rollback();
            echo 'Error while updating matches from API:: ' . $e->getMessage() . PHP_EOL;
        }

        return;
    }
}
