<?php
/**
 * @Copyright (c) 2010, Bc. Martin Bortel
 * @author Bc. Martin Bortel
 * @project EA (MM2T 2010) at BUT http://vutbr.cz
 * @version 1.0.1 
 */ 

/** setup global vars */
include("include/common.inc.php");

//restore Context from db
$ContextData = json_decode($Context->Data['Data'], 1);
$Context->BestPopulationFitness = json_decode($ContextData['BestPopulationFitness'], 1);
$Context->BestChromosomeFitness = json_decode($ContextData['BestChromosomeFitness'], 1);
$Context->ChromosomeFitnessDecay = json_decode($ContextData['ChromosomeFitnessDecay'], 1);
$Context->PopulationFitnessDecay = json_decode($ContextData['PopulationFitnessDecay'], 1);
//if(isset($ContextData['Round']))
//    $Context->Round = json_decode($ContextData['Round'], 1);
//if(count($Context->POST) == 0) $Context->POST = json_decode($ContextData['POST'], 1);
//if(count($Context->REQUEST) == 0) $Context->REQUEST = json_decode($ContextData['REQUEST'], 1);

/** Fetch posted/sent data to server */
//dela se v konstruktoru $Context
//$Context->POST = $_POST;
//$Context->REQUEST = $_REQUEST;
//
/** Prepare content to be displayed */
if(isset($Context->REQUEST['src']))
    $Content->Dest = $Context->REQUEST['src'];
//else{
//    //jsme tu prvne, smaz nepatricnosti z db..
//    $Context->Data['Data'] = "";
//    $Context->Save();
//    //presmeruj na spravnou stranku
//    $Tools->Redirect("{$config['baseurl']}run/");
//}
    
$Content->GetContent();

//save Context to db
$JsonData = array(
        "BestPopulationFitness" => json_encode($Context->BestPopulationFitness)
        ,"BestChromosomeFitness" => json_encode($Context->BestChromosomeFitness)
        ,"ChromosomeFitnessDecay" => json_encode($Context->ChromosomeFitnessDecay)                
        ,"PopulationFitnessDecay" => json_encode($Context->PopulationFitnessDecay)                
//        ,"Round" => json_encode($Context->Round)
//        ,"POST" => json_encode($Context->POST)
        //,"REQUEST" => json_encode($Context->REQUEST)
);
$Context->Data['Data'] = json_encode($JsonData);
$Context->Save();

/** Prepare parameters of the page, on which data is to be displayed */
$Page->PageTemplate = "index.html";
$Page->Debug = false;

/** Render the Page to browser */
echo $Page->Render();

/** end */
?>
