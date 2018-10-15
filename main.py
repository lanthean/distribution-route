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

from inc.baseconfig import config
# import inc.common
# import resources.Db
import pprint

# bo DEBUG:
print("bo DEBUG")
# setup global

pp.pprint(config)
print(config['dbserver'])

# db = Db.Db()
# pp = pprint.PrettyPrinter(indent=4)
#
# print(db.Query("SELECT * FROM tables;"))
# pp.pprint(baseconfig.config)

print("eo DEBUG")
# eo DEBUG:

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
