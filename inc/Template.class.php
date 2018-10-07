<?php
/**
 * @Copyright (c) 2011, Bc. Martin Bortel
 * @author Bc. Martin Bortel
 * @project EA (MM2T 2011) at BUT http://vutbr.cz
 */

/**
 * @class Template
 * Interface to use the Smarty template Engine 
 */
class Template {

    protected $Smarty;

    /**
     * @method Template
     * Initializes the Smarty template engine
     */
    public function __construct() {
        global $config; //, $Context;
        
        require_once("{$config['basepath']}resources/Smarty/Smarty.class.php");
        $this->Smarty = new Smarty();
        
        $this->Smarty->setCompileDir("{$config['basepath']}design/smarty/templates_c");
        $this->Smarty->setCacheDir("{$config['basepath']}design/smarty/cache");
        $this->Smarty->setConfigDir("{$config['basepath']}design/smarty/configs");
        /**
         * prirazeni globalnich objektu
         */   
        $this->Smarty->assignGlobal('config', $config);
        //$this->Smarty->assignGlobal('Context', $Context);
    }

    /**
     * @method  Root
     * sets templateroot to web root and returns filled template
     * @param   string $TemplateFile = $config['basepath'].$TemplateFile;
     * @param   array $Data data to be filled to the template
     * @return  string/XHTML 
     */
    public function Root($TemplateFile, $Data) {
      global $config;
      
        //nastavuji root pro templaty
        $this->Smarty->setTemplateDir("{$config['basepath']}");
        
        //prirazuji data smarte
        $this->Smarty->assign($Data);
        return $this->Smarty->display($TemplateFile);
    }

    /**
     * @method  Main
     * sets templatesroot to design root and returns filled template
     * @param   string $TemplateFile = $config['basepath']."design/html/".$TemplateFile;
     * @param   array $Data data to be filled to the template
     * @return  string/XHTML
     */
    public function Main($TemplateFile, $Data) {
      global $config;
      
        //nastavuji root pro templaty
        $this->Smarty->setTemplateDir("{$config['basepath']}design/html/");
        
        //prirazuji data smarte
        $this->Smarty->assign($Data);
        
        return $this->Smarty->display($TemplateFile, false);
    }
    
     /**
     * @method  Display
     * sets templatesroot to design root and returns filled template
     * @param   string $TemplateFile = $config['basepath']."design/html/".$TemplateFile;
     * @param   array $Data data to be filled to the template
     * @return  string/XHTML
     */
    public function EchoMain($TemplateFile, $Data) {
      global $config;
      
        //nastavuji root pro templaty
        $this->Smarty->setTemplateDir("{$config['basepath']}design/html/");
        
        //prirazuji data smarte
        $this->Smarty->assign($Data);
        
        //echo smarty fetch($tempfile, $display = TRUE)..
        $this->Smarty->display($TemplateFile);
    }   

}

?>
