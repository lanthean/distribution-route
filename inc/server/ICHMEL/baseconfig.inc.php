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
    "dbserver" => "hz-mysql1"   //nazev databazoveho serveru
    ,"dbuser" => "mysql32174"         //uzivatelske jmeno pristupujici k databazi
    ,"dbpasswd" => "DasacaJ11"           //pristupove heslo pro zadaneho uzivatele (viz dbuser)
    ,"dbname" => "mysql37370"           //nazev databaze
    
    /**
     * "basepath" => "/cesta/ke/korenovemu/adresari/zdrojovych/souboru/na/serveru/"
     */
	,"basepath" => str_replace("\\", "/", realpath(dirname(__FILE__).'/..') . "/")
    /**
     * "baseurl" => "url adresa ke korenu projektu"
     */
    ,"baseurl" => 'http://ea.ichmel.cz/'

    ,"APIKey" => 'ABQIAAAAXWcGXwk3hBVAVYTTFL5yMRTeLdO4vl5eiCXINfc1QvAWkk48bRQ8NxP4LDm85xuN_oTTdF8yFkcCXA'    
);
?>
