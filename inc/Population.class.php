<?php

/**
 * @Copyright (c) 2011, Bc. Martin Bortel
 * @author Bc. Martin Bortel <martin.bortel(at)gmail.com>
 * @package ea
 * @project EA (MM2T 2011) at BUT http://vutbr.cz
 */

class Population extends DBCollectionUI{
    public $N = 20; //number of individuals
    public $Counter = 0;
    public $Fitness = 0; // celkova sila populace
    public $MaxDecayOfFitness = 100;
    public $PopulationSize = 10;
    
    public $Relation = 1;
    public $RelationTable = "Chromosome";
    public $RelDbCollectionName = "Chromosomes";
    protected $DBTable = "Population";
    protected $DBObjectClassName = "PopulationDBObject";
    
    protected $DBObject;
    
     /**   public $BrowsedColumns = array(
     *   "[nazev_sloupce]" => array(
     *      "header" => "[Nazev sloupce v hlavicce]",
     *      "format" => "[Nazev funkce tridy formatString]",
     *      "template" => "[Html template pro zobrazeni (hodnota je v promenne value)]"
     */
    public $BrowsedColumns = array(
        'ChromosomeID' => array(
            "header" => "ChromosomeID"
            ,"width" => "50px"
        )
        ,'GeneIDs' => array(
            "header" => "GeneIDs"
            ,"width" => "150px"
        )
        ,'Fitness' => array(
            "header" => "Fitness"
            ,"autoexpand" => true
        )
    );
    public $GridTitle = "Population";
    public $GridSubTitle = "Chromosomes";

    public function Fitness($First = false){
        global $Context;
        //zaklad -> ohodnoceni jedince v populaci
        //vysledek: ulozeni hodnoty fitness do db populace, pod id chromozomu viz posloupnost (populaceID->chromozomID->genID)

        $this->Fitness = 0;
        foreach($this->Items as $PopulationDBO){
            $Chromosome = new Chromosome(intval($PopulationDBO->Data[$PopulationDBO->PK[1]]));
            $Chromosome->Fitness($First);
            $this->Fitness += $Chromosome->Fitness;
        }
        if($this->Fitness < $Context->BestPopulationFitness["value"] || $First){
            $Context->BestPopulationFitness = array(
                "id" => $Context->ActivePopulationID
                ,"value" => $this->Fitness
            );
        }else $Context->PopulationFitnessDecay++;
        
        $Context->LastPopulationFitness = $this->Fitness;
    }//eofunc Fitness()

    public function setDBObject($DBObject){
        $this->DBObject = $DBObject;
    }
    
    /**
     *
     * @global type $Randomizer
     * @param int $tempID template chromosome (default = 1)
     * @param type $populationSize
     * @return PopulationDBObject 
     */
    public function generateFirstPopulation($tplID){
        global $Randomizer, $Tools, $Context;
        self::getActivePopulationID();
        //define data object
        $T = new Chromosome(intval($tplID));
        //Get T data from string
        $Data = explode(",", $T->Data['GeneIDs']);
        //preparing random positions for the gene data array
        (integer) $Randomizer->N = 10;
        for($i=0;$i<$this->PopulationSize;$i++){
            $Randomizer->GetRandPosition();
            //merge random positions with "locations" data values
            $TMPData = array();
            foreach($Randomizer->RArr as $k=>$v){
                foreach($Data as $kk=>$vv){
                    if($kk == $v){
                        $TMPData[$v] = $vv;
                    }
                }
            }
            $C = new Chromosome();
            $C->Data[$C->PK[0]] = "";
            $C->Data['GeneIDs'] = implode(",",$TMPData);
            $C->Save();

            $PO = new PopulationDBObject();
            $PO->Data['PopulationID'] = $Context->ActivePopulationID;
            $PO->Data['ChromosomeID'] = $C->InsertedID;
            $PO->Save();
            
            $PopulationIDArr[] = $PO->InsertedID;
            $ChromosomeIDArr[] = $C->InsertedID;
        }
        $PopulationIDs = implode(",", $PopulationIDArr);
        $ChromosomeIDs = implode(",", $ChromosomeIDArr);
        
        $Tools->Log->LogToFile("genom", "Generated new population","PopulationID: {$PO->InsertedID}", "message");
        $Tools->Log->LogToFile("genom", "Set of chromosomes","ChromosomeIDs: {$ChromosomeIDs}", "message");
    }
    /**
     * Find last inserted PopulationID, increment and return.
     * @global type $Tools
     * @return int ActivePopulationID
     */
    public static function getActivePopulationID(){
        global $Tools, $Context;
        $LastPopulation = 0;
        $sql = "SELECT PopulationID FROM Population ORDER BY PopulationID DESC";
        $rs = $Tools->Db->Query($sql);
        while(!$rs->eof()){
            $LastPopulationID = $rs->fields['PopulationID'];
            break;
        $rs->movenext();}
        $Context->ActivePopulationID = $LastPopulationID+1;
        return $Context->ActivePopulationID;
    }
        
}//eoclass Population

class PopulationDBObject extends DBObject{
    protected $DBTable = "Population";
}
?>
