<?php
/**
 * @Copyright (c) 2011, Bc. Martin Bortel
 * @author Bc. Martin Bortel
 * @project EA (MM2T 2011) at BUT http://vutbr.cz
 */
class Tools {
    public $Log;
    public $Db;
    
    /**
     *
     * @global type $config 
     */
    public function __construct(){
        global $config;
        
        //require static classes
        require_once("{$config['basepath']}resources/LogToFile.php");
        
        //require classic classes
        require_once("{$config['basepath']}resources/Db.class.php");
        $this->Db = new Db();
    }//eofunc Tools()
    
    public function LogToFile($section, $message, $description = ""){
        LogToFile::saveLog($section, $message, $description);
    }
    public function Redirect($url){
        header("Location: ".$url);
    }
    
    /* static Tools functions */
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
            while($j<$endID+1){
                $IDs[$c]["id"] = $i;
                $IDs[$c]["id2"] = $j;
                $j++;$c++;
            }
        }
       return $IDs;
    }
    
    public static function saveGoogleAPI2DB(){
        global $GoogleAPI;
        $TmpDBO = new DataDBObject();
        $TmpDBO->Data['Data'] = json_encode($GoogleAPI);
        $TmpDBO->Save();
    }
    public static function restoreGoogleAPIFromDB(){
        global $GoogleAPI;
        $TmpDBO = new DataDBObject(1);
        $GAPIStatic = json_decode($TmpDBO->Data['Data'], 1);
        $GoogleAPI->Request = $GAPIStatic['Request'];
        $GoogleAPI->Data = $GAPIStatic['Data'];
    }
    /**
     * Metoda pro vyklizeni databaze (TRUNCATE)
     * Vola postupne metodu self::emptyDBTable
     */
    public static function clearDB(){
        self::emptyDBTable('Parent');
        self::emptyDBTable('Population');
        self::emptyDBTable('Chromosome');
        self::emptyDBTable('FitnessRelation');
        self::emptyDBTable('Gene');
        self::emptyDBTable('temp');
        self::emptyDBTable('Context');
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
    /**
     * @method DiacriticsOff removes diacritics from letters over entire string
     * @param string $str
     * @return string without diacritics 
     */
    public static function DiacriticsOff($str){
        $Diacritics =   array("ě","Ě","š","Š","č","Č","ř","Ř","ž","Ž","ý","Ý","á","Á","í","Í","é","É","ň","Ň","ď","Ď","ť","Ť");
        $SaveStr =      array("e","E","s","S","c","C","r","R","z","Z","y","Y","a","A","i","I","e","E","n","N","d","D","t","T");
       
        return str_replace($Diacritics, $SaveStr, $str);
    }//eofunc DiacriticsOff()
    
    public static function ScriptNameFromURL2Context(){
        global $Context; /* @var $Context Context */
        
        if(!isset($Context->REQUEST['src'])) {
            $Context->REQUEST['src'] = $Context->REQUEST['path'];
        }
        $src = explode("/", $Context->REQUEST['src']);
        $Context->REQUEST['src'] = $src[0];        
//        echo __CLASS__;
//        print_r($Context->REQUEST);
//        print_r($src);
    }
}//eoclass Tools

?>
