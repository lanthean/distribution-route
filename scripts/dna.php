<?php

/**
 * @Copyright (c) 2011, Bc. Martin Bortel
 * @author Bc. Martin Bortel <martin.bortel(at)gmail.com>
 * @project EA (2011) at BUT http://vutbr.cz
 */

class dna{
    public static function Render(){
        global $config, $Context, $Tools, $Template, $Page, $Randomizer, $GoogleAPI;
        //$Body = dna::prepareFirstCromosome();
        if(count($Context->POST) > 0){
            //user has sent addresses, lets prepare the genom
            //prepare url parameters for GoogleAPI from POST
            if(is_array($Context->POST['address'])){
                //get GoogleAPI data
                $GoogleAPI->Execute();
                if($Context->DontUseEA){
                    $Context->DebugOutput['EAusage'] = "Not using EA";
                }
                $Page->Data['body'] = dna::getMap($GoogleAPI->Data);
            }
            return;
        } else {
            $Page->Data['body'] = dna::getForm();
            return;
        }


//        dna::emptyDBTable("Population");
//        dna::emptyDBTable("Chromosome");
//        dna::emptyDBTable("Gene");
//        dna::emptyDBTable("FitnessRelation");
        
        //vstupni data:
//        $InDataObj = new DataDBObject(1);
        //1st step
        //decode json data, and by foreach work out Gene (DBObject) entries + return all inserted IDs in string (preferably)
//        $GeneIDs = dna::generateGenom($InDataObj);
//        $Tools->Log->LogToFile("genom", "Generated new genom","GeneIDs: {$GeneIDs}", "message");
        //2nd step
        //save string Gene IDs as default chromosome
//        $GeneIDs = "1,2,3,4,5,6,7,8,9,10";
//        $DefC = new Chromosome(array("GeneIDs" => $GeneIDs, "Default" => 1));
//        $DefC->Save();
        //3rd step 
        //start generating random collections of genes - chromosomes (DBObject) and again return all inserted IDs        
//        $Context->ActivePopulationID = dna::getActivePopulationID();
//        dna::generateFirstPopulation($DefC->InsertedID);
//        dna::generateFitnessData(dna::selectCouples($DefC->InsertedID));    
        //4th step
        //make Population (DbObject) ChromosomeIDs
        $P = new Population();
        $P->Filter($P->getPK('0'), $Context->ActivePopulationID);
        $P->Bind(1);
//        //$Context->DebugOutput['Population_Fitness-Items'] = 
        $P->Fitness();
        unset($P);
        //return;
        
        $P = new Population();
        $P->Relation = 1;
        $P->RelationID = $Context->ActivePopulationID;
        $P->OrderBy = "Fitness ASC";
        $P->verbose = true;
        //$P->Filter($P->getPK('0'), $Context->ActivePopulationID);
        $P->BuildSQL();
        $P->Bind(true);
        $Body = $P->RenderGrid(true);
        
//        $FitnessRelations = new FitnessRelations();
//        $FitnessRelations->OrderBy = "Data ASC";
//        $Page->Data['body'] = $FitnessRelations->RenderGrid();
//        
//        return;

//        $PO = new PopulationDBObject(1);
//        print_r($PO);die("dna#51");
        
        
        //print_r($P->Items);
        
        $Page->Data['body'] .= $Body;
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
    public static function getMap($inData){
        global $Template, $Page;
        //@TODO[7] predelat na chromozom -> ziskat data z db pres geny dle finalniho poradi = vitezny chromozom
            for($i=0;$i<count($inData);$i++){
                if($i == 0){
                    $Data['start'] = implode(", ", $inData[$i]['start_location']);
                }
                elseif($i == count($inData)-1){
                    $Data['waypoints'][$i] = implode(", ", $inData[$i]['start_location']);
                    $Data['end'] = implode(", ", $inData[$i]['end_location']);
                }else{
                    $Data['waypoints'][$i] = implode(", ", $inData[$i]['start_location']);
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
        $Data['script'] = 'dna';
        return $Template->Main("_form.html", $Data);
    }
    public static function generateFitnessData($FRIDs){
        global $Randomizer;
        $Randomizer->N = 1;
        $Randomizer->Min = 400;
        $Randomizer->Max = 40000;
        $Randomizer->Depth = 0;
        foreach($FRIDs as $IDs){
            $Randomizer->GetRandValues();
            $FR = new FitnessRelation(intval($IDs['id']), intval($IDs['id2']));
            $FR->Data['Data'] = $Randomizer->RArr[0];
            $FR->Save();
        }
    }
    /**
     *
     * @param int $startID
     * @return array $IDs
     */
    public static function selectCouples($startID, $n = 10){
        $c = 0;
        $endID = $startID + $n;
        $IDs = array();
        for($i=$startID;$i<$endID;$i++){
            $j = $i+1;
            while($j<11){
                $IDs[$c]["id"] = $i;
                $IDs[$c]["id2"] = $j;
                $j++;$c++;
            }
        }
       return $IDs;
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
    public static function generateGenom($DataObject){
        global $Context;
        $InData = json_decode($DataObject->Data['Data'], true);
        
        $Context->DebugOutput['generateGenom-Count'] = "dna::enerateGenom->inputDataCount = ".count($InData);
        
        foreach($InData as $k=>$GeneData){
            if(!isset($GeneData['Longtitude']) || !isset($GeneData['Latitude'])) continue;
            $Gene = new Gene($GeneData);
            $Gene->Save();
            $GeneIDArr[] = $Gene->InsertedID;
        $Count++;}
        return $GeneIDs = implode(",", $GeneIDArr);
    }
    
    /**
     * Find last inserted PopulationID, increment and return.
     * @global type $Tools
     * @return int ActivePopulationID
     */
    public static function getActivePopulationID(){
        global $Tools;
        $sql = "SELECT PopulationID FROM Population ORDER BY PopulationID DESC";
        $rs = $Tools->Db->Query($sql);
        while(!$rs->eof()){
            $LastPopulationID = $rs->fields['PopulationID'];
            break;
        $rs->movenext();}
        return $LastPopulationID+1;
    }
    /**
     *
     * @global type $Randomizer
     * @param int $tempID
     * @param type $populationSize
     * @return PopulationDBObject 
     */
    public static function generateFirstPopulation($tempID = null, $populationSize = 10){
        global $Randomizer, $Tools, $Context;
        if($tempID == null) $tempID = 10;        
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
    public static function prepareFirstCromosome(){
        global $config, $Page, $Randomizer;

        (string) $Body = "";
      
//        $sum = 0+1+2+3+4+5+6+7+8+9+10+11+12+13+14+15+16+17+18+19;
//        $asum = array_sum($Randomizer->RArr);
//        $sumdiff = $sum - $asum;
//        $Body = "";
//        ($sumdiff == 0)?$Body .= "<span class='success'>":$Body .= "<span class='error'>";
//        $Body .= "
//            <h3>Sum of presumed positions and the actual ones</h3>
//            {$sum} = {$asum}; and diff = {$sumdiff}
//            ";
//        ($sumdiff == 0)?$Body .= "</span>":$Body .= "</span>";
//            
//        $Body .= "<h3>Random positions for {$Randomizer->N}</h3>";
//        $Body .= implode(", ", $Randomizer->RArr);//.print_r($RArr, true);
//        
//        if(sort($Randomizer->RArr, SORT_NUMERIC))
//            $Body .= "<br />".implode(", ",$Randomizer->RArr);
//        else 
//            $Body .= "<br />sort failed";
//        $Body .= "<h3>Number of runs</h3>";
//        $Body .= "{$Randomizer->i}";// && {$Randomizer->getCounter()}<br />";
//
//        $Data = array(
//            "tempID" => 1
//            ,"Data" => '{"0":0.0806,"1":0.2543,"2":0.4295,"3":0.0213,"4":0.8283,"5":0.9201,"6":0.7291,"7":0.9051,"8":0.0785,"9":0.9988,"10":0.0837,"11":0.6043,"12":0.7987,"13":0.77,"14":0.3112,"15":0.0586,"16":0.3151,"17":0.5101,"18":0.2456,"19":0.0248}'
//        );


        //get the  "locations" data

        
        //chromozom (temp)
//        $T = new DataDBObject(1);
//        $Data = dna::getSampleData();
//        $T->Data[$T->PK[0]] = 1;
//        $T->Data[Data] = json_encode($Data);
//        $T->Save();
        
//        $Data = json_decode($T->Data['Data'], true);
//
//        $IDs = dna::saveSampleData2Genom($Data);

        $Body .= "<h3>DB saved gene objs</h3>";
        $Body .= print_r($IDs, true);

        $Body .= "<h3>DB saved gene IDs</h3>";
        (string) $SavedIDs = "";
        $first = 1;
        foreach($IDs as $Gene){
            ($first)?:$SavedIDs .= ",";
            $SavedIDs .= $Gene->InsertedID;
        $first = 0;}
        $Body .= $SavedIDs;

        $T->Data[$T->PK[0]] = "";
        $T->Data['Data'] = $SavedIDs;
        $T->Save();
        

        $Body .= "<h3>TEMP Chromozome</h3>";
        $Body .= print_r($T->Data, true);
        

    return $Body;
        
//        $Body .= "<h3>Randomized array positions of DB temp data</h3>";
//        $Body .= print_r($Data, true);
    }
    public static function saveData2Genom($RNDData){
        foreach($RNDData as $Data){
            $InsertedIDs[] = new Gene($Data);
        }
        return $InsertedIDs;
    }
    
    
    
    public static function getSampleData(){
        global $Randomizer;
        
        $Randomizer->N = 20;
        $Randomizer->Depth = 6;
        $Randomizer->Max = 180;
	$Randomizer->GetRandValues(true);
	for($i=0;count($Randomizer->RArr) > $i; $i=$i+2){
	    $Data[$i]["Latitude"] = $Randomizer->RArr[$i];
	    $Data[$i+1]["Longtitude"] = $Randomizer->RArr[$i+1];
        }        
        return $Data;
    }
}




?>
