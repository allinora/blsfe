<?php
class DBQuery extends  Mysqli {
    var $db;
    private $is_connected = false;

    private static $connections = null;

    public static function get_connection($name)
    {
        if (null === self::$connections) {
            self::$connections = array();
        }

        if (!array_key_exists($name, self::$connections)) {
		$cfg=get_config("db", $name);

            self::$connections[$name] = new DBQuery(
                $cfg["host"],
                $cfg["username"],
                $cfg["password"],
                $cfg["dbname"]
            );
        }

        return self::$connections[$name];
    }

    public function __construct($host, $user, $pass, $db){
        $this->init();  // Initialize but do not connect.
        $this->db["host"]=$host;
        $this->db["user"]=$user;
        $this->db["pass"]=$pass;
        $this->db["db"]=$db;
        //return $this->getConnection();
        // Connect will happen automatically when we run a query.
        // if you want forced connect, call getConnectiion()
    }

    private function getConnection(){
        $this->real_connect($this->db["host"], $this->db["user"], $this->db["pass"], $this->db["db"]);

        if ($this->connect_error) {
            return false;
        }

        $this->set_charset('utf8');
        $this->is_connected = true;

        return true;
    }

    private function isConnected(){
        if (!$this->is_connected) {
            $this->getConnection();
        }
        if ($this->ping()) {
            return true;
        } else {
            return $this->getConnection();
        }

    }

    public function Execute($sql){
        return $this->ExecuteQuery($sql);
    }
    public function ExecuteQuery($sql){
        if (!$this->isConnected()) {
            return false;
        }
        if ($this->query($sql)){
            return true;
        } else {
            throw new Exception ($this->error);
        }
        return false;
    }

    public function GetAllAssoc($sql){
        if (!$this->isConnected()) {
            return false;
        }
        $result=$this->query($sql);
        if ($result === false) {
            return false;
        }
        if (function_exists("mysqli_fetch_all")) {
            return $result->fetch_All(MYSQLI_ASSOC);
        } else {
            $data=array();
            while ($row = $result->fetch_assoc()) {
                $data[]=$row;
            }
            return $data;
        }
    }
    public function GetAllArray($sql){
        if (!$this->isConnected()) {
            return false;
        }
        $result=$this->query($sql);
        if ($result === false) {
            return false;
        }
        if (function_exists("mysqli_fetch_all")) {
            return $result->fetch_All(MYSQLI_NUM);
        } else {
            $data=array();
            while ($row = $result->fetch_array()) {
                $data[]=$row;
            }
            return $data;
        }
    }

    public function GetAll($sql){
        return $this->GetAllAssoc($sql);
    }

    public function GetCol($sql, $field=null){
        // Return the column from the results as an array
        if (!$this->isConnected()) {
            return false;
        }
        if ($field) {
            $all=$this->GetAllAssoc($sql);
            if (is_array($all)){
                $cols=array();
                foreach ($all as $row){
                    $cols[]=$row[$field];
                }
                return $cols;
            }
        } else {
            $all=$this->GetAllArray($sql);
            if (is_array($all)){
                $cols=array();
                foreach ($all as $row){
                    $cols[]=$row[0];
                }
                return $cols;
            }
            return false;
        }
    }
    public function GetOne($sql, $field=null){
        if (!$this->isConnected()) {
            return false;
        }
        if ($field){
            $row=$this->GetRow($sql);
            if ($row){
                return $row[$field];
            }

        } else {
            if ($result=$this->query($sql)){
                $row=$result->fetch_row();
                return $row[0];
            }

        }
    }

    public function GetRow($sql){
        if (!$this->isConnected()) {
            return false;
        }
        if ($result=$this->query($sql)){
            return $result->fetch_assoc();
        }

        return false;
    }

    public function quote($value) {

        if (!$this->isConnected()) {
            throw new Exception("No access to database connection");
        }
        return "'" . $this->escape_string($value) . "'";
    }

    public function getInsertId() {
        if (!$this->isConnected()) {
            throw new Exception("No access to database connection");
        }
        return $this->insert_id;
    }
    public function ErrorNo(){
        if (!$this->isConnected()) {
            throw new Exception("No access to database connection");
        }
        return $this->errno;
    }
    public function ErrorMsg(){
        if (!$this->isConnected()) {
            throw new Exception("No access to database connection");
        }
        return $this->error;
    }

    public function GenID($table, $start=0){
        $_genIDSQL = sprintf("update %s set id=LAST_INSERT_ID(id+1);", $table);
        if ($this->Execute($_genIDSQL)) {
            return $this->insert_id;
        }
    }
}
