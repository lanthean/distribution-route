#!/usr/bin/env python
# -*- coding:utf-8 -*-
###
# @Author   http://martinbortel.cz
# @Contact  martin.bortel@gmail.com
# @Created  07/10/2018
#
# @Package  Distribution route
###
import os

config = {
    # je potreba nastavit dle vlastniho uloziste

    ##
    # nastaveni mysql uloziste
    "dbfilename": "ea-main.db"  # infile database (sqlite3)
    ,"dbserver" : "localhost"   # nazev databazoveho serveru
    ,"dbuser"   : "root"        # uzivatelske jmeno pristupujici k databazi
    ,"dbpasswd" : ""            # pristupove heslo pro zadaneho uzivatele (viz dbuser)
    ,"dbname"   : "EA"          # nazev databaze

    ##
    # "basepath" : "/cesta/ke/korenovemu/adresari/zdrojovych/souboru/na/serveru/"
	,"basepath" : os.path.dirname(os.path.realpath(__file__))
    ##
    # "baseurl" : "url adresa ke korenu projektu"
    ,"baseurl" : 'http://localhost/ea/'

    ,"APIKey" : 'ABQIAAAAXWcGXwk3hBVAVYTTFL5yMRQtjReqdFHmh44zZdTw87HtIN-cWRTa9h8fuMp6N_ng844Cg4_ohWTOoQ'
}
