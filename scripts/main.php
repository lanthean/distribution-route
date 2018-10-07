<?php

/**
 * @Copyright (c) 2010, Bc. Martin Bortel
 * @author Bc. Martin Bortel <martin.bortel(at)gmail.com>
 * @package ea
 * @project EA (MM2T 2010) at BUT http://vutbr.cz
 * @version 1.0.1 
 * 
 * @date 25-Apr-2011
 */
class main{
    
    public static function Render(){
        global $config, $Page, $Template;
        
        $Page->Data['body'] = $Template->Main('_main.html', array());
        
    }//eofunc Render()
}//eoscriptclass main

?>
