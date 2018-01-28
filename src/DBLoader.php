<?php namespace RevoSystems\DBSync;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use File;
use Symfony\Component\Process\Process;

class DBLoader {
    
    public static function loadFile($dbUsername, $dbPassword, $databaseFile,$revo_db) {
        $command = "mysql --user=".$dbUsername." --password=".$dbPassword. " $revo_db < $databaseFile";
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