<?php

/**
 * @Copyright (c) 2011, Bc. Martin Bortel
 * @author Bc. Martin Bortel
 * @project EA (MM2T 2011) at BUT http://vutbr.cz
 */

class Gene extends DbObject{
    public $GeneType = 1; //0 = bin; 1 = array;
    
    protected $DBTable = "Gene";
    
    /**
     *
     * @param type $GeneParts
     * @param type $GeneTypeText 
     */
    public function __construct($GeneParts, $READ = false, $GeneTypeText = null){
        if($READ){
            parent::__construct($GeneParts); #in this case GeneParts contains ID of DBObject
        }else{
            if($GeneTypeText != null)
                $this->setGeneType($GeneTypeText);

            if(is_array($GeneParts))
                foreach($GeneParts as $key=>$value) $this->Data[$key] = $value;
        }
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
     * @method setGeneType
     * nastaveni typu zapisu genu (BIN/POLE)
     * 
     * @param int $type 
     * @uses 0 = bin, 1 = array
     */
    public function setGeneType($type){
        switch($type){
            case 'binary': $this->GeneType = 0;
                break;
            case 'array': $this->GeneType = 1;
                break;
            default: $this->GeneType = 1;
                break;
        }
    }//eofunc setGeneType()
    
    /**    
    public function GenerateGeneCode($GeneParts = array()){
        foreach($GeneParts as $key=>$value){
            if($this->GeneType == 0){
                (binary)$this->Code .= $this->GetBIN($value, $key);
            }else{
                (array)$this->Code[$key] = $this->GetBIN($value, $key);
            }
        }
    }//eofunc GenerateGene()
    */
    /*
    public function Save(){
        global $Db;
        $Db->InsertArray($this->DBTable, $this->Data);
        $this->InsertedID = $Db->Insert_ID;
    }//eofunc Save()
    */
    /** getters&setters */
    public function getLocation(){
        if($this->GeneType == 0){
            $this->Data['lat'] = $this->GetFLOAT(strtr($this->Code, 0, 29), 'lat');
            $this->Data['lng'] = $this->GetFLOAT(strtr($this->Code, 29, 58), 'lng');
        }else{
            foreach($this->Code as $key=>$value){
                $this->Location[$key] = $this->GetFLOAT($value, $key);
            }
        }
        return $this->Location;
    }
    public function getCode(){
        try {
            if(isset($this->Code)) return $this->Code;
        }catch(Exception $exc){
            echo $exc->getTraceAsString();
        }
    }
        
    /**
     *
     * @param type $Float input float number (lattitude/longtitude)
     * @param type $Length range of the float (lattitude = 180, longtitude = 90)
     * @param type $Depth depth of the partial part of number (default = 6 digits after comma)
     * @return type BIN
     */
    public static function GetBIN($Float, $Length, $Depth = 6){
        ($Length == 'lat')?$Length=180:$Length=90;
        
        $Dec = $Float * pow(10, $Depth);#tranform 6digit-after-dot float to integer
        $Dec += $Length*pow(10, $Depth);#trasform below zero values (lat: -180+180=0; 180+180=360; long: -90+90=0; 90+90=180;)
        
        $BIN = decbin($Dec);#transform decimal int to binary
        
        //add some zeros to get string of constant length
        (binary)$BINlong = "";
        if(strlen($BIN) < 29){
            $zeroCount = 29 - strlen($BIN);    
            for($i=0;$i<$zeroCount;$i++){
                $BINlong .= "0";
            }
            $BINlong .= $BIN;
        }
        
        return $BINlong;
    }//eofunc GetBIN()
    /**
     *
     * @param binary $BIN
     * @param string $Length
     * @param int $Depth
     * @return float  
     */
    public static function GetFLOAT($BIN, $Length, $Depth = 6){
        ($Length == 'lat')?$Length=180:$Length=90;

        (integer)$Dec = bindec($BIN);
        $Dec -= $Length*pow(10, $Depth);
        (float)$Float = $Dec / pow(10, $Depth);
        return $Float;
    }//eofunc GetFLOAT()
    
}
?>
