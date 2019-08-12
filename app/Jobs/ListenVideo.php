<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;

class ListenVideo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $path = "";
    private $name = "";
    private $ext = "";
    private $hash = "";

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($path, $name, $ext, $hash)
    {
        Log::info('ListenVideo: __construct start');
        $this->path = $path;
        $this->name = $name;
        $this->ext = $ext;
        $this->hash = $hash;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info('ListenVideo: handle start');

        $i = 0;
        while(true)
        {
            $i++;
            if(file_exists($this->path . "/test.txt") == true)
            {
                $logs = file_get_contents($this->path . "/test.txt");
                Log::info('ListenVideo: listen:'. $logs);
            }
            
            sleep(1);
            if($i>=10)
            {
                break;
            }

        }
        
    }
}
