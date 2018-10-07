<?php

/**
 * @Copyright (c) 2011, Bc. Martin Bortel
 * @author Bc. Martin Bortel <martin.bortel(at)gmail.com>
 * @package ea
 * @project EA (MM2T 2011) at BUT http://vutbr.cz
 */

class run {
	public $FormData = array();
	
    /**
     * Hlavni spousteci skript - metoda Render je automaticky spoustena (http://localhost/ea/run/)
     * skriptem content.php, ktery je zodpovedny za sluzbu ziskani dat na zaklade url dotazu na server
     * 
     * @global type $Context
     * @global type $GoogleAPI
     * @global type $Page
     * @return type 
     */    
    public static function Render(){
        global $Context, $GoogleAPI, $Page;

        //1st vstupni data -> ziskej z GOOGLEAPI geny a vzdalenosti pouzij do FitnessRelation
        if(count($Context->POST) > 0){ // && self::formValidate()
        //getting google api data
//pred F5 zakomenuj
            $GoogleAPI->PrepareParametersFromPOST($Context->POST['address']);
            if(!$GoogleAPI->Execute()){
                self::getForm("Zadejte prosím smysluplné adresy!");
                return;
            }
            Tools::saveGoogleAPI2DB();
//pred F5 odkomenuj
//            Tools::restoreGoogleAPIFromDB();
            
            //setting travel method -> distance/duration
            $GoogleAPI->setTravelMethod($Context->POST['TravelMethod']);
            //generating genom
//pred F5 zakomenuj
            $GeneIDs = Gene::generateGenom();
            
            if($Context->DontUseEA){
                $C = new Chromosome();
                $C->Data['GeneIDs'] = $GeneIDs;
                $C->Data['Vinner'] = 1;
                $C->Save();
                $Page->Data['body'] = self::getMap($C->Data['GeneIDs']);
                return;
            }

//pred F5 odkomenuj
//            $GeneIDs = "1,2,3,4";
            
            //generatefirst fitnessRelation
//pred F5 zakomenuj
            FitnessRelations::generateFirstFitnessRelations($GeneIDs);
            
        //2nd dodelej FitnessRelation o zbyle variace
            $GeneIDArr = explode(",", $GeneIDs);
            //setting gene counts
            $Context->NumberOfGenes = count($GoogleAPI->Data);
            
//pred F5 odkomenuj
            FitnessRelations::generateRestOfFitnessRelations(Tools::selectCouples($GeneIDArr[0], count($GeneIDArr)-1));
//            return;
            
        //3rd prvni generace
            //template chromosome
            
//pred 2. F5 odkomenuj
            $DefC = new Chromosome(array("GeneIDs" => $GeneIDs, "Default" => 1));
            $DefC->Save();
            
            //start generating random collections of genes - chromosomes (DBObject) and again return all inserted IDs        
            $FirstPopulation = new Population();
            if(isset($Context->POST['PopulationSize'])) $FirstPopulation->PopulationSize = $Context->POST['PopulationSize'];
            $FirstPopulation->generateFirstPopulation($DefC->InsertedID);
            
       //First Fitness
            $P = new Population();
            $P->Filter($P->getPK('0'), $Context->ActivePopulationID);
            $P->Bind();
            $P->Fitness(true);
       //Print grid of first population
//pokud postupne zobrazovani (F5) odkomentuj pred 2.F5
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
            //if(!isset($Context->EndCondition)) $Context->EndCondition = 10;
            while($Context->EndCondition()){
                //clear variables
                unset($P); unset($Selection); unset($Parents); unset($ChildDBOs); unset($C);
                //fitness
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
                while($i < $count){
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
            Population::getActivePopulationID();
            $Context->ActivePopulationID--;
            $P = new Population();
            $P->Filter($P->getPK('0'), $Context->ActivePopulationID);
            $P->Bind();
            $P->Fitness();
            
//pokud postupne zobrazovani (F5) odkomentuj pred F5 pro zobrazeni dalsich populaci
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
            
            if(isset($Context->REQUEST['EndPopulationID'])){
                
                $addons = "Nejlepší Fitness chromozomu: <b>{$Context->BestChromosomeFitness['value']}</b>";
                $addons .= "<br />Nejlepší Fitness populace: <b>{$Context->BestPopulationFitness['value']}</b>";
                $Page->Data = array(
                    'Title' => "Populace #{$Context->ActivePopulationID}"
                    ,'body' => $P->RenderGrid()
                    ,'addons' => $addons
                );
                
            }else{
                //zobraz viteze na mape
                $VinnerID = $Context->BestChromosomeFitness['id'];
                $C = new Chromosome(intval($VinnerID));
                $C->Data['Vinner'] = 1;
                $C->Save();
                self::getMap($C->Data['GeneIDs']);
            }
            return;

        }else{//neni odeslan formular..
            self::getForm();
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
     * @global type $Template
     * @global type $Page
     * @param str $GeneIDs - hodnota sloupce GeneIDs v tabulce chromosome
     * @return html templated page with js google map 
     */
    public static function getMap($GeneIDs){
        global $Template, $Page, $GoogleAPI;
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
        $Page->Data['body'] = $Template->Main('googlemaps.html', $Data);
        return;
    }
    /**
     *
     * @global type $Template
     * @return html templated html form
     */
    public static function getForm($errorMSG = null){
        global $Template, $Context, $Page, $config;
        if(isset($Context->POST['address']))
            $Adresses = $Context->POST['address'];
        else
            $Adresses = array(
                    "1st" => "49.20853N, 16.55968E"        //array("text"=>"První", "error_msg"=>"")
                    ,"2nd" => "49.21942N, 16.64616E"       //array("text"=>"Druhá", "error_msg"=>"")
                    ,"3rd" => "49.20735N, 16.60358E"       //array("text"=>"Třetí", "error_msg"=>"")
                    ,"4th" => "49.22832N, 16.58580E"      //array("text"=>"Čtvrtá", "error_msg"=>"")
                    ,"5th" => "49.17995N, 16.58689E"        //array("text"=>"Pátá", "error_msg"=>"")
                    ,"6th" => "49.19772N, 16.63314E"       //array("text"=>"Šestá", "error_msg"=>"")
                    ,"7th" => "49.19807N, 16.60855E"       //array("text"=>"Sedmá", "error_msg"=>"")
                    ,"8th" => "49.17810N, 16.63688E"        //array("text"=>"Osmá", "error_msg"=>"")
                    ,"9th" => "49.18989N, 16.53274E"      //array("text"=>"Devátá", "error_msg"=>"")
                    ,"10th" => "49.20698N, 16.15192E"     //array("text"=>"Desátá", "error_msg"=>"")
            );
        $Data = array(
            "Rows" => array(
                    "1st" => "První"        //array("text"=>"První", "error_msg"=>"")
                    ,"2nd" => "Druhá"       //array("text"=>"Druhá", "error_msg"=>"")
                    ,"3rd" => "Třetí"       //array("text"=>"Třetí", "error_msg"=>"")
                    ,"4th" => "Čtvrtá"      //array("text"=>"Čtvrtá", "error_msg"=>"")
                    ,"5th" => "Pátá"        //array("text"=>"Pátá", "error_msg"=>"")
                    ,"6th" => "Šestá"       //array("text"=>"Šestá", "error_msg"=>"")
                    ,"7th" => "Sedmá"       //array("text"=>"Sedmá", "error_msg"=>"")
                    ,"8th" => "Osmá"        //array("text"=>"Osmá", "error_msg"=>"")
                    ,"9th" => "Devátá"      //array("text"=>"Devátá", "error_msg"=>"")
                    ,"10th" => "Desátá"     //array("text"=>"Desátá", "error_msg"=>"")
                )
            ,"post" => $Adresses
            ,"sampleLocHref" => "http://mapy.cz/#x=16.467631&y=49.186130&z=9&t=o&umc=9mEe6xTx6q9mSRpiyn1MwceqdnrmgjXjxT7tq9mPzFlZ6cab8Ck4F1le9mAjWjOo9kxTblLO&uml=Brno%2C%20okres%20Brno-m%C4%9Bsto&uml=Brno%2C%20okres%20Brno-m%C4%9Bsto&uml=Brno%2C%20okres%20Brno-m%C4%9Bsto&uml=Brno%2C%20okres%20Brno-m%C4%9Bsto&uml=Brno%2C%20okres%20Brno-m%C4%9Bsto&uml=Brno%2C%20okres%20Brno-m%C4%9Bsto&uml=Brno%2C%20okres%20Brno-m%C4%9Bsto&uml=Brno%2C%20okres%20Brno-m%C4%9Bsto&uml=Brno%2C%20okres%20Brno-m%C4%9Bsto&uml=N%C3%A1m%C4%9B%C5%A1%C5%A5%20nad%20Oslavou%2C%20okres%20T%C5%99eb%C3%AD%C4%8D&u=m"
            );
        if($errorMSG != null) $Data['addons'] = self::echoError($errorMSG);
        $Data['script'] = "run";
        
        $Page->RegisterJavaScriptFile("{$config['baseurl']}design/js/idle.js");
        $Page->Data['body'] = $Template->Main("_form.html", $Data);
    }
    
    //not implemented yet
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
    
    public static function echoError($message){
        $echo = "<span class=\"error\">";
        $echo .= $message;
        $echo .= "</span>";
        return $echo;
    }
    
}

?>