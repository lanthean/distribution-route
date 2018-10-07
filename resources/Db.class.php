<?php
/**
 * @project EA (MM2T 2011) at BUT http://vutbr.cz
 * 
 * @author Wue
 */
class Db {

    /**
     *
     * @var AdoDBConnection Pripojeni k databazi 
     */
    public $conn;

	/**
     *
     * @var Autoinkrement vlozeneho zaznamu
     */
    public $Insert_ID;

    public function Db() {
        global $PATH, $ADODB_FETCH_MODE, $config;
        $ADODB_FETCH_MODE = MYSQL_NUM;

        require_once($PATH."resources/adodb/adodb.inc.php");
        $this->conn = &ADONewConnection("mysql");
        $this->conn->Connect($config['dbserver'], $config['dbuser'], $config['dbpasswd'], $config['dbname']);
        echo mysql_error();

        $this->Query("SET CHARACTER SET utf8");
        $this->Query("SET NAMES utf8");
    }

    public function Query($sql, $verbose = false) {
        global $Tools, $Context;
        if ($verbose) {
            //echo $sql; #old version
            $Context->DebugOutput[] = $sql; #from v.1.0.2 use Context
        }
        $rs = $this->conn->execute($sql);
        if (!$rs) {
            LogToFile::saveLog("dbconn","error",mysql_error()." SQL: $sql");
            //echo "<br />MySQL: ".mysql_error().", SQL: {$sql}";
            $return = false;
        } else {
            $return = $rs;
        }
        return $return;
    }

    public function Find(&$RS, $FieldName, $Value) {
        $RS->movefirst();
        while (!$RS->eof() && $RS->fields[$FieldName] != $Value) {
            $RS->movenext();
        }
        return !$RS->eof();
    }

	public function InsertArray($table, $array,  $verbose = false) {
	  global $Tools, $Context;
	   
		$rs = Db::query("SHOW COLUMNS FROM ".$table);
		if (!$rs) {
			return false;
		}
		$sqlKeys = array();
		$sqlValues = array();
		foreach ($array as $k => $v) {
			if (Db::Find($rs,"Field", $k) && $k!="0") {
				$sqlKeys[] = "`{$k}`";
				$sqlValues[] = "'" . mysql_escape_string($v) . "'";
			}
		}
		$sql = "INSERT INTO $table (".implode(", ", $sqlKeys).") VALUES (".implode(", ", $sqlValues).")";
		$result = Db::Query($sql, $verbose);
		$this->Insert_ID = $this->conn->Insert_ID();
		
	}

	public function UpdateArray($table, $array, $verbose = false) {
		$rs = Db::query("SHOW COLUMNS FROM ".$table);
		if (!$rs) {
			return false;
		}
		$sqlKeys = array();
		$sqlValues = array();
		foreach ($array as $k => $v) {
			if (Db::Find($rs,"Field", $k) && $k!="0") {
				$sqlKeys[] = "`{$k}`";
				$sqlValues[] = "$v";
			}
		}
        $rs  = Db::Query("SHOW COLUMNS FROM $table WHERE `key` = 'PRI'");
        if ($rs && $rs->recordcount() > 0) {
            $where = " WHERE ";
            $first = true;
            while (!$rs->eof()) {
                if (!$first) {
                    $where .= " AND ";
                }
                $where .= "`{$rs->fields['Field']}`  = '{$array[$rs->fields['Field']]}'";
                $first = false;
                $rs->MoveNext();
            }
            $sql = "";
            for ($i=0; $i < count($sqlKeys); $i++) {
                $sql.=$sqlKeys[$i]." = '".mysql_escape_string($sqlValues[$i])."',";
            }
            $sql = substr($sql, 0, strlen($sql)-1);
            $sql = "UPDATE $table SET ".$sql." ".$where;
            return Db::Query($sql, $verbose);
        } else {
            return false;
        }
	}
}
?>
