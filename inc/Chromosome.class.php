<?php
/**
 * @Copyright (c) 2011, Bc. Martin Bortel
 * @author Bc. Martin Bortel
 * @project EA (MM2T 2011) at BUT http://vutbr.cz
 */

class Chromosome extends DbObjectUI{
    public $Data = array();
    public $InsertedID;
    
    protected $DBTable = "Chromosome";
    protected $N_1Property = "GeneIDs";
    protected $N_1DBObjectClassName = "Gene";
    
    public $Items = array();
    public $Fitness = 0;

    
    /**
     * obdoba DBCollection->Bind() pro pouziti s $N_1Property;
     */
    public function Bind(){
        $IDs = explode(",", $this->Data[$this->N_1Property]);
        foreach($IDs as $ID){
            eval("\$this->Items[] = new $this->N_1DBObjectClassName(intval(\$ID), true);");
        }        
    }
    
    public function Fitness($First = false){
        global $Context;
        $this->Bind();
        
        $this->Fitness = 0;
        for($i=0;$i<count($this->Items);$i++){
            if($i == count($this->Items) -1 ) break;
            $id = $this->Items[$i]->Data[$this->Items[$i]->PK[0]];
            $id2 = $this->Items[$i+1]->Data[$this->Items[$i]->PK[0]];
            $FitnessRelation = new FitnessRelation(intval($id), intval($id2));
            $this->Fitness += $FitnessRelation->Data['Data'];
        }
        if($this->Fitness < $Context->BestChromosomeFitness["value"] || $First){
            $Context->BestChromosomeFitness = array(
                "id" => $this->Data[$this->PK[0]]
                ,"value" => $this->Fitness
            );
        }else $Context->ChromosomeFitnessDecay++;
        $this->SaveFitness();
        return;
    }
    public function SaveFitness(){
        $this->Data['Fitness'] = $this->Fitness;
        $this->Save();
    }
    /**
     * Deprecated
     * @param type $Float
     * @param type $Length
     * @param type $Depth
     * @return type 
     */
    public static function GetBIN($Float, $Length = 180, $Depth = 6){
        $Dec = $Float * pow(10, $Depth);
        $Dec += $Length*pow(10, $Depth);
        $BIN = decbin($Dec);
        if(strlen($BIN) < 29){
            $zeroCount = 29 - strlen($BIN);
            
            $BINlong = "";
            for($i=0;$i<$zeroCount;$i++){
                $BINlong .= "0";
            }
            $BINlong .= $BIN;
        }
//        echo "Float: $Float";
//        echo "\nDEC: $Dec";
//        echo "\nBIN: $BINlong";
        //echo "\n";
        return $BINlong;
    }
    /**
     * Deprecated
     * @param type $BIN
     * @param type $Length
     * @param type $Depth
     * @return type 
     */
    public static function GetDEC($BIN, $Length = 180, $Depth = 6){
        $Dec = bindec($BIN);
//        echo "\nBIN: ".$BIN;
//        echo "\nDec: ".$Dec;
        $Dec -= $Length*pow(10, $Depth);
        $Float = $Dec / pow(10, $Depth);
//        echo "\nFloat: ".$Float;
//        echo "\n";
        
        return $Dec;
    }
    /**
     * Deprecated
     * @param float $Float
     * @param int $Depth default 6 desetinnych mist
     * @return type 
     */
    public function Float2Bin($Float, $Base = 2, $c = 0, $isFloat = true, $RArr = null, $Depth = null){
        // calculating z=x^y -> z = pow(x,y);
        // calculating z=log2(x) -> z = log(x,2);
        $Part = 0; $Diff = 0; $Dec = 0; $Exp = 0;
        if($RArr == null) $RArr = array('Dec'=>$Dec,'Exp'=>"");
        if($isFloat){
            if($Depth == null) $Depth = pow(10,6);
            $Dec = $Float * $Depth;
            $RArr['Dec'] = $Dec;
        }else{
            $Dec = $Float;
        }
        if($Dec >= $Base){
            for($exp=0;$Dec > $Part;++$exp){
                $Part = pow($Base,$exp);
            }
//            if($Dec/$Base) $i -= 1;
//            elseif($Dec > $Base) $i -= 2;
            
            if($Dec > $Base && !$Dec%$Base){
                $Exp-=2;
            }elseif($Dec == $Base){
                $Exp = 1;
            }else{
                $Exp-=1;
            }
            $Part = pow($Base,($exp));
            $RArr['Exp'][$c] = ($exp);
        }
        $Diff = $Dec - $Part;
        
        //testing
//        echo "\nc = $c";
//        echo "\nDec = $Dec; Part = $Part; K = $Exp;";
//        echo "\nDiff = ".$Diff;
        //eo testing
        

        if($Diff == 1 || $Diff == 0 || $c >= 20){
            echo "\nTransforming dec to bin:";
            if($Diff == 1)
                $BIN = "00000000000000000000000000001";
            elseif($Diff == 0)
                $BIN = "00000000000000000000000000000";
            
            if(is_array($RArr['Exp'])){
                foreach($RArr['Exp'] as $value){
                    $BIN[28-$value] = 1;
                }
            }
//            echo "\nDEC: ";
//            echo $RArr['Dec'];
//            echo "\nBIN: ";
//            echo $BIN;
            return print_r($RArr);
        }else{    
            $this->Float2Bin($Diff, $Base, ++$c, false, $RArr);
        }
    }//eosfunc GetPart()
}//eo class Individual

class Chromosomes extends DBCollectionUI{
    protected $DBObjectClassName = "Chromosome";
}
?>
