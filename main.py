#!/usr/bin/env python
# -*- coding:utf-8 -*-
###
# @Author   http://martinbortel.cz
# @Contact  martin.bortel@gmail.com
# @Created  07/10/2018
#
# @Package  Distribution route
#
# @Copyright (c) 2018, Ing. Martin Bortel
# @author Martin Bortel
# @project EA (MM2T 2012) at BUT http://vutbr.cz
# @version 1.1
###

# setup global vars
# from inc.baseconfig import config
import resources.Db
db = Db.Db()

print(db.Query("SELECT * FROM "))

import os
config = {
    # je potreba nastavit dle vlastniho uloziste

    ##
    # nastaveni mysql uloziste
    "dbserver"  : "localhost"   # nazev databazoveho serveru
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

import pprint
pp = pprint.PrettyPrinter(indent=4)

#pp.pprint(baseconfig.config)
pp.pprint(config)

print("qwe")
print(config['dbserver'])


# print(baseconfig.config['dbserver'])

'''
global config, Context, Content, Tools
/* @var Context Context *//* @var Content Content *//* @var Tools Tools */
Tools::clearDB()
Tools::ScriptNameFromURL2Context()

##
# Fetch posted/sent data to server
# preneseno do konstruktoru global objektu Context
# Context.POST = _POST
# Context.REQUEST = _REQUEST

##
# Prepare content to be displayed
if ( isset(Context.REQUEST['src']) || isset(Context.REQUEST['path'])){
    Content.Dest = Context.REQUEST['src']
} else {
    //jsme tu prvne, smaz nepatricnosti z db..
    Context.Data['Data'] = ""
    Context.Save()
    //presmeruj na spravnou stranku
    Tools.Redirect("{config['baseurl']}?src=run".Context.getPath())
}

Content.GetContent()

# save Context to db
JsonData = {
        "BestPopulationFitness" : json_encode(Context.BestPopulationFitness)
        ,"BestChromosomeFitness" : json_encode(Context.BestChromosomeFitness)
        ,"ChromosomeFitnessDecay" : json_encode(Context.ChromosomeFitnessDecay)
        ,"PopulationFitnessDecay" : json_encode(Context.PopulationFitnessDecay)
}
Context.Data['Data'] = json_encode(JsonData)
Context.Save()

##
# Prepare parameters of the page, on which data is to be displayed
Page.PageTemplate = "index.html"
Page.Debug = false

##
# Render the Page to browser
print(Page.Render())

## END
'''
