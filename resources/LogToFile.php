<?php

/**
 * @Copyright (c) 2011, Bc. Martin Bortel
 * @author Bc. Martin Bortel <martin.bortel(at)gmail.com>
 * @package ea
 * @project EA (2011) at BUT http://vutbr.cz
 */

class LogToFile {

    public static function saveLog($section, $message, $description = "") {
        global $config, $Context;
        $Now = time();
        $TextToFile = 
        "\n\n@Date:\t" . date("Y-m-d H:i:s", $Now)
        ."\n@IP:\t" . $Context->SessionID
        ."\n@Message:\t" . $message
	."\n@Script:\t" . $location['Script']
	."\n@Line:\t" . $location['Line']
        ;
        if ($description != "")
            $TextToFile .= "\n@Description:\t" . $description;

        $LogFile = fopen($config["basepath"] . "Logs/$section.log", "a+");

        fwrite($LogFile, $TextToFile);
        fclose($LogFile);
    }

}

?>
