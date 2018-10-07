<?php
/**
 * @Copyright (c) 2011, Bc. Martin Bortel
 * @author Bc. Martin Bortel
 * @project EA (MM2T 2011) at BUT http://vutbr.cz
 */
class UIGoogleAPI{
    /**
     *
     * @var array 
     */
    public $ServiceName = "directions"; //can be set for "geocoding" too
    public $Request = "";
    public $Name = "";
    
    private $APIKey = "";
	
    protected $OutputEncoding = "json"; //json / xml
    protected $TravelMethod = "distance"; //distance/duration
    
    /**
     *
     * @var array $Parameters
     * @uses    $Parameters = array(
     *              "origin" => "starting location"
     *              ,"destination" => "destination location"
     *              ,"waypoints" => "optimize:true|onroad location 1|onroad location 2|..|onroad location k" "optimize:true" -> optimizes order of waypoints
     *              ,"sensor" => "false"
     *          ); 
     */
    protected $Parameters;
    private $ValidParameters = array();
    private $ValidParamKeyOrder = array(
        "directions" => array(
            "origin"
            ,"destination"
            ,"waypoints"
            ,"sensor"
        )
        ,"geocoding" => array(
            "address"
        )
    );
    public $Optimize = "false";
    public $Sensor = "false";
    private $StartLocation; private $EndLocation;
    
    private $dump_path;
    private $echo;
    private $APIReply;
    public $Data;
    
    private $FullTextAddresses;
    
    public function __construct(){
        global $config;
        $this->APIKey = "&key={$config['APIKey']}";
    }//eofunc UIGoogleAPI
    /**
     *
     * @param type $Addresses from POST entered addresses by user, translate to parameters for API
     */
    public function PrepareParametersFromPOST($Addresses = array()){
            foreach($Addresses as $k => $v){
                if($v != "") $Addr[] = $v;
            }
            $this->setFullTextAddresses($Addr);
            //prepare origin, waypoints and destination parameters
            $waypoints = "";
            for($i=0;$i<count($Addr);$i++){
                if($i==0){
                    $origin = $Addr[$i];
                }
                elseif($i==(count($Addr)-1)){
                    $destination = $Addr[$i];
                }
                else{
                    $waypoints .= "|{$Addr[$i]}";
                }
            }
            $waypoints = explode("|", $waypoints);
            $this->setParameters(array(
                "origin" => $origin
                ,"destination" => $destination
                ,"waypoints" => $waypoints
                ,"sensor" => $this->Sensor
            ));
    }
    
    public function BuildRequest($test = false, $Name = ""){
        global $config;
        if($this->Request != "") return;
        if($test && $Name != ""){
            $this->Request = $config[basepath]."/files/json/{$Name}.js";
            return true;
        }
        $this->Request = "http://maps.googleapis.com/maps/api/{$this->ServiceName}/{$this->OutputEncoding}?";
        $this->ValidateParameters();       
        
        $first = true;
        foreach($this->ValidParameters as $key=>$value){
            
            if(!$first) $this->Request .= "&";
            $this->Request .= "{$key}={$value}";
        
        $first=false;}
        
        //sing the request
        //$this->Request .= $this->APIKey;
        
        if($test){
            echo $this->Request;
            die();
        }
        return $this->Request;
    }//eofunc BuildRequest()
    
    protected function ValidateParameters(){
        if(is_array($this->Parameters) && count($this->Parameters) > 0){
            foreach($this->ValidParamKeyOrder[$this->ServiceName] as $key){
                if(array_key_exists($key, $this->Parameters)){
                    if($key == "waypoints"){
                        if(is_array($this->Parameters[$key])){
                            $waypoints = "optimize:{$this->Optimize}";
                            foreach($this->Parameters[$key] as $wp){
                                if($wp != ""){
                                    $waypoints .= "|".urlencode($wp);
                                }
                            }
                            $this->ValidParameters[$key] = $waypoints;
                        }
                    }else{
                        $this->ValidParameters[$key] = urlencode($this->Parameters[$key]);
                    }
                }else{
                    throw new Exception("Request parameters are not valid.");
                }
            }
        }else{
            throw new Exception("Request parameters are not in an array format.");
        }
    }//eofunc ValidateParameters
    private function FormatData(){
        $Legs = $this->Data['routes'][0]['legs'];
        $this->Data = array();
        $i=0;
        if(is_array($Legs)) foreach($Legs as $Leg){
            if(is_array($Leg)) foreach($Leg as $key => $value){
                if($key == "distance" || $key == "duration" || $key == "start_location" || $key == "end_location")
                $this->Data[$i][$key] = $value;
            }
            $i++;
        }
        if(!(count($this->Data) > 0)) return false;
        else return true;
    }//eofunc FormatData()
    
    private function GetAPIReply(){
        if($this->Request != "") $this->BuildRequest();
        
        $this->APIReply = @file_get_contents($this->Request);
        if($this->APIReply != ""){
            return true;
        }
        else{
            echo $this->Request;
            throw new GoogleAPIException("OFFLINE!!");
            return false;
        }
    }//eofunc GetAPIReply()
    private function DecodeJSON($Dump = false){
        if($this->APIReply != ""){
            $this->Data = json_decode($this->APIReply, true);
            if($Dump) $this->SaveJSON2File($this->APIReply, $this->Name);
            return true;
        }else{
            return false;
        }
    }//eofunc DecodeJSON()
    private function DecodeXML(){}//eofunc DecodeXML()

    /**
     *
     * @global type $Context
     * @param type $test
     * @return boo 
     */
    public function Execute($test = false){
        global $Context;
        if(count($this->getFullTextAddresses()) < 3)
                $Context->DontUseEA = true;
        
        $this->BuildRequest($test);
        
        if(!$this->GetAPIReply()) exit("Spatny API request: ".$this->Request);
        if($this->OutputEncoding == "json"){
            $this->DecodeJSON(0);
        }else{
            $this->DecodeXML();
        }
        return $this->FormatData();
    }
    
    
    /** CMD version */
    public function Show($http = false){
        $nl = "\n";
        $Output = "";
        $Output .= $fix = "\n| --------------------------------------------------------------";
        $Output .= $nl."\tCargo v1.0.1";
        $Output .= $nl."| --------------------------------------------------------------";
        $Output .= $nl;
        $Output .= $this->echo;

        if($http){
            $Output = str_replace("\n", "<br />", $Output);
            $Output = str_replace("\t", "&nbsp;&nbsp;&nbsp;", $Output);
        }
        return $Output;
    }//eofunc Show()
    protected function SaveJSON2File($JSON, $name){
        $this->dump_path = "/Data/Dokumenty/TIT-2010/EA/project/php/ea/files/json/{$name}".date("Y-m-d", time()).".js";
        if($fh = fopen($this->dump_path, "w")){
            fwrite($fh, $JSON);
            fclose($fh);
            return true;
        }else{
            return false;
        }
    }//eofunc SaveJSON2File()
    protected function SaveArray2File($arr){
        $this->dump_path = "/Data/Dokumenty/TIT-2010/EA/project/php/ea/files/json_arr/".date("Y-m-d_H-i-s", time()).".dat";
        if($fh = fopen($this->dump_path, "w")){
            $string = date("d/m/Y H:i:s", time());
            $string .="\n";
            $string .= print_r($arr, true);
            $string .= "\n";
            fwrite($fh, $string);
            fclose($fh);
            return true;
        }else{
            return false;
        }
    }//eofunc SaveArray2File()
    private function PrintData(){
        print_r($this->Data);
        return;
        $echo .= "\nStart: {$this->Parameters['origin']}";
	$echo .= "\tEnd: {$this->Parameters['destination']}\t{$this->Name}";
        $echo .= "\nDURATION:";
        $echo .= "\t".$this->ParseArray($this->Data['routes'][0]['legs'][0]['duration'], null, null, true)." s";
        $echo .= "\nDISTANCE:";
        $echo .= "\t".$this->ParseArray($this->Data['routes'][0]['legs'][0]['distance'], null, null, true)." m";
        $echo .= "\nStart Location:";
        $echo .= "\t".$this->ParseArray($this->Data['routes'][0]['legs'][0]['start_location']); $this->StartLoc = $this->Data['routes'][0]['legs'][0]['start_locations'];
        $echo .= "\nEnd Location:";
        $echo .= "\t".$this->ParseArray($this->Data['routes'][0]['legs'][0]['end_location']); $this->EndLoc = $this->Data['routes'][0]['legs'][0]['end_locations'];
        $echo .= "\n";
        $this->echo .= $echo;
        echo $this->echo;
    }//eofunc PrintData()
    private function ParseJSONData($JSONData){
        
    }//eofunc ParseJSONData()
    /* eoCMD */

    /* getters/setters */
    public function getData(){
        return $this->Data;
    }
    /**
     * @param array $Params 
     *
     * @var array $Parameters
     * @uses    $Parameters = array(
     *              "origin" => "starting location"
     *              ,"destination" => "destination location"
     *              ,"waypoints" => "optimize:true|onroad location 1|onroad location 2|..|onroad location k" "optimize:true" -> optimizes order of waypoints
     *              ,"sensor" => "false"
     *          ); 
     * $Params is array of all the coordinates which completes GoogleAPI request when put into http request.
     */
    public function setParameters($Params = array()){
        $this->Parameters = $Params;
    }//eofunc SetParameters
    public function getParameters(){
        return $this->Parameters;
    }//eofunc GetParameters
    /**
     * @param string $Output
     * $Output values one of "json" or "xml"
     */
    public function setOutputEncoding($Output){
        $this->OutputEncoding = $Output;
    }//eofunc SetOutput()
    public function getOutputEncoding(){
        return $this->OutputEncoding;
    }//eofunc GetOutput()
    /**
     *
     * @param type $Method distance/duration
     */
    public function setTravelMethod($Method){
        $this->TravelMethod = $Method;
    }//eofunc setTravelMethod()
    public function getTravelMethod(){
        global $Context;
        if(isset($Context->TravelMethod)) $this->TravelMethod = $Context->TravelMethod;
        
        return $this->TravelMethod;
    }//eofunc getTravelMethod()

    public function setFullTextAddresses($Addresses){
        $this->FullTextAddresses = $Addresses;
    }
    public function getFullTextAddresses(){
        return $this->FullTextAddresses;
    }
    
    public function getAPIKey(){
        return $this->APIKey;
    }

}//eoclass UIGoogleAPI

class GoogleAPIException extends Exception{}
?>
