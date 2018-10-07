<?php
/**
 * @Copyright (c) 2011, Bc. Martin Bortel
 * @author Bc. Martin Bortel <martin.bortel(at)gmail.com>
 * @package ea
 * @project EA (MM2T 2011) at BUT http://vutbr.cz
 */
class Select{
    public $N = 5;
    public $ChildrenN = 10;
    public $Population;
    public $Parents;
    public $Children;
    
    public function __construct($ActivePopulationID = null){
        global $Context;
        if($ActivePopulationID == null) $ActivePopulationID = $Context->ActivePopulationID;
        
        $this->Population = new Population();
        $this->Population->Relation = true;
        $this->Population->RelationID = $ActivePopulationID;
    }
    /**
     * Method fills array Parents with selected parents from active population.
     * @global type $Context 
     */
    public function getParents(){
        //@TODO[9] parent selection process has to be applied before filling Parents with items..
        $this->Population->OrderBy = "Fitness ASC";
        $this->Population->Bind(true);        
        $this->Parents = $this->Population->Items;
    }
    public function saveSelection(){
        $this->getParents();
        foreach($this->Parents as $Parent){
//            print_r($Parent->Data);
//            echo "<br />";
            $ParentDBO = new ParentDBO();
            $ParentDBO->Data = $Parent->Data;
            $ParentDBO->Data['Created'] = null;
            $ParentDBO->Data['Updated'] = null;
//            print_r($ParentDBO);
//            echo "<br />";
            $ParentDBO->Save();
        }
    }
}

class ParentDBC extends DBCollectionUI{
    protected $DBTable = "Parent";
    protected $DBObjectClassName = 'ParentDBO';
    public $RelationTable = "Chromosome";
    public $RelDbCollectionName = "Chromosomes";
    public $Relation = true;
}
class ParentDBO extends DBObjectUI{
    protected $DBTable = "Parent";
}
?>
