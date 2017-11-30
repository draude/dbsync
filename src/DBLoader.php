<?php namespace RevoSystems\DBSync;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use File;
use Symfony\Component\Process\Process;

class DBLoader {
    
    public static function loadFile($databaseFile,$revo_db) {
        $command = "mysql --user=".getenv('DB_USERNAME')." --password=".getenv('DB_PASSWORD'). " $revo_db < $databaseFile";
        $process = new Process($command);
        return $process->run();
    }
 
    public static function readFile($filename) {
        try
        {
            $contents = File::get($filename);
            return $contents;
        }
        catch (FileNotFoundException $exception)
        {
            return null;
        }
    }
    
}