<?php

/**
 * @Copyright (c) 2011, Bc. Martin Bortel
 * @author Bc. Martin Bortel <martin.bortel(at)gmail.com>
 * @project EA (MM2T 2011) at BUT http://vutbr.cz
 */

/**
 * DbCollectionUI
 * trida je potomek tridy DbCollection
 * navic poskytuje funkce pro zobrazeni uzivatelskeho rozhrani
 */
class DbCollectionUI extends DbCollection {

    /**
     *
     * @var array pole zobrazovanych sloupcu v datove mrizce
     * pouziti:
     *   public $BrowsedColumns = array(
     *   "[nazev_sloupce]" => array(
     *      "header" => "[Nazev sloupce v hlavicce]",
     *      "format" => "[Nazev funkce tridy formatString]",
     *      "template" => "[Html template pro zobrazeni (hodnota je v promenne value)]"
     *   );
     */
    public $BrowsedColumns = array();
    public $GridEntry;
    /**
     *
     * @var int pocet zaznamu na strance gridu
     */
    public $GridPageSize = 20;
    protected $GridDataSourceUrl = "";
    public $GridAutoExpandColumn = "";
    public $GridTitle = "";
    public $GridSubTitle = "";
    public $GridWidth = "";
    public $GridHeight = "";
    public $GridStoreName = "";
    public $ActionMenu = TRUE;
    public $InsertButtonMenu = FALSE;
    public $ListingIds = array();
    public $Permissions;

    //pro pripadne rozsirovni UI (ExtJS apod)
    function JsonOutput() {
        if ($_REQUEST['start']) {
            $this->Start = $_REQUEST['start'];
        } else {
            $start = 0;
        }
        if ($_REQUEST['limit']) {
            $this->Limit = $_REQUEST['limit'];
        }
        if ($_REQUEST['sort'] && !$this->Orderby) {
            $this->OrderBy = $_REQUEST['sort'] . " " . $_REQUEST['dir'];
        }
        $this->Bind();
        $Data = $this->ToList();

        if (isset($_GET["verbose"])) {
            print_r($Data);
            die();
        }

        $obj = new stdClass;
        $obj->totalCount = $this->FoundRows;
        $obj->topics = $Data;
        header('Content-Type: text/html; charset=utf-8');
        echo json_encode($obj);
    }

    /**
     * DbCollectionUI::RenderGrid()
     * 
     * @return JSON string
     */
    function RenderGrid($sqlBind = false) {
        global $config, $Tools;
        $display = "";
        require_once($config["basepath"] . "resources/Grid.php");
        $TableGrid = new TblGrid();
        $this->Bind($sqlBind);
        $display .= $TableGrid->Render($this);
        ###logging the event
        //($this->GridTitle!="")?$title = $this->GridTitle:$title = $this->DBTable;
        $Tools->Log->LogToFile("program_run", "Rendering {$title} grid","Items: ".json_encode($this->Items), "message");
        ###eo logging
        return $display;
    }

}

class DbCollection {

    /**
     * 
     * @var string nazev tabulky v databazi, se kterou objekt pracuje..
     */
    protected $DBTable;
    /**
     * 
     * @var array pole objektu DbObject
     */
    public $Items = array();

    /**
     * decides if query should vomit used sql
     * @var boolean 
     */
    public $verbose = false;
    /**
     *
     * @var boolean urcuje zda-li se polozky kolekce budou zarazovat do kategorii
     * pro zarazeni polozek do kategorii slouzi db tabulka "<nazev_kolekce>_rel"                  
     */
    public $Relation = FALSE;
    public $RelationTable = "";
    public $RelationID;
    /**
     *
     * @var string nazev tridy ze ktere se zjistuje nazev databazove tabulky
     */
    protected $DBObjectClassName;
    
    /**
     * 
     * @var string nazev relacni kolekce 
     */
    public $RelDbCollectionName;
    /**
     * 
     * @var array pole primarnich klicu db tabulky
     */
    private $PK = array();
    /**
     * 
     * @var array pole cizich klicu db tabulky
     */
    private $FK = array();
    /**
     * 
     * @var int pocet nalezenych radku v tabulce vyhovujicich filtru
     */
    public $FoundRows;
    /**
     * 
     * @var string vygenerovany dotaz SQL
     */
    private $SQL;
    
    /**
     * 
     * @var array pole klicu pro filtrovani zaznamu db tabulek (WHERE)
     */
    private $FilterKeys = array();
    /**
     * 
     * @var array pole objektu Join obsahujicich nastaveni pro join db tabulek
     * viz class Join         
     */
    private $Joins = array();
    /**
     * @var
     * pomocne promenne pro tvorbu SQL
     */
    public $Select = "*";
    public $Where = NULL;
    public $GroupBy = NULL;
    public $OrderBy = NULL;
    public $OrderDownBy = NULL;
    public $Having = NULL;
    public $Start = NULL;
    public $Limit = NULL;
    /**
     * TRUE = Items are loaded
     * @var boolean 
     */
    public $Bound = false;

    protected $N_1Property = "";
    protected $N_1DBObjectClassName = "";


    /**
     *
     * @contruct DbCollection
     */
    function __construct() {
        global $Tools;

        $this->GetDBTable();
        // zjisteni primarniho klice tabulky $DBTable
        $rs = $Tools->Db->Query("SHOW COLUMNS FROM {$this->DBTable} WHERE `key` = 'PRI'");
        if ($rs && $rs->recordcount() > 0) {
            while (!$rs->eof()) {
                $this->PK[] = $rs->fields['Field'];
                $rs->MoveNext();
            }
        }
        $rs2 = $Tools->Db->Query("SHOW COLUMNS FROM {$this->DBTable} WHERE `key` = 'MUL'");
        if ($rs2 && $rs2->recordcount() > 0) {
            while (!$rs2->eof()) {
                $this->FK[] = $rs2->fields['Field'];
                $rs2->MoveNext();
            }
        }
        if ($this->Relation) {
            // zkontroluje se jestli existuje relacni tabulka
            if (!$this->RelationTable) {
                $RelDbCollection = new $this->RelDbCollectionName();
                $RelDbObjectName = $RelDbCollection->GetDBObjectClassName();
                $RelDbObject = new $RelDbObjectName();
                $RelationTable = $RelDbObject->GetDBTable();
            }
            else
                $RelationTable = $this->RelationTable;

            $resultSet = $Tools->Db->Query("SHOW TABLES LIKE '{$RelationTable}'");
            if ($resultSet && $resultSet->recordcount() > 0) {
                $rs = $Tools->Db->Query("SHOW COLUMNS FROM {$RelationTable} WHERE `key` = 'PRI'");
                while (!$rs->eof()) {
                    if (in_array($rs->fields['Field'], $this->PK))
                        $rs->MoveNext();
                    else {
                        $criticalError = true;
                        break;
                    }
                }
            } else {
                $Tools->Log->error("Kritická chyba: neexistuje relační tabulka");
                return false;
            }
        }
    }

    function GetDBTable() {
        if (empty($this->DBTable)) {
            eval("\$DBObject = new {$this->DBObjectClassName}();");
            $this->DBTable = $DBObject->GetDBTable();
        }
        return $this->DBTable;
    }

    function GetSQL() {
        return $this->SQL;
    }

    function Filter($key, $value, $operator = "=") {
        $this->FilterKeys[$key] = $operator . " '$value'";
    }

    function OrderBy($key, $sort = "ASC") {
        $this->OrderBy = $key . " " . $sort;
    }

    function BuildSQL() {
        $this->SQL = "SELECT SQL_CALC_FOUND_ROWS {$this->Select} FROM {$this->DBTable}";

        if ($this->Relation) {
            $RelDbCollection = new $this->RelDbCollectionName();
            $RelDbObjectName = $RelDbCollection->GetDBObjectClassName();
            $RelDbObject = new $RelDbObjectName();
            $this->RelationTable = $RelDbObject->GetDBTable();
            $this->Join($this->RelationTable, $RelDbObject->PK[0], $this->RelationID);
        }
        foreach ($this->Joins as $Join) {
            if ($Join->JoinTableName) {// && $Join->JoinConditionField
                if (!empty($Join->JoinConditionField)) {
                    $this->SQL .= " JOIN {$Join->JoinTableName} ON {$Join->JoinTableName}.{$Join->JoinConditionField} = {$this->DBTable}.{$Join->JoinConditionField}";
                } else {
                    $this->SQL .= " JOIN {$Join->JoinTableName} ON {$Join->JoinTableName}.{$this->PK[0]} = {$this->DBTable}.{$this->PK[0]}";
                }
                if ($Join->JoinConditionValue)
                    $this->Filter("{$Join->JoinTableName}.{$Join->JoinConditionField}", $Join->JoinConditionValue);
            }
        }
        $first = true;
        foreach ($this->FilterKeys as $key => $value) {
            if ($first && empty($this->Where)) {
                $where = " $key $value";
            } else {
                $where .= " AND $key $value";
            }
            $first = false;
        }

        $where = $this->Where . $where;
        if ($where) {
            $this->SQL .= " WHERE " . $where;
        }
        if ($this->GroupBy) {
            $this->SQL .= " GROUP BY {$this->GroupBy}";
        }
        if ($this->Having) {
            $this->SQL .= " HAVING {$this->Having}";
        }
        if ($this->OrderBy) {
            $this->SQL .= " ORDER BY {$this->OrderBy}";
        }
        if ($this->Limit && $this->Start) {
            $this->SQL .= " LIMIT {$this->Start}, {$this->Limit}";
        } elseif ($this->Limit) {
            $this->SQL .= " LIMIT {$this->Limit}";
        }
        
        LogToFile::saveLog("DBCollections", "DbCollection: ".$this->SQL);
        
        return $this->SQL;
    }

    function Join($JoinTableName, $JoinConditionField = null, $JoinConditionValue = null, $PrimaryKey = null) {
        $Join = new Join();
        $Join->JoinTableName = $JoinTableName;
        $Join->JoinConditionField = $JoinConditionField;
        if ($JoinConditionValue != null)
            $Join->JoinConditionValue = $JoinConditionValue;
        if ($PrimaryKey != null)
            $Join->PrimaryKey = $PrimaryKey;

        $this->Joins[] = $Join;
    }

    /**
     *
     * @global tools object $Tools
     * @param boolean $sqlrun = false get data normally from db, true: get data from data object link (implode/explode IDs)
     * @return void 
     */
    function Bind() {
        global $Tools;
        if($this->Bound) return;

        if (!$this->SQL) {
            $this->BuildSQL();
        }
        $rs = $Tools->Db->Query($this->SQL, $this->verbose);
        $row_num = $Tools->Db->Query("SELECT FOUND_ROWS() as FoundRows");
        while ($rs && !$rs->eof()) {
            eval("\$this->Items[] = new {$this->DBObjectClassName}(\$rs->fields);");
            $rs->movenext();
        }
        $this->FoundRows = $row_num->fields['FoundRows'];
        $this->Bound = true;
    }


    function GetDBObjectClassName() {
        return $this->DBObjectClassName;
    }
    function getPK($index = null){
        if($index != null){
            return $this->PK[$index];
        }else
            return $this->PK;
    }
    function getFK($index = null){
        if($index != null){
            return $this->FK[$index];
        }else
            return $this->FK;
    }

}

class Join {

    /**
     * @var string nazev db tabulky, ze ktere se vybiraji data pro SQL join - subkolekce
     */
    public $JoinTableName = NULL;
    /**
     * @var array pole filtrovacich klicu pro joinovanou tabulku (WHERE $key $value)
     */
    public $JoinConditionField = NULL;
    /**
     * @var array pole hodnot filtrovacich klicu pro joinovanou tabulku (WHERE $key $value)
     */
    public $JoinConditionValue = NULL;
    /**
     * @var array pole hodnot primarnich klicu joinovanych tabulek
     */
    public $PrimaryKey = NULL;
}

?>