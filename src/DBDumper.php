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
        if ($createTables) {
            return $mysqlCall;
        }
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
                ->includeTables($this->tablesWithPrefix())
                ->addExtraOption("--replace");
        } catch (CannotSetParameter $e) {
            return $e->getMessage();
        }
    }
    
    public function tablesWithPrefix() {
        return collect($this->tables)->map(function ($tableName) {
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
}