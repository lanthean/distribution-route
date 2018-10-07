<?php

/**
 * @Copyright (c) 2011, Bc. Martin Bortel
 * @author Bc. Martin Bortel <martin.bortel(at)gmail.com>
 * @package ea
 * @project EA (MM2T 2011) at BUT http://vutbr.cz
 */
class Page {
    /**
     *
     * @var <string> název souboru šablony
     */
    public $PageTemplate;
    /**
     *
     * @var <array> pole javascriptů, které budou umístěny v záhlaví html stránky
     */
    public $JavaScripts = array();
    /**
     *
     * @var <array> pole javascriptů, které budou umístěny v těle html stránky
     */
    public $JavaScriptsToBody = array();
    /**
     *
     * @var <array> pole javascriptových souboru, které budou přidány do záhlaví html stránky
     */
    public $JavaScriptFiles = array();
    /**
     *
     * @var <array> pole css souboru, které budou přidány do záhlaví html stránky
     */
    public $CSSFiles = array();
    /**
     *
     * @var <array> pole css doplňkových deklaraci, které budou přidány do záhlaví html stránky
     */
    public $CSSs = array();
    /**
     *
     * @var <array> data stránky pro templatování
     */
    public $Data = array();
    
    /**
     *
     * @var <string> Titulek stránky - záhlaví html <title></title>
     */
    public $Title;
    /**
     *
     * @var <srting> klíčová slova stránky - záhlaví html <meta keywords="" />
     */
    public $KeyWords;
    /**
     *
     * @var <string> popis stránky - záhlaví html <meta description="" />
     */
    public $Description;

    public $Debug = false;
    
    function __construct(){
     global $config;
        
        $this->Title = "Cargo v1.0.1";
        $this->KeyWords = "Evolutionary Algorithms, route planning optimization";
        
        $this->RegisterCSSFile("main", "style.css");
        
//        $this->RegisterJavaScriptFile("http://maps.google.com/maps?file=api&v=2&key={$config['APIKey']}&sensor=false");
        $this->RegisterJavaScriptFile("http://maps.google.com/maps/api/js?sensor=false");
        $this->RegisterJavaScriptFile($config['baseurl']."resources/jquery/jquery.js");
    }
    
    /**
     * @method RegisterJavaScript Metoda přidá skript do pole $this->JavaScripts
     * @param <string> $ScriptName název skriptu (popis v komentáři)
     * @param <string> $Script tělo skriptu
     * @uses
     *      RegisterJavaScript("jsblock", "
     *          var f = function(s){
     *              ..some code..
     *          }
     *      ");
     */
    function RegisterJavaScript($ScriptName, $Script) {
        if(!empty($ScriptName) && !empty($Script)) {
            if(in_array($ScriptName, $this->JavaScripts) === FALSE){
                $this->JavaScripts[$ScriptName] = $Script;
            }
        }
    }
    /**
     * @method RegisterJavaScriptToBody Metoda přidá skript do pole $this->JavaScriptsToBody
     * @param <string> $ScriptName název skriptu (popis v komentáři)
     * @param <string> $Script tělo skriptu
     * @uses
     *      RegisterJavaScriptToBody("jsblock", "
     *          var f = function(s){
     *              ..some code..
     *          }
     *      ");
     */
    function RegisterJavaScriptToBody($ScriptName, $Script) {
        if(!empty($ScriptName) && !empty($Script)) {
            if(in_array($ScriptName, $this->JavaScriptsToBody) === FALSE){
                $this->JavaScriptsToBody[$ScriptName] = $Script;
            }
        }
    }    
    /**
     * @method RegisterJavaScriptFile Metoda přidá js soubor do pole $this->JavaScriptsFiles
     * @param <string> $filename js soubor - url
     * @uses
     *      RegisterJavaScriptFile("url");
     */
    function RegisterJavaScriptFile($filename) {
        if(in_array($filename, $this->JavaScriptFiles) === FALSE) {
            $this->JavaScriptFiles[] = $filename;
        }
    }
    /**
     * @method RegisterCSSFile Metoda přidá js soubor do pole $this->CSSFiles
     * @param <string> $filename css soubor
     */
    function RegisterCSSFile($Name, $Filename, $PathByHand = false) {
        global $config;
        if(in_array($Filename, $this->CSSFiles) === FALSE) {
            if($PathByHand){
                $Filename = $Filename;
            }else{
                $Filename = $config['baseurl']."design/css/".$Filename;
            }
            $this->CSSFiles[$Name] = $Filename;
        }        
    }
    /**
     * @method RegisterCSS Metoda přidá js soubor do pole $this->CSSFiles
     * @param <string> $filename css soubor
     */
    function RegisterCSS($cssName, $css) {
        if(!empty($cssName) && !empty($css)) {
            if(in_array($cssName, $this->CSSs) === FALSE){
                $this->CSSs[$cssName] = $css;
            }
        }
    }
    
    /**
     * @method GenerateHeaderContent Metoda pro vygenerování záhlaví html stránky
     * @return <type> Metoda vrací obsah záhlaví html stránky
     */
    function GenerateHeaderContent() {
        global $config, $Tools;
        $result = "";
        if(!empty($this->Title)) {
            $result .= "<title>". $this->Title ."</title>";
        }
        if(file_exists("{$config['basepath']}design/icons/favicon.ico"))
            $result .= "<link rel=\"shortcut icon\" href=\"{$config['baseurl']}design/icons/favicon.ico\" />";
        
        $result .= "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />";
        if(!empty($this->KeyWords)) {
            $result .= "\n<meta name=\"keywords\" content=\"". $this->KeyWords ."\" />";
        }
        if(!empty($this->Description)) {
            $result .= "\n<meta name=\"description\" content=\"". $this->Description ."\" />";
        }
        if(!empty($this->CSSFiles)) {
                $result .= "\n<!-- include CSS files -->";
            foreach($this->CSSFiles as $name => $url) {
                $result .= "\n<link rel=\"stylesheet\" type=\"text/css\" href=\"".$url."\" />";
            }
        }
        if(!empty($this->CSSs)){
            $result .= "\n\n<!-- include CSS text/style -->";
            foreach($this->CSSs as $key => $value){
                $result .= "\n<!-- CSS block ". $key ." Begin -->";
                $var = "<style";
                $pos = StrPos($value, $var);
                if($pos === FALSE){
                    $result .= "\n<style type=\"text/css\">\n". $value ."\n</style>\n";                    
                }else{
                    $var_n = array(">\n", "\n</"); //$var_n -> new string
                    $var_r = array(">", "</"); //$var_r -> replaced string
                    $value = Str_Replace($var_r, $var_n, $value);
                    $result .= "\n" .$value;
                }
                $result .= "<!-- CSS block ". $key ." End -->\n";
            }
        }
        if(!empty($this->JavaScriptFiles)) {
            $result .= "\n\n <!-- include JavaScript files -->";
            foreach($this->JavaScriptFiles as $url) {
                $result .= "\n<script type=\"text/javascript\" src=\"".$url."\"></script>";
            }
        }
        if(!empty($this->JavaScripts)){
            $result .= "\n\n <!-- include JavaScript script/text -->";
            foreach($this->JavaScripts as $key => $value){
                $result .= "\n<!-- JavaScriptBlock ". $key ." Begin -->";
                $var = "<script";
                $pos = StrPos($value, $var);
                if($pos === FALSE){
                    $result .= "\n<script type=\"text/javascript\">\n". $value ."\n</script>\n";                    
                }else{
                    $var_n = array(">\n", "\n</"); //$var_n -> new string
                    $var_r = array(">", "</"); //$var_r -> replaced string
                    $value = Str_Replace($var_r, $var_n, $value);
                    $result .= "\n" .$value;
                }
                $result .= "<!-- JavaScriptBlock ". $key ." End -->\n";
            }
        }
        $result .= "\n";
        return $result;
    }
    
    /**
     * @method GenerateBodyScripts Metoda pro generování skriptů do těla html stránky
     * @return <string> metoda vrací jsskript v tagu <script></script>
     */
    function GenerateBodyScripts() {
     	$Bresult = "";
		if(!empty($this->JavaScriptsToBody)){
            $Bresult .= "\n\n <!-- include JavaScript script/text -->";
            foreach($this->JavaScriptsToBody as $key => $value){
                $Bresult .= "\n<!-- JavaScriptBlock ". $key ." Begin -->";
                $var = "<script";
                $pos = StrPos($value, $var);
                if($pos === FALSE){
                    $Bresult .= "\n<script type=\"text/javascript\">\n". $value ."\n</script>\n";                    
                }else{
                    $var_n = array(">\n", "\n</"); //$var_n -> new string
                    $var_r = array(">", "</"); //$var_r -> replaced string
                    $value = Str_Replace($var_r, $var_n, $value);
                    $Bresult .= "\n" .$value;
                }
                $Bresult .= "<!-- JavaScriptBlock ". $key ." End -->\n";
            }
        }
        $Bresult .= "\n";
        return $Bresult;     	
    }
    
    /**
     * @method Render Metoda pro finální sestavení html stránky
     * @global <type> $Template
     * @param <string> $type Proměnná určuje, která z funkcí třídy Template se má použít
     * @return <type> html výstup z templatovacího enginu
     * @uses $Page->Render("Admin"|"Main"|"MainAdmin"|"Menu"|"Listing"|"Addon");
     */
    function Render($type = "Main") {
        global $Template, $Context;
        
        $this->Data['headcontent'] = $this->GenerateHeaderContent();
        $this->Data['bodyScripts'] = $this->GenerateBodyScripts();
        
        if($this->Debug){
            $this->Data['debug'] = "";
            if(is_array($Context->DebugOutput)) foreach($Context->DebugOutput as $k=>$v){
                if(!is_numeric($k))
                    $this->Data['debug'] .= "$k: $v<br />";
                else
                    $this->Data['debug'] .= "$v<br />";
            }
        }
	if(empty($this->PageTemplate)){
            return "Není PageTemplate..";
        }elseif(empty($this->Data)){
            return "Nejsou data..";        	
        }else{
            return $Template->$type($this->PageTemplate, $this->Data);
        }        	
    }  
 }
?>
