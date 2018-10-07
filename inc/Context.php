<?php

/**
 * @Copyright (c) 2011, Bc. Martin Bortel
 * @author Bc. Martin Bortel <martin.bortel(at)gmail.com>
 * @package ea
 * @project EA (MM2T 2011) at BUT http://vutbr.cz
 */

class Context extends DBObjectUI{
    protected $DBTable = "Context";
    public $SessionID;
    public $DebugOutput = array();
    public $Debug;

    public $POST;
    public $REQUEST;
    private $Path = "";

    public $DontUseEA = false;
    
    public $ActivePopulationID = 1;
    public $LastPopulationFitness;
    public $Round = 0;
    /**
     *
     * @var array
     * @uses array(
     *  "PopulationID" => Fitness
     * )
     */
    public $BestPopulationFitness = array(
        "id" => 0
        ,"value" => 0
        );
    public $PopulationFitnessDecay;
    public $MaxPopulationFitnessDecay = 100;
    public $BestChromosomeFitness = array(
        "id" => 0
        ,"value" => 0
        );
    public $ChromosomeFitnessDecay;
    public $MaxChromosomeFitnessDecay = 20;
    
    public $MaxPopulationNumber = 100;
    
    public $NumberOfGenes;
    public $TravelMethod;
    public $EndCondition = 10; //100 cyklu
    
    public function __construct($id = null, $id2 = null, $id3 = null) {
        $this->SessionID = $_SERVER['REMOTE_ADDR'];
        if(isset($_POST)) $this->POST = $_POST;
        if(isset($_REQUEST)) $this->REQUEST = $_REQUEST;
        if(isset($this->POST['TravelMethod'])) $this->TravelMethod = $this->POST['TravelMethod'];
        if(isset($this->POST['MaxPopulationFitnessDecay'])) $this->MaxPopulationFitnessDecay = $this->POST['MaxPopulationFitnessDecay'];
        if(isset($this->POST['MaxPopulationNumber'])) $this->MaxPopulationNumber = $this->POST['MaxPopulationNumber'];
                
        parent::__construct($id, $id2, $id3);
        //restore Context from db
        if(count($this->Data) > 0){
            $ContextData = json_decode($this->Data['Data'], 1);
            $this->BestPopulationFitness = json_decode($ContextData['BestPopulationFitness'], 1);
            $this->BestChromosomeFitness = json_decode($ContextData['BestChromosomeFitness'], 1);
            $this->ChromosomeFitnessDecay = json_decode($ContextData['ChromosomeFitnessDecay'], 1);
            $this->PopulationFitnessDecay = json_decode($ContextData['PopulationFitnessDecay'], 1);
        }
    }
    
    /**
     * @method getPath
     * Returns usable url from seourl - it counts on the fact, that as first REQUEST param is 'src',
     * so it strictly uses ampersands..
     * @return str/null string/NULL -> success/fail
     */
    public function getPath(){
        $this->Path = "";
        if(is_array($this->REQUEST)){
                foreach($this->REQUEST as $k=>$v){
                if($k != 'src'){
                        $this->Path .= "&$k=$v";
                }
            }
            return $this->Path;
        }
        return NULL;
    }
    /**
     * @method EndCondition 
     * Returns bool depending on if the end condition was matched.
     * @return boolean True/False pokracujeme/konec - byla splnena podminka
     */
    public function EndCondition(){
        if($this->ActivePopulationID > $this->MaxPopulationNumber)
            return false;
        
        elseif($this->PopulationFitnessDecay > $this->MaxPopulationFitnessDecay)
            return false;
        
        //pro parametrizaci spousteni - ukonceni behu po drivejsi populaci
        elseif($this->ActivePopulationID > $this->REQUEST['EndPopulationID'])
            return false;
        
        else
            return true;
    }
}//eoclass
?>
