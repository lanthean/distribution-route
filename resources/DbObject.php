<?php

/**
 * @Copyright (c) 2011, Bc. Martin Bortel
 * @author Bc. Martin Bortel <martin.bortel(at)gmail.com>
 * @project EA (2011) at BUT http://vutbr.cz
 */
class DBObject {

    /**
     *
     *  @var array $Data je pole s indexem nazvu sloupce a hodnoutou dat
     */
    public $Data = array();
    public $ASSOC = true;
    public $verbose = false;
    /**
     *
     *  @var string $DBTable obsahuje nazev tabulky, se kterou je DbObject svazan
     */
    protected $DBTable;

    /**
     *
     *  @var boolean $DbExists je TRUE pokud je zaznam ulozen v DB
     */
    public $DbExists = FALSE;
    /**
     *
     * @var array $PK pole nazvu primarnich klicu
     */
    public $PK = array();
    public $FK = array();
    /**
     *
     * @var string $TimeOfSave údaj o čase uložení DbObjectu
     */
    public $TimeOfSave;
    public $InsertedID;
    public $SQL;

    private $Logging = true;
    /**
     *
     * @method DbObject
     * konstruktor v zavislosti na vstupnich paramemtrech vytvori instanci objektu
     * @param (int || array || string) $id
     *   $id == NULL - vytvori novy prazdny objekt, ktery neni ulozen v DB
     *   $id == int - vyhleda zaznam v DB pomoci primarniho klice (a hodnoty $id)
     *   $id == string - vyhleda zaznam v DB dle hodnoty $id ($id => WHERE)
     * 	 $id == array - naplni pole $this->Data polem $id
     */
    function __construct($id = null, $id2 = null, $id3 = null) {
        global $Tools;
        $where = " WHERE ";
        
        $rs = $Tools->Db->Query("SHOW COLUMNS FROM {$this->DBTable} WHERE `key` = 'PRI'");
        if ($rs && $rs->recordcount() > 0) {
            while (!$rs->eof()) {
                $this->PK[] = $rs->fields['Field'];
                $rs->MoveNext();
            }
        }
        if (is_int($id)) {
            $where = $this->PK[0] . " = " . $id;
            if ($id2 != null && is_int($id2)) {
                $where .= " AND " . $this->PK[1] . " = " . $id2;
            }
            if ($id3 != null && is_int($id3)) {
                $where .= " AND " . $this->PK[2] . " = " . $id3;
            }
            $this->SQL = "SELECT * FROM {$this->DBTable} WHERE $where";
            $rs = $Tools->Db->Query($this->SQL, $this->verbose);
            if ($rs && $rs->recordcount() > 0) {
                $this->Data = $rs->fields;
                $this->DbExists = TRUE;
            }else{
            	$this->DbExists = FALSE;
            }
        } elseif (is_array($id)) {
            $this->Data = $id;
            $this->DbExists = TRUE;
        } elseif (is_string($id)) {
            $this->SQL = "SELECT * FROM {$this->DBTable} WHERE $id";
            $rs = $Tools->Db->Query($this->SQL, $this->verbose);

            if ($rs && $rs->recordcount() > 0) {
                $this->Data = $rs->fields;
                $this->DbExists = TRUE;
            } else {
                $this->DbExists = FALSE;
            }
        } else {
            $this->DbExists = FALSE;
        }

        if ($this->ASSOC && count($this->Data) > 0) {
            $this->MakeDataAnAssocArr();
        }
        //LogToFile::saveLog("DbObject: ".$this->SQL);
    }

    function Refresh() {
        global $Tools;

        $IsPKSet = true;
        foreach ($this->PK as $value) {
            if (empty($this->Data[$value])) {
                $IsPKSet = false;
                break;
            }
        }
        if ($IsPKSet) {
            foreach ($this->PK as $value) {
                $where[] = "$value = '{$this->Data[$value]}'";
            }
            $sql = "SELECT * FROM {$this->DBTable} WHERE " . implode(" AND ", $where);
            $rs = $Tools->Db->Query($sql);
            if ($rs && $rs->recordcount() > 0) {
                $this->Data = $rs->fields;
                $this->DbExists = TRUE;
            }
        }
    }

    /**
     *
     * @method void
     *      pokud existuje zaznam v DB pod nazvem primarniho klice upravi stavajici zaznam pomoci $Tools->Db metody UpdateArray()
     *      pokud zaznam neexistuje, vytvori novy zaznam pomoci $Tools->Db metody InsertArray()
     */
    function Save() {
        global $Tools, $Context;
           
        $this->TimeOfSave = date("Y-m-d H:i:s", time());

        $where = array();
//        echo "<br />PRI (".get_class($this).":".$this->GetDBTable()."):";
//        print_r($this->PK);
//        echo "<br />";
        foreach ($this->PK as $value) {
            $where[] = "$value = '{$this->Data[$value]}'";
        }
        if(count($this->PK) > 0 ){
            $sql = "SELECT COUNT(*) as `Count` FROM {$this->DBTable} WHERE " . implode(" AND ", $where);
            $rs = $Tools->Db->Query($sql);
        }else{
            $rs = false;
        }
        if ($rs && $rs->fields["Count"] > 0) {
            $this->Data["Updated"] = $this->TimeOfSave;
//            $this->Data["UpdatorUserId"] = $Context->User->Data["UserId"];
            if($this->Logging) $Tools->Log->LogToFile("database",
                "UPDATE {$this->PK[0]}={$this->Data[$this->PK[0]]}\tTime: {$this->Data['Updated']}", print_r($this->Data, true)
            ,"message");//"UpdatorUserID: {$Context->User->Data['UserId']}/{$this->Data['UpdatorUserId']}\tTime: {$this->Data['Updated']}", print_r($this->Data, true)
            foreach ($this->Data as $key => $value) {
                $key = "`{$key}`"; //osetreni pro buildSQL() - konfilkt user, default, apod.
            }
            $result = $Tools->Db->UpdateArray($this->DBTable, $this->Data, $this->verbose);
        } else {
            $this->Data["Created"] = $this->TimeOfSave;
//            $this->Data["CreatorUserId"] = intval($Context->User->Data["UserId"]);
            if($this->Logging) $Tools->Log->LogToFile("database",
                "INSERT\tTime: {$this->Data['Created']}", print_r($this->Data, true)
            ,"message");//"CreatorUserID: {$Context->User->Data['UserId']}/{$this->Data['CreatorUserId']}\tTime: {$this->Data['Created']}"
            foreach ($this->Data as $key => $value) {
                $key = "`{$key}`"; //osetreni pro buildSQL() - konfilkt user, default, apod.
            }
            $result = $Tools->Db->InsertArray($this->DBTable, $this->Data, $this->verbose);
            if(count($this->PK) > 0) if (empty($this->Data[$this->PK[0]])) {
                $this->Data[$this->PK[0]] = $Tools->Db->Insert_ID;
            }
            $this->InsertedID = $Tools->Db->Insert_ID;
        }
    }

    /**
     *
     * @method void
     *      podle zadaneho parametru $where smaze zaznam v tabulce $this->DBTable
     */
    function remove($where) {
        global $Tools;
        $sql = "DELETE FROM {$this->DBTable} WHERE " . $where;
        $rs = $Tools->Db->Query($sql);
        if ($rs)
            $this->DbExists = TRUE;
    }

    function GetDBTable() {
        return $this->DBTable;
    }

        function MakeDataAnAssocArr() {
        $DataTMP = $this->Data;
        $this->Data = array();
        foreach ($DataTMP as $k => $v) {
            if (!is_numeric($k)) {
                $this->Data[$k] = $v;
            }
        }
    }
    
    function setLogging($boo){
        $this->Logging = $boo;
    }
    function getLogging(){
        echo get_class($this);
        return intval($this->Logging);
    }
}

class DbObjectUI extends DbObject {
    protected $DBTable;
}

?>