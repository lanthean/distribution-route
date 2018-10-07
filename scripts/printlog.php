<?php

/**
 * @Copyright (c) 2010, Bc. Martin Bortel
 * @author Bc. Martin Bortel <martin.bortel(at)gmail.com>
 * @package ea
 * @project EA (MM2T 2010) at BUT http://vutbr.cz
 * @version 1.0.1 
 * 
 * @date 13-May-2011
 */
class printlog{
    
    function __autocall(){
        global $config;
        require_once "{$config['basepath']}scripts/printlog.php";
    }
    
    public static function Render(){
        global $Page, $Tools;
        $Tools->Log->Bind(true);
//        print_r($Tools->Log->Items);
        $Page->Data['body'] = $Tools->Log->RenderGrid();
    }
}
?>
