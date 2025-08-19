<?php

namespace Bd;

use PDO;

define('HOST', 'localhost');
define('LOG', 'root');
define('PASSWORD_DB', 'root');
define('DATA_BASE', 'Loginphp');



 class Repository_base
{
    private $host = HOST;
    private $log = LOG;
    private $password = PASSWORD_DB;
    private $db = DATA_BASE;
    private $conexao;


    public function __construct()
    {
        $this->GetConnection();
    }
    public function GetConnection()
    {
        
        try {
            $conn_string = "mysql:host=" . $this->host . ";dbname=" . $this->db;
            $this->conexao = new PDO($conn_string, $this->log, $this->password);
        } catch (\Throwable $error) {
            echo "Connection Error: " . $error->getMessage();
            return null;
        }

        return $this->conexao;
    }
}
