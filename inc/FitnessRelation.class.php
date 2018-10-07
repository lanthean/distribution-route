<?php
/**
 * @Copyright (c) 2011, Bc. Martin Bortel
 * @author Bc. Martin Bortel
 * @project EA (MM2T 2011) at BUT http://vutbr.cz
 */
class FitnessRelation extends DBObject{
    protected $DBTable = "FitnessRelation";
    
    public function __construct($id = null, $id2 = null, $verbose = false) {
        global $Tools;

        $this->verbose = $verbose;
        
        $rs2 = $Tools->Db->Query("SHOW COLUMNS FROM {$this->DBTable} WHERE `key` = 'MUL'");
        if ($rs2 && $rs2->recordcount() > 0) {
            while (!$rs2->eof()) {
                $this->FK[] = $rs2->fields['Field'];
                $rs2->MoveNext();
            }
        }
        if(is_int($id) && is_int($id2)){
            $this->BuildSQL($id, $id2);
            $rs = $Tools->Db->Query($this->SQL, $this->verbose);
            if ($rs && $rs->recordcount() > 0) {
//                echo " 1st ";
//                die("ids int and in db");
                $this->Data = $rs->fields;                  
                $this->DbExists = TRUE;
            }else{
                $this->BuildSQL($id, $id2, true);
                $rs2 = $Tools->Db->Query($this->SQL, $this->verbose);
                if ($rs2 && $rs2->recordcount() > 0) {
//                    echo " 2nd ";
//                    die("ids int, reverse in db");
                    $this->Data = $rs2->fields;
                    $this->DbExists = TRUE;
                }else{
//                    echo " false ";
                    $this->DbExists = FALSE;
                    $this->Data[$this->FK[0]] = $id;
                    $this->Data[$this->FK[1]] = $id2;
//                    print_r($this->Data);
//                die("ids int but not in db");
                }
            }
        }else{
//        die("ids no int");
            parent::__construct($id, $id2, $id3);
        }
    }
    public function BuildSQL($id, $id2, $rotate = false){
        $where = "";
        if($rotate){
            $where .= $this->FK[0] . " = " . $id;
            $where .= " AND " . $this->FK[1] . " = " . $id2;
        }else{
            $where .= $this->FK[0] . " = " . $id2;
            $where .= " AND " . $this->FK[1] . " = " . $id;
        }
        $this->SQL = "SELECT * FROM {$this->DBTable} WHERE $where";
    }

    /**
     * Pretizeni metody save, tento objekt pouziva pro vyhledani databazaveho zaznamu kombinaci cizich klicu, na misto primarniho
     * 
     * @global type $Tools
     * @global type $Context 
     */
    public function Save() {
            global $Tools, $Context;

            $this->TimeOfSave = date("Y-m-d H:i:s", time());
            $where = array();
            foreach ($this->FK as $value) {
                $where[] = "$value = '{$this->Data[$value]}'";
            }
            if(count($this->FK) > 0 ){
                $sql = "SELECT COUNT(*) as `Count` FROM {$this->DBTable} WHERE " . implode(" AND ", $where);
                $rs = $Tools->Db->Query($sql);
            }else{
                $rs = false;
            }
            if ($rs && $rs->fields["Count"] > 0) {
                $this->Data["Updated"] = $this->TimeOfSave;
                if($this->Logging) $Tools->Log->LogToFile("database",
                    "UPDATE {$this->PK[0]}={$this->Data[$this->PK[0]]}\tTime: {$this->Data['Updated']}", print_r($this->Data, true)
                ,"message");
                foreach ($this->Data as $key => $value) {
                    $key = "`{$key}`"; //osetreni pro buildSQL() - konfilkt user, default, apod.
                }
                $result = $Tools->Db->UpdateArray($this->DBTable, $this->Data, $this->verbose);
            } else {
                $this->Data["Created"] = $this->TimeOfSave;
                if($this->Logging) $Tools->Log->LogToFile("database",
                    "INSERT\tTime: {$this->Data['Created']}", print_r($this->Data, true)
                ,"message");
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
        
}
class FitnessRelations extends DBCollectionUI{
    protected $DBObjectClassName = "FitnessRelation";
    
    public $GridTitle = "Fitness relations";
    
    public $BrowsedColumns = array(
        "FitnessRelationID" => array(
            "header" => "ID"
            ,"width" => "50"
        )
        ,"GeneID" => array(
            "header" => "GeneID 1"
            ,"width" => "75"
        )
        ,"Gene2ID" => array(
            "header" => "GeneID 2"
            ,"width" => "75"
        )
        ,"Data" => array(
            "header" => "Distance in meters"
            ,"autowidth" => true
        )
    );
    
    public static function generateFirstFitnessRelations($GeneIDs){
        global $GoogleAPI;
        $GeneIDArr = explode(",", $GeneIDs);
        $Data = $GoogleAPI->Data;
        $j=0;
        for($i=0;$i<count($Data);$i++){
            $FR = new FitnessRelation(intval($GeneIDArr[$j]), intval($GeneIDArr[$j+1]));
//            echo $GoogleAPI->getTravelMethod();
            $FR->Data['Data'] = $Data[$i]["{$GoogleAPI->getTravelMethod()}"]['value'];
            $FR->Save();
        $j++;}
    }
    public static function generateRestOfFitnessRelations($GeneIDs){
        global $GoogleAPI;
        foreach($GeneIDs as $Gene){
            $FR = new FitnessRelation(intval($Gene['id']), intval($Gene['id2']));
            if(!$FR->DbExists){
                $GeneStart = new Gene(intval($Gene['id']), true);
                $origin = $GeneStart->Data['Latitude'].",".$GeneStart->Data['Longtitude'];

                $GeneEnd = new Gene(intval($Gene['id2']), true);
                $destination = $GeneEnd->Data['Latitude'].",".$GeneEnd->Data['Longtitude'];

                $GoogleAPI->Request = "";
                $GoogleAPI->setParameters(array(
                    "origin" => $origin
                    ,"destination" => $destination
                    ,"waypoints" => ""
                    ,"sensor" => "false"
                ));
                $GoogleAPI->Execute();
                $FR->Data['Data'] = $GoogleAPI->Data[0]["{$GoogleAPI->getTravelMethod()}"]['value'];
                $FR->Save();
            }
        }        
    }        
   
}
?>