<?php namespace RevoSystems\DBSync;

use Spatie\DbDumper\Databases\MySql;
use Spatie\DbDumper\Exceptions\CannotSetParameter;
use Spatie\DbDumper\Exceptions\CannotStartDump;
use Spatie\DbDumper\Exceptions\DumpFailed;

class DBDumper {

    protected $db_name;
    protected $tables;
    protected $from;
    
    public function __construct($revoUser, $tables)
    {
        $this->db_name  = $revoUser->databaseName();
        $this->tables   = $revoUser->tablesToSync();
        $this->from     = $revoUser->lastUploadSync();
    }
    
    public function create($withoutCreateTables = true) {
        $mysqlCall = $this->generateMysqlCall();
        if ($withoutCreateTables) {
            return $mysqlCall->addExtraOption("-t");
        }
        return $mysqlCall;
    }
    
    private function generateMysqlCall() {
        $userName       = config('database.connections.mysql.username');
        $password       = config('database.connections.mysql.password');
        try {
            return MySql::create()
                ->dontUseExtendedInserts()
                ->skipComments()
                ->setDbName($this->db_name)
                ->setUserName($userName)
                ->setPassword($password)
                ->includeTables($this->tables)
                ->addExtraOption("--replace");
        } catch (CannotSetParameter $e) {
        }
    }
    
    public function dump($filename) {
        try {
            $this->create()
                ->addExtraOption("--where=\"updated_at > '$this->from'\"")
                ->dumpToFile(public_path($filename));
        } catch (CannotStartDump $e) {
        } catch (DumpFailed $e) {
        }
    }
}