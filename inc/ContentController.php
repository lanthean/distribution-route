<?php
/**
 * @Copyright (c) 2011, Bc. Martin Bortel
 * @author Bc. Martin Bortel
 * @project EA (MM2T 2011) at BUT http://vutbr.cz
 */
class ContentController {

    /**
     * @var <string> $this->Dest = $_REQUEST['LanguageShort'];
     */
    public $Dest = "run";
    public $Path = "scripts/";
    public $ScriptFile = "";

    public function getContent() {
        global $config, $Context;
        /*
          //could be used to override REQUEST['src'] value to something proprietary    
          switch($this->Dest){
              case "smthing": $this->Dest = "smthingproprietary";
                  break;
          }
        */
        $this->ScriptFile = "{$config['basepath']}{$this->Path}{$this->Dest}.php";
        
        if (file_exists($this->ScriptFile)) {
            require_once($this->ScriptFile);
            $DestObj = new $this->Dest();
            $DestObj->Render();
        }//eo file_exists();
        else {
            die("missing content .php file..");
        }
    }//eo getContent();
}//eoclass Content();
?>
