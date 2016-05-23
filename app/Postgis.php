<?php

namespace App;

/**
 * Description of Postgis
 *
 * @author mafonso
 */
class Postgis {
    
    /**
     * The postgis host
     * 
     * @var string
     */
    protected $host;
    
    /**
     * The postgis port
     * 
     * @var integer
     */
    protected $port;
    
    /**
     * The postgis database
     * 
     * @var string
     */
    protected $dbname;
    
    /**
     * The connection handler
     * 
     * @var \PDO
     */
    protected $pdo;
    
    /**
     * Create a new Postgis instance
     * 
     * @param string $host
     * @param string $dbname
     * @param string $port
     */
    public function __construct($host, $dbname, $port = 5432)
    {
        $this->host = $host;
        $this->dbname = $dbname;
        $this->port = $port;
    }
    
    /**
     * Create a postgis connection
     * 
     * @param string $user
     * @param string $pass
     */
    public function connect($user, $pass)
    {
        $dsn = "pgsql:host={$this->host};port={$this->port};dbname={$this->dbname}";
        $this->pdo = new \PDO($dsn, $user, $pass);
    }

    /**
     * Get postgis schemas
     * 
     * @return array
     * @throws \Exception
     */
    public function getSchemaNames()
    {
        if (!$this->pdo) throw new \Exception ('Postgis error: not connected');
        $stm = $this->pdo->query('select schema_name as name from information_schema.schemata');
        return $stm->fetchAll(\PDO::FETCH_OBJ);
    }
    
    /**
     * Get schema tables
     * 
     * @param string $schemaname
     * @return array
     * @throws \Exception
     */
    public function getTableNames($schemaname)
    {
        if (!$this->pdo) throw new \Exception ('Postgis error: not connected');
        $stm = $this->pdo->prepare('SELECT table_name as name FROM information_schema.tables WHERE table_schema = ?');
        $stm->execute([$schemaname]);
        return $stm->fetchAll(\PDO::FETCH_OBJ);
    }
    
    /**
     * Get table columns
     * 
     * @param string $schemaname
     * @param string $tablename
     * @return array
     * @throws \Exception
     */
    public function getColumnNames($schemaname, $tablename)
    {
        if (!$this->pdo) throw new \Exception ('Postgis error: not connected');
        $stm = $this->pdo->prepare('SELECT column_name as name FROM information_schema.columns WHERE table_schema = ? AND table_name = ?');
        $stm->execute([$schemaname, $tablename]);
        return $stm->fetchAll(\PDO::FETCH_OBJ);
    }
}
