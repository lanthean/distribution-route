<?php

/**
 * @Copyright (c) 2011, Bc. Martin Bortel
 * @author Bc. Martin Bortel <martin.bortel(at)gmail.com>
 * @package ea
 * @project EA (MM2T 2011) at BUT http://vutbr.cz
 */

class run {
	public $FormData = array();
	
	public function Render(){
		global $Context, $GoogleAPI, $Page;
		if($this->validateForm()){
			
		}
		//if nothing happends, it will allways be there..
		$Page->Data['body'] = $this->getForm();
	}
	public function FirstRun(){}
	public function SecondRun(){}
	public function Cycle(){}
	
    public static function Render_2(){
        global $Context, $GoogleAPI, $Page;

        $body = self::getForm();
        if($body){
            $Page->Data['body']=$body;
            return;
        }
        //1st vstupni data -> ziskej z GOOGLEAPI geny a vzdalenosti pouzij do FitnessRelation
        if(count($Context->POST) > 0){
//            print_r($Context->POST);
        //getting google api data
//            $GoogleAPI->PrepareParametersFromPOST($Context->POST['address']);
//            $GoogleAPI->Execute();
//            Tools::saveGoogleAPI2DB();

            Tools::restoreGoogleAPIFromDB();
            
            //setting travel method -> distance/duration
            $GoogleAPI->setTravelMethod($Context->POST['TravelMethod']);
        //generating genom
//            $GeneIDs = Gene::generateGenom();

            $GeneIDs = "1,2,3,4";
        //generatefirst fitnessRelation
//            FitnessRelations::generateFirstFitnessRelations($GeneIDs);
            
        //2nd dodelej FitnessRelation o zbyle variace
            $GeneIDArr = explode(",", $GeneIDs);
            //setting gene counts
            $Context->NumberOfGenes = count($GoogleAPI->Data);
//            FitnessRelations::generateRestOfFitnessRelations(Tools::selectCouples($GeneIDArr[0], count($GeneIDArr)-1));
        //3rd prvni generace
            //template chromosome
//            $DefC = new Chromosome(array("GeneIDs" => $GeneIDs, "Default" => 1));
//            $DefC->Save();
            //start generating random collections of genes - chromosomes (DBObject) and again return all inserted IDs        
//            if(isset($Context->POST['PopulationSize'])) $PopulationSize = $Context->POST['PopulationSize'];
//            else $PopulationSize = 10;
//            Population::generateFirstPopulation($DefC->InsertedID, $PopulationSize);
       //First Fitness
//            $P = new Population();
//            $P->Filter($P->getPK('0'), $Context->ActivePopulationID);
//            $P->Bind();
//            $P->Fitness(true);
       //Print grid of first population
//            unset($P);
//            $P = new Population();
//            $P->Filter($P->getPK('0'), $Context->ActivePopulationID);
//            $P->OrderBy("Fitness", "ASC");
//            $P->Bind();
//            $addons = "Nejlepší Fitness chromozomu: <b>{$Context->BestChromosomeFitness['value']}</b>";
//            $addons .= "<br />Nejlepší Fitness populace: <b>{$Context->BestPopulationFitness['value']}</b>";
//            $Page->Data = array(
//                'Title' => "Populace #"
//                ,'body' => $P->RenderGrid()
//                ,'addons' => $addons
//            );
//            return;
        
        //while
            $c = 0;
            $Context->EndCondition = 0;
            while($c<$Context->EndCondition){
                //fitness
                unset($P); unset($Selection); unset($Parents); unset($ChildDBOs); unset($C);
                $P = new Population();
                $P->Filter($P->getPK('0'), $Context->ActivePopulationID);
                $P->Bind();
                $P->Fitness();
                //selekce
                $Selection = new Select();
                $Selection->saveSelection();
                //krizeni
                $Parents = new ParentDBC();
                $Parents->Filter("Parent.".$Parents->getFK('0'), $Context->ActivePopulationID);
                $Parents->BuildSQL();
                $Parents->Bind(1);
                Population::getActivePopulationID();
                $i=0;
                if(count($Parents->Items)>$Context->POST['PopulationSize']) $count = $Context->POST['PopulationSize'];
                else $count = count($Parents->Items);
                while($i<$count){
                    $j = $i+1;
                    unset($ChildDBOs);
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
//            Population::getActivePopulationID();
//            $Context->ActivePopulationID--;
//            $P = new Population();
//            $P->Filter($P->getPK('0'), $Context->ActivePopulationID);
//            $P->Bind();
//            $P->Fitness();
            
//            unset($P);
//            $P = new Population();
//            $P->Filter("Population.".$P->getPK('0'), $Context->ActivePopulationID);
//            $P->OrderBy("Fitness", "ASC");
//            $P->Bind();
//            $addons = "Nejlepší Fitness chromozomu: <b>{$Context->BestChromosomeFitness['value']}</b>";
//            $addons .= "<br />Ukazatel stagnace: {$Context->PopulationFitnessDecay}";//Nejlepší Fitness populace: <b>{$Context->BestPopulationFitness['value']}</b>";
//            $Page->Data = array(
//                'Title' => "Populace #{$Context->ActivePopulationID}"
//                ,'body' => $P->RenderGrid()
//                ,'addons' => $addons
//            );
//            return;

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
    
    public function getForm(){
        global $Template;
        $this->FormData = array(
            "Rows" => array(
                    "1st" => array("text"=>"První", "error_msg"=>"")
                    ,"2nd" => array("text"=>"Druhá", "error_msg"=>"")
                    ,"3rd" => array("text"=>"Třetí", "error_msg"=>"")
                    ,"4th" => array("text"=>"Čtvrtá", "error_msg"=>"")
                    ,"5th" => array("text"=>"Pátá", "error_msg"=>"")
                    ,"6th" => array("text"=>"Šestá", "error_msg"=>"")
                    ,"7th" => array("text"=>"Sedmá", "error_msg"=>"")
                    ,"8th" => array("text"=>"Osmá", "error_msg"=>"")
                    ,"9th" => array("text"=>"Devátá", "error_msg"=>"")
                    ,"10th" => array("text"=>"Desátá", "error_msg"=>"")
                ));
		$this->FormData['script'] = "run";
        return $Template->Main("_form.html", $this->FormData);
    }
    public function validateForm(&$Data){
        global $Context;
        $Valid = true;
        $error_msg = "Vyplňte prosím adresu, např. Novákova 2, 60200 Brno anebo GPS koordináty";
        foreach($Context->POST as $k => $v){
            if(is_array($v)){
                foreach($v as $kk=>$vv){
                    if($vv == ""){ 
                        $err[] = $kk;
                        $Valid = false;
                    }
                }
            }else{
                if($v == "" && $k == "PopulationSize"){
                    $err['PopulationSize_err'] = "Vyplňte prosím počet jedinců v populaci (doporučeno je 10).";
                    $Valid = false;
                }
            }
        }
        if(!$Valid)
            foreach($err as $key){
                $Data['Rows'][$key]['error_msg'] = $error_msg;
            }
        return $Valid;
    }

    
}

?>