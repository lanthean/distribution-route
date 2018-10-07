<?php

/**
 * @Copyright (c) 2010, Bc. Martin Bortel
 * @author Bc. Martin Bortel <martin.bortel(at)gmail.com>
 * @package ea
 * @project EA (MM2T 2010) at BUT http://vutbr.cz
 * @version 1.0.1 
 * 
 * @date 25-Apr-2011
 */

class run {
    public static function Render(){
        global $Context, $GoogleAPI, $Page;
        

        self::emptyDBTable('Parent');
        self::emptyDBTable('Population');
        self::emptyDBTable('Chromosome');
        self::emptyDBTable('FitnessRelation');
        self::emptyDBTable('Gene');
//        self::emptyDBTable('temp');
//        die();
        
        //1st vstupni data -> ziskej z GOOGLEAPI geny a vzdalenosti pouzij do FitnessRelation
        if(count($Context->POST) > 0){
//            print_r($Context->POST);
            //getting google api data
            $GoogleAPI->PrepareParametersFromPOST($Context->POST['address']);
            $GoogleAPI->Execute();
            
//            $TmpDBO = new DataDBObject();
//            $TmpDBO->Data['Data'] = json_encode($GoogleAPI);
//            $TmpDBO->Save();
//            $TmpDBO = new DataDBObject(1);
//            $GAPIStatic = json_decode($TmpDBO->Data['Data'], 1);
//            $GoogleAPI->Request = $GAPIStatic['Request'];
//            $GoogleAPI->Data = $GAPIStatic['Data'];
//            print_r($GoogleAPI->Data);
//            die();
            
            //setting travel method -> distance/duration
            $GoogleAPI->setTravelMethod($Context->POST['TravelMethod']);
            //generating genom
            $GeneIDs = self::generateGenom();
//            die();
//            $GeneIDs = "1,2,3,4";
            //generatefirst fitnessRelation
            self::generateFirstFitnessRelations($GeneIDs);
            //setting gene counts
            $Context->NumberOfGenes = count($GoogleAPI->Data);
            
        //2nd dodelej FitnessRelation o zbyle variace
            $GeneIDArr = explode(",", $GeneIDs);
            self::generateRestOfFitnessRelations(self::selectCouples($GeneIDArr[0], count($GeneIDArr)-1));
//            die();
        //3rd prvni generace
            //template chromosome
            $DefC = new Chromosome(array("GeneIDs" => $GeneIDs, "Default" => 1));
            $DefC->Save();
            //start generating random collections of genes - chromosomes (DBObject) and again return all inserted IDs        
            if(isset($Context->POST['PopulationSize'])) $PopulationSize = $Context->POST['PopulationSize'];
            else $PopulationSize = 10;
            self::generateFirstPopulation($DefC->InsertedID, $PopulationSize);

//while
            //4th Fitness
//            $P = new Population();
//            $P->Filter($P->getPK('0'), $Context->ActivePopulationID);
//            $P->Bind(1);
//            $P->Fitness();
        //5th selekce
//            $Selection = new Select();
//            $Selection->saveSelection();
        //6th krizeni
//            $Parents = new ParentDBC();
//            $Parents->Filter("Parent.".$Parents->getFK('0'), $Context->ActivePopulationID);
//            $Parents->BuildSQL();
//            $Parents->Bind(1);
//            self::getActivePopulationID();
//            $i=0;
//            while($i<count($Parents->Items)){
//                $j = $i+1;
//                $ChildDBOs = array();
//                $ParentDBOs[0] = $Parents->Items[$i];
//                $ParentDBOs[1] = $Parents->Items[$j];
//                $ChildDBOs[] = CrossOver::run($ParentDBOs);
//                $ChildDBOs[] = CrossOver::run($ParentDBOs);
//                foreach($ChildDBOs as $Child){
//                    $C = new Chromosome();
//                    $C->Data['GeneIDs'] = $Child[0]->Data['GeneIDs'];
//                    $C->Save();
//                    $PO = new PopulationDBObject();
//                    $PO->Data['PopulationID'] = $Context->ActivePopulationID;
//                    $PO->Data['ChromosomeID'] = $C->InsertedID;
//                    $PO->Save();                
//                }
//            $i+=2;}
//        return;    
        
        //while
            $c = 0;
            while($c<$Context->EndCondition){
                //fitness
                unset($P); unset($Selection); unset($Parents); unset($ChildDBOs); unset($C);
                $P = new Population();
                $P->Filter($P->getPK('0'), $Context->ActivePopulationID);
                $P->Bind(1);
                $P->Fitness();
                echo $Context->BestChromosomeFitness['value']. " ";
                //selekce
                $Selection = new Select();
                $Selection->saveSelection();
                //krizeni
                $Parents = new ParentDBC();
                $Parents->Filter("Parent.".$Parents->getFK('0'), $Context->ActivePopulationID);
                $Parents->BuildSQL();
                $Parents->Bind(1);
                self::getActivePopulationID();
                $i=0;
                while($i<count($Parents->Items)){
                    $j = $i+1;
                    $ChildDBOs = array();
                    $ParentDBOs[0] = $Parents->Items[$i];
                    $ParentDBOs[1] = $Parents->Items[$j];
                    $ChildDBOs[] = CrossOver::run($ParentDBOs);
                    $ChildDBOs[] = CrossOver::run($ParentDBOs);
                    foreach($ChildDBOs as $Child){
                        $C = new Chromosome();
                        $C->Data['GeneIDs'] = $Child[0]->Data['GeneIDs'];
                        $C->Save();
                        $PO = new PopulationDBObject();
                        $PO->Data['PopulationID'] = $Context->ActivePopulationID;
                        $PO->Data['ChromosomeID'] = $C->InsertedID;
                        $PO->Save();                
                    }
                $i+=2;}
            $c++;}
            //end while
            //last fitness
            $P = new Population();
            $P->Filter($P->getPK('0'), $Context->ActivePopulationID);
            $P->Bind(1);
            $P->Fitness();

    //        $PopulationFin = new Population();
    //        $PopulationFin->Filter($PopulationFin->getPK('0'), $Context->BestPopulationFitness['id']);
    //        $PopulationFin->OrderBy = "Fitness DESC";
    //        $PopulationFin->Bind(1);
            $VinnerID = $Context->BestChromosomeFitness['id'];
            $C = new Chromosome(intval($VinnerID));
            $Page->Data['body'] = self::getMap($C->Data['GeneIDs']);
            return;

        //googlemap -> final
        }else{
            $Page->Data['body'] = self::getForm();
        }
    }
    
    /**
     *
     * @param int $startID
     * @return array $IDs
     */
    public static function selectCouples($startID, $n = 10){
        $c = 0;
//        echo "startID: $startID<br />";
        $endID = $startID + $n;
//        echo "endID: $endID<br />";
        $IDs = array();
        for($i=$startID;$i<$endID;$i++){
            $j = $i+1;
            while($j<$endID+1){
                $IDs[$c]["id"] = $i;
                $IDs[$c]["id2"] = $j;
                $j++;$c++;
            }
        }
//        echo $c;
       return $IDs;
    }
    
    public static function generateGenom(){
        global $GoogleAPI;
        $Data = $GoogleAPI->Data;
        $Names = $GoogleAPI->getFullTextAddresses();
        
        for($i=0;$i<count($Data);$i++){
            if($i==count($Data)-1){
                $GeneData = array("Latitude"=>$Data[$i]['start_location']['lat'], "Longtitude"=>$Data[$i]['start_location']['lng']);
                $Gene = new Gene($GeneData);
                $Gene->Data['Name'] = $Names[$i];
                $Gene->Save();
                $GeneIDArr[] = $Gene->InsertedID;
                
                unset($GeneData);
                unset($Gene);
                
                $GeneData = array("Latitude"=>$Data[$i]['end_location']['lat'], "Longtitude"=>$Data[$i]['end_location']['lng']);
                $Gene = new Gene($GeneData);
                $Gene->Data['Name'] = $Names[$i+1];
                $Gene->Save();
                $GeneIDArr[] = $Gene->InsertedID;
            }else{
                $GeneData = array("Latitude"=>$Data[$i]['start_location']['lat'], "Longtitude"=>$Data[$i]['start_location']['lng']);
                $Gene = new Gene($GeneData);
                $Gene->Data['Name'] = $Names[$i];
                $Gene->Save();
                $GeneIDArr[] = $Gene->InsertedID;
            }
            unset($GeneData);
            unset($Gene);
        }
        return $GeneIDs = implode(",", $GeneIDArr);
    }
    /**
     *
     * @global type $Randomizer
     * @param int $tempID template chromosome (default = 1)
     * @param type $populationSize
     * @return PopulationDBObject 
     */
    public static function generateFirstPopulation($tempID, $populationSize = 10){
        global $Randomizer, $Tools, $Context;
        self::getActivePopulationID();
        //define data object
        $T = new Chromosome(intval($tempID));
        //Get T data from string
        $Data = explode(",", $T->Data['GeneIDs']);
        //preparing random positions for the gene data array
        (integer) $Randomizer->N = 10;
        for($i=0;$i<$populationSize;$i++){
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
    
    //fitness generation is ok, must have valid GeneIDs though!
    public static function generateFirstFitnessRelations($GeneIDs){
        global $GoogleAPI;
        $GeneIDArr = explode(",", $GeneIDs);
        $Data = $GoogleAPI->Data;
//        print_r($GeneIDArr);
//        echo "<br />";
//        print_r($Data);
//        echo "<br />";
        $j=0;
        for($i=0;$i<count($Data);$i++){
//            echo intval($GeneIDArr[$j]).", ".intval($GeneIDArr[$j+1]);
//            echo "<br />";
//            echo $j."<br />";
            $FR = new FitnessRelation(intval($GeneIDArr[$j]), intval($GeneIDArr[$j+1]));
            $TravelMethod = $GoogleAPI->getTravelMethod();
            $FR->Data['Data'] = $Data[$i][$TravelMethod]['value'];
//            print_r($FR);
//            echo "<br />";
            $FR->Save();
        $j++;}
    }
    public static function generateRestOfFitnessRelations($GeneIDs){
        global $GoogleAPI;
        //@TODO[0] DONE get GoogleAPI data for the rest of gene relations
        foreach($GeneIDs as $Gene){
//            echo $Gene['id'];
//            echo "<br />";
//            echo $Gene['id2'];
//            echo "<br />";
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
                $FR->Data['Data'] = $GoogleAPI->Data[0][$GoogleAPI->getTravelMethod()]['value'];
//                print_r($FR);
//                echo "<br />";
                $FR->Save();
            }
        }        
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
    
    /**
     *
     * @param array $inData = array(
     *      array(lat, long)
     *      array(lat, long)
     *      ...
     *    )
     * @return string html/js google map with directions
     */
    public static function getMap($GeneIDs){
        global $Template, $Page;
        //@TODO[7] predelat na chromozom -> ziskat data z db pres geny dle finalniho poradi = vitezny chromozom
            $Chromosome = new Chromosome("GeneIDs = '{$GeneIDs}'");
            $Chromosome->Bind();
            for($i=0;$i<count($Chromosome->Items);$i++){
                if($i == 0){
                    $Data['start'] = $Chromosome->Items[$i]->Data['Latitude'].",".$Chromosome->Items[$i]->Data['Longtitude'];
                }
                elseif($i == count($Chromosome->Items)-1){
                    $Data['end'] = $Chromosome->Items[$i]->Data['Latitude'].",".$Chromosome->Items[$i]->Data['Longtitude'];
                }else{
                    $Data['waypoints'][$i] = $Chromosome->Items[$i]->Data['Latitude'].",".$Chromosome->Items[$i]->Data['Longtitude'];
                }
            }
        $MAP = $Template->Main('googlemaps.html', $Data);
        return $MAP;
    }
    
    public static function getForm(){
        global $Template;
        $Data = array(
            "Rows" => array(
                    "1st" => "První"
                    ,"2nd" => "Druhá"
                    ,"3rd" => "Třetí"
                    ,"4th" => "Čtvrtá"
                    ,"5th" => "Pátá"
                    ,"6th" => "Šestá"
                    ,"7th" => "Sedmá"
                    ,"8th" => "Osmá"
                    ,"9th" => "Devátá"
                    ,"10th" => "Desátá"
                ));
        $Data['script'] = "run";
        return $Template->Main("_form.html", $Data);
    }

    /**
     *
     * @global type $Tools
     * @param type $DBTable
     * @param type $verbose 
     */
    public static function emptyDBTable($DBTable, $verbose = false){
        global $Tools, $Context;
        $sql = "TRUNCATE TABLE `{$DBTable}`";
        $rs = $Tools->Db->Query($sql, $verbose);
        if($rs)
            $Context->DebugOutput['dna::emptyDBTable_'.$DBTable] = "{$DBTable} successfuly truncated";
        else
            $Context->DebugOutput['dna::emptyDBTable_'.$DBTable] = "ERROR:{$DBTable} not truncated<br />MySQL:" . mysql_error();

    }
    
}

?>
