<?php namespace RevoSystems\DBSync;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class DBLoader {
    
    public static function loadFile($dbUsername, $dbPassword, $databaseFile,$revo_db,$dbHost = "") {
        if ($dbHost !== "")
            $dbHost = "--host=$dbHost";
        $command = "mysql $dbHost --user=".$dbUsername." --password=".$dbPassword. " $revo_db < $databaseFile";
        $process = new Process($command);
        $process->run();
    
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
        return $process->getOutput();
    }
}
