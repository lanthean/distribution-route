<?php
/**
 * @Copyright (c) 2011, Bc. Martin Bortel
 * @author Bc. Martin Bortel
 * @project EA (MM2T 2011) at BUT http://vutbr.cz
 */
global $config;
$config = array(
    // je potreba nastavit dle vlastniho uloziste

    /**
     * nastaveni mysql uloziste
     */
    "dbserver" => "mysql.ic.cz"   //nazev databazoveho serveru
    ,"dbuser" => "ic_cargoopti"         //uzivatelske jmeno pristupujici k databazi
    ,"dbpasswd" => "maplemaple"           //pristupove heslo pro zadaneho uzivatele (viz dbuser)
    ,"dbname" => "ic_cargoopti"           //nazev databaze
    
    /**
     * "basepath" => "/cesta/ke/korenovemu/adresari/zdrojovych/souboru/na/serveru/"
     */
	,"basepath" => str_replace("\\", "/", realpath(dirname(__FILE__).'/..') . "/")
    /**
     * "baseurl" => "url adresa ke korenu projektu"
     */
    ,"baseurl" => 'http://cargoopti.ic.cz/'

    ,"APIKey" => 'ABQIAAAAXWcGXwk3hBVAVYTTFL5yMRQtjReqdFHmh44zZdTw87HtIN-cWRTa9h8fuMp6N_ng844Cg4_ohWTOoQ'    
);
?>
