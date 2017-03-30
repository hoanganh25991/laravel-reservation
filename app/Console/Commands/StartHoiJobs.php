<?php

namespace App\Console\Commands;

use App\Jobs\HoiJobs;
use App\Jobs\HoiJobsV2;
use Illuminate\Console\Command;

class StartHoiJobs extends Command{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hoijobs:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(){
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(){
//        new HoiJobsV2;
//        new HoiJobs;
        echo "＼＿ヘ(ᐖ◞)､";
        $hoi_jobs = new HoiJobs;
        dispatch($hoi_jobs);

        $pm2_cmd = 'pm2 start pm2-queue-jobs.config.js';
        exec($pm2_cmd);
    }
}
