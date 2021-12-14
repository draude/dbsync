<?php namespace RevoSystems\DBSync;

class DBLoader {
    
    public static function loadFile($dbUsername, $dbPassword, $databaseFile,$revo_db,$dbHost = "") {
        if ($dbHost !== "") {
            $dbHost = "--host=$dbHost";
        }

        exec("mysql $dbHost --user=$dbUsername --password=$dbPassword $revo_db < $databaseFile 2>&1",$retArr,$output);

        if (intval($output) > 0) {
            throw new \Exception("SQL import Failed");
        }

        return "OK";
    }
}
