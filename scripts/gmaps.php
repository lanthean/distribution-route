<?php

/**
 * @Copyright (c) 2011, Bc. Martin Bortel
 * @author Bc. Martin Bortel <martin.bortel(at)gmail.com>
 * @project EA (2011) at BUT http://vutbr.cz
 */
class gmaps {
    
    public static function Render(){
        global $Template, $Page;
        $Data['start'] = "Purkynova 93, Brno";
        $Page->Data['body'] = $Template->Main("googlemaps.html", $Data);
    }
}

?>
