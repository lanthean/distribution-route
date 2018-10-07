<?php

/**
 * @Copyright (c) 2011, Bc. Martin Bortel
 * @author Bc. Martin Bortel <martin.bortel(at)gmail.com>
 * @project EA (2011) at BUT http://vutbr.cz
 */

/**
 * class LogEntry
 *
 * Třída LogEntry vytváří objekt typu DbObject,
 */
class LogEntry extends DbObjectUI {

    /**
     * @property $DbTable
     * @var <string>
     *
     * Nastavuje název databázové tabulky ("log").
     */
    protected $DBTable = "Log";
    private $Logging = false;
    
    public function GetForm($Id, $ListingName) {
        global $config;
        require_once($config["basepath"] . "include/Forms.php");
        $Form = new Form();
        $Form->NotFullHeight = FALSE;
        $Form->DbObject = &$this;

        $Form->action = $config["baseurl"] . "services/ListingDetailDataService.php?Id=$Id&ListingName=$ListingName";

//        $Form->addStandardButtons();
        $SaveButton = new SaveButton();
        $CancelButton = new CancelButton();
        $CancelButton->Confirm = "Opravdu chcete formulář vymazat?";
        $Form->addButton($SaveButton);
        $Form->addButton($CancelButton);

        $Form->addelement(array(
            "name" => "detail_tab",
            "type" => "tab",
            "displayname" => "Detail záznamu"
        ));
        $Form->addelement(array(
            "name" => "detail_head",
            "type" => "heading",
            "displayname" => "Detail záznamu Logu",
            "description" => "zobrazení detailních informací záznamu.."
        ));
        $Form->addelement(array(
            "name" => "LogentryType",
            "type" => "text",
            "displayname" => "Typ záznamu"
        ));

        $Form->addelement(array(
            "name" => "Message",
            "type" => "textarea",
            "height" => "100px",
            "displayname" => "Text zprávy"
        ));
//            $Form->addelement(array(
//                    "name" => "Created",
//                    "type" => "date",
//                    "readonly" => "true",
//                    "displayname" => "Vytvořeno"
//            ));
//             $Form->addelement(array(
//                     "name" => "Updated",
//                     "type" => "date",
//                     "displayname" => "Upraveno",
//             ));
        $Form->addelement(array(
            "name" => "Description",
            "type" => "textarea",
            "displayname" => "Popis"
        ));
        $Form->Bind();
        return $Form;
    }

}

/**
 * class Log
 *
 * Třída Log vytváří objekt typu DbCollection, resp. kolekci objektů typu DbObject.
 */
class Log extends DbCollectionUI {

    /**
     * @property $DbObjectClassName
     * @var <string>
     *
     * Nastavuje název objektu typu DbObject, který tvoří základ kolekce.
     */
    protected $DBObjectClassName = "LogEntry";
    /**
     * @property $BrowsedColumns
     * @var <array>
     * @
     *
     * Nastavení zobrazovaných sloupců z databáze a jejich parametrů.
     * @uses
     *      "název sloupce tabulky" => array(
     *          "header" => "text záhlaví sloupce při zobrazení v gridu",
     *          "format" => "nastavení formátu dat", napr. CzechLongDate
     *          "align" => "zarovnání dat ve sloupci",
     *          "width" => "sířka sloupce",
     *          "autoexpand" => "když nastaveno, roztáhne sloupec na maximální šířku" (nepovinné)
     */
    public $BrowsedColumns = array(
        "LogentryType" => array(
            "header" => "Typ"
            , "align" => "left"
            , "width" => 70
        ),
        "Created" => array(
            "header" => "vytvořeno"
//            , "format" => "CzechDateTime"
            , "align" => "left"
            , "width" => 105
        ),
        "Updated" => array(
            "header" => "upraveno"
//            , "format" => "CzechDateTime"
            , "align" => "left"
            , "width" => 105
        ),
        "Message" => array(
            "header" => "text zprávy"
            , "align" => "left"
            , "width" => 400
// 	            "autoexpand" => true
        )
    );
    /**
     * @property $GridTitle
     * @var <string>
     *
     * Nastavení záhlaví Panelu v němž bude zobrazen grid s daty kolekce.
     */
    public $GridTitle = "Výpis Log záznamů";
    /**
     * @property $OrderBy
     * @var <string>
     *
     * Nastavení atributu a směru řazení záznamů při čtení z databáze.
     */
    public $OrderBy = "LogID DESC";
    public $GridStoreName = "Log";
    
    
    private $_file_perms = 0644; 

    function GetDbObjectClassName() {
        return $this->DbObjectClassName;
    }

    /**
     *
     * @method message
     * Metoda message naplní objekt LogEntry daty, nastaví typ záznamu na "message" a zavolá na něm metodu save();
     *
     * @param <string> $message
     *      Proměnná obsahuje vlastní text záznamu.
     * @param <string> $description
     *      Proměnná obsahuje nepovinný popis záznamu.
     */
    function message($section, $message, $description = "") {
        global $Context;
        $LogEntry = new LogEntry;
        $LogEntry->setLogging(0);

        $LogEntry->Data['Section'] = $section;
        $LogEntry->Data['LogentryType'] = "message";
        $LogEntry->Data['Message'] = $message;
        $LogEntry->Data['SessionID'] = $Context->SessionID;
        $LogEntry->Data['Created'] = "";
        $LogEntry->Data['Printed'] = "0";
        if ($description != "")
            $LogEntry->Data["Description"] = $description;
        $LogEntry->save();
    }

    /**
     *
     * @method warning
     * Metoda warning naplní objekt LogEntry daty, nastaví typ záznamu na "warning" a zavolá na něm metodu save();
     *
     * @param <string> $message
     *      Proměnná obsahuje vlastní text záznamu.
     * @param <string> $description
     *      Proměnná obsahuje nepovinný popis záznamu.
     */
    function warning($section, $message, $description = "") {
        global $Context;
        $LogEntry = new LogEntry;
        $LogEntry->setLogging(0);

        $LogEntry->Data['Section'] = $section;
        $LogEntry->Data['LogentryType'] = "warning";
        $LogEntry->Data['Message'] = $message;
        $LogEntry->Data['SessionID'] = $Context->SessionID;
        $LogEntry->Data['Created'] = "";
        $LogEntry->Data['Printed'] = "0";
        if ($description != "")
            $LogEntry->Data["Description"] = $description;
        $LogEntry->save();
    }

    /**
     *
     * @method error
     * Metoda error naplní objekt LogEntry daty, nastaví typ záznamu na "error" a zavolá na něm metodu save();
     *
     * @param <string> $message
     *      Proměnná obsahuje vlastní text záznamu.
     * @param <string> $description
     *      Proměnná obsahuje nepovinný popis záznamu.
     */
    function error($section, $message, $description = "") {
        global $Context;
        $LogEntry = new LogEntry;
        $LogEntry->setLogging(0);

        $LogEntry->Data['Section'] = $section;
        $LogEntry->Data['LogentryType'] = "error";
        $LogEntry->Data['Message'] = $message;
        $LogEntry->Data['SessionID'] = $Context->SessionID;
        $LogEntry->Data['Created'] = "";
        $LogEntry->Data['Printed'] = "0";
        if ($description != "") {
            $LogEntry->Data['Description'] = $description;
        }
        $LogEntry->save();
    }

    /**
     *
     * @global type $config
     * @param <string> $section 
     *      section of the logical structure of the program to which log belongs
     * @param <string> $message
     *      log message
     * @param <string> $description
     *      log message description
     * @param <string> $RunMethodByName
     *      db log method to call 
     */
    function LogToFile($section, $message, $description = "", $CallBack = null) {
        global $config, $Context;

        //prepare the message format
        $Now = time();
        $TextToFile = 
        "\n\n@Date:\t" . date("Y-m-d H:i:s", $Now) .
        "\n@IP:\t" . $Context->SessionID .
        "\n@Message:\t" . $message
        ;
        if ($description != "")
            $TextToFile .= "\n@Description:\t" . $description;

        //find proper log file with path
        $_filename = $config["basepath"] . "Logs/$section.log";
        //prepare file permissions for writing log to file
        //if(file_exists($_filename)) chmod($_filename, $this->_file_perms);
        
        //get the file, and write        
        if($LogFile = @fopen($_filename, "a+")){
            fwrite($LogFile, $TextToFile);
            fclose($LogFile);
        }else{
            $this->error("Log", "Couldn't write to log file: $_filename", $TextToFile);
        }
		
        if ($CallBack != null)
            $this->$CallBack($section, $message, $description);
    }

}

