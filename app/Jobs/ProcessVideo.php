<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;

class ProcessVideo implements ShouldQueue
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
        Log::info('ProcessVideo: __construct start');
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
        Log::info('ProcessVideo: handle start');
        //shell_exec('calc');
        file_put_contents($this->path . "/test.txt", "");
        
        $hashNameWithoutExt = explode(".", $this->name)[0];

        //$dstDir = $this->path . '/' . $hashNameWithoutExt . '/';
        //Log::info('ProcessVideo: create path at:' . $dstDir);
        //@mkdir($dstDir, 0777, true);
        //Log::info('ProcessVideo: create path success');

        $ffmpeg = 'C:\Users\pickuse\Desktop\ffmpeg\bin\ffmpeg';
        $ffprobe = 'C:\Users\pickuse\Desktop\ffmpeg\bin\ffprobe';
        
        $ffprobeCmd = "\"$ffprobe\" -v error -select_streams v:0 -show_entries stream=width,height,duration,bit_rate -print_format json \"$this->path/orig.$this->ext\"";
        Log::info('ProcessVideo: $ffprobeCmd:' . $ffprobeCmd);
        
        $metaTxt = shell_exec($ffprobeCmd);
        $meta = json_decode($metaTxt);
        $meta = $meta->streams[0];

        file_put_contents($this->path . "/meta.json", json_encode($meta));
        

        $time = floatval($meta->duration);
        $interval = $time * .25;
        foreach (range(1, 3) as $p) {
            $t = $interval * $p;
            $cmd = "\"$ffmpeg\" -v error -y -ss $t -i \"$this->path/orig.$this->ext\" -vframes 1 -vf scale=640:-1 \"$this->path/screenshot_$p.jpg\"";
            shell_exec($cmd);
        }

        $resolutions = [360, 480, 720];
        $bRate = [.50, .60, .75];

        foreach (range(0, count($resolutions) - 1) as $i) {
            $p = $resolutions[$i];
            $r = $bRate[$i] * $meta->bit_rate;

            if ($meta->height <= $p) {
                continue;
            }

            $cmd = "\"$ffmpeg\" -y -i \"$this->path/orig.$this->ext\" -c:v libx264 " .
                "-vf scale=-1:720 -c:a aac -b:v ${r} -strict -2 -movflags faststart " .
                " \"$this->path/${p}p.mp4\" 2>\"$this->path/test.txt\" &";
            Log::info('ProcessVideo: $ffmpegCmd:' . $cmd);
            shell_exec($cmd);
            //$logs = file_get_contents($this->path . "/test.txt");
            //dd($logs);
            //Log::info('ProcessVideo: $ffmpegCmd:' . $logs);
        }
    }
}
