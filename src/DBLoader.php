<?php namespace RevoSystems\DBSync;

use File;
use Symfony\Component\Process\Process;

class DBLoader {
    
    public static function loadFile($dbUsername, $dbPassword, $databaseFile,$revo_db) {
        $command = "mysql --user=".$dbUsername." --password=".$dbPassword. " $revo_db < $databaseFile";
        $process = new Process($command);
        $process->run();
    
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
        return $process->getOutput();
    }
}