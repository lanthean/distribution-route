<?php
/**
 * @Copyright (c) 2011, Bc. Martin Bortel
 * @author Bc. Martin Bortel
 * @project EA (MM2T 2011) at BUT http://vutbr.cz
 * 
 * @class Randomizer
 * @uses Generating random values for EA selection method Rulette/Stochastic universal sampling  
 */
 
class Randomizer{
    public $RArr = array(); //result array
    public $Min = 0;
    public $Max = 1;
    public $Depth = 2;
    public $N = 10;
    public $INT = false;
    private $Counter = 0;
    private $N_1 = 0.0;
    private $D;
    
    public $i = 0;//just for testing (how many runs it takes to randomize array of count == $this->N

    /**
     * @method GetRandValues
     * Generates $N random values from interval ($this->Min=0, $this->Max=1) with $Depth sensitivity.
     * 
     * $Max vs $Depth -> $Max = $Max*10^$Depth
     * 
     * @uses 
     * 		$RArr holds the result values.
     * @param type $OnlyBetweenOneAndZero default false
     *          
     * true: $Max is set to 1*10^$Depth and is generated rand($Min, $Max)/$Max
     */
    public function GetRandValues($OnlyBetweenOneAndZero = false){
//        echo "depth:".$this->setDepth()." coutner:".$this->Counter." N:".$this->N;
        $this->resetCounter();
        $this->RArr = array();
        while($this->Counter < $this->N){
            if($OnlyBetweenOneAndZero){
                $this->Max = $this->setDepth();
                ($this->INT)?$rand = rand(0, $this->Max):$rand = rand(0, $this->Max)/($this->Max);
            }else{
                $rand = rand($this->Min, $this->Max*$this->setDepth())/$this->setDepth();
            }
            
            if($OnlyBetweenOneAndZero){
                if($rand != 1 && $rand != 0){
                    $this->RArr[] = $rand;
                    $this->Counter++;
                }
            }else{
                $this->RArr[] = $rand;
                $this->Counter++;
            }
        }
        //$this->RArr["-"] = "-.--";
        //return array_values($this->RArr);
    }//eo function
    
    /**
     * @method GetRandPosition()
     * Generates $N random positions in array
     */
    public function GetRandPosition(){
        $this->RArr = array();
        $this->resetCounter();
        
        while($this->Counter < $this->N){
            $pos = rand(0, $this->N-1);
            if(!in_array($pos, $this->RArr)){
                $this->RArr[] = $pos;
                $this->Counter++;
            }
            $this->i++;
        }
    }
    
    /**
     * @method GetPointerBase
     * Prepares the length which is to be between pointers for stoch. universal sampling
     * and generates random value to be the offset of pointers <0, $N_1>
     * @uses 
     * 		$RArr is joined with results of this method.
     * @return void
     */
    public function GetPointerBase(){
        $this->N_1 = 1/$this->N;
        $this->D = rand(0, $this->N_1*10000)/10000;
        $this->D = round($this->D, 2, PHP_ROUND_HALF_UP);
        
        $this->RArr['1/N'] = $this->N_1;
        $this->RArr['D'] = $this->D;
        //return $this->RArr;
    }//eo function

    /**
     * @method ShowResultsCMD
     * Prints the result array into console
     * @return string
     */
    public function ShowResultsCMD(){
        $echo = "
        | Randomizer.class.php
        | 
        | Copyright (c) Bc. Martin Bortel
        | Script generating random values for EA project
        | Settings:
	|\t\tN = {$this->N}  Max = {$this->Max}
        |\n";

        $alt = false;
        foreach($this->RArr as $k => $v){
            if(!is_numeric($k) && $k == "-"){
                 $alt = true;
                 $echo .= "\n\t|\t";
            }else{
                
                if($alt) $echo .= "\t";
                else $echo .= "\n\t|\t";
                if(is_numeric($k)){
                    $k++;
                    if(strlen($k) == 1) $k = "0$k";
                }
                $echo .= "[$k] => $v ";
            }
            //echo $echo;
            $alt = !$alt;
        }
        $echo .= "\n\t|";
        $echo .= "\n";
        
        return $echo;
    }//eo function

    
    function resetCounter(){
        $this->Counter = 0;
    }
    function setDepth(){
        return $Depth = pow(10, $this->Depth);
    }
    
}//eo class
?>
