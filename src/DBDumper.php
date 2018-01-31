<?php namespace RevoSystems\DBSync;

use Spatie\DbDumper\Databases\MySql;
use Spatie\DbDumper\Exceptions\CannotSetParameter;
use Spatie\DbDumper\Exceptions\CannotStartDump;
use Spatie\DbDumper\Exceptions\DumpFailed;

class DBDumper {

    protected $dbName;
    protected $tables;
    protected $from;
    protected $dbUsername;
    protected $dbPassword;
    protected $dbHost;
    protected $tablesPrefix;
    
    public function __construct($dbHost, $dbUsername, $dbPassword, $db_name, $tables, $tablesPrefix, $from) {
        $this->dbName      = $db_name;
        $this->tables       = $tables;
        $this->from         = $from;
        $this->dbUsername   = $dbUsername;
        $this->dbPassword   = $dbPassword;
        $this->dbHost       = $dbHost;
        $this->tablesPrefix = $tablesPrefix;
    }
    
    private function create($createTables) {
        $mysqlCall = $this->generateMysqlCall();
        $mysqlCall = $this->filterTables($mysqlCall);
        //if ($createTables) {
            return $mysqlCall;
        //}
        return $mysqlCall->addExtraOption("-t");
    }
    
    private function generateMysqlCall() {
        try {
            return MySql::create()
                ->setHost($this->dbHost)
                ->dontUseExtendedInserts()
                ->skipComments()
                ->setDbName($this->dbName)
                ->setUserName($this->dbUsername)
                ->setPassword($this->dbPassword)
                ->addExtraOption("--replace")
                ->addExtraOption("--extended-insert")
                ->addExtraOption("--complete-insert");
        } catch (CannotSetParameter $e) {
            return $e->getMessage();
        }
    }
    
    public function tablesWithPrefix($tables) {
        return collect($tables)->map(function ($tableName) {
            return $this->tablesPrefix.$tableName;
        })->toArray();
    }
    
    public function dump($filePath,$createTables = false) {
        try {
            $mySqlCall = $this->create($createTables);
                if ($this->from !== "")
                    $mySqlCall->addExtraOption("--where=\"updated_at > '$this->from'\"");
                $mySqlCall->dumpToFile($filePath);
        } catch (CannotStartDump $e) {
            return $e->getMessage();
        } catch (DumpFailed $e) {
            return $e->getMessage();
        }
    }
    
    /**
     * @param $mysqlCall
     */
    private function filterTables($mysqlCall)
    {
        if (count($this->tables["include"]) > 0)
            return $mysqlCall->includeTables($this->tablesWithPrefix($this->tables["include"]));
        
        return $mysqlCall->excludeTables($this->tablesWithPrefix($this->tables["exclude"]));
    }
}