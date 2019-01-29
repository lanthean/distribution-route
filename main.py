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

# import json
from inc.baseconfig import config
import inc.common
# import inc.Page
# import resources.Db
# import pprint

# bo DEBUG:
print("bo DEBUG")
# setup global
print(inc.common.GoogleAPI)

# pp = pprint.PrettyPrinter(indent=4)
# pp.pprint(config)
print(config['dbserver'])

# db = Db.Db()
#
# print(db.Query("SELECT * FROM tables;"))
# pp.pprint(baseconfig.config)

print("eo DEBUG")
exit()
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
        "BestPopulationFitness" : json.dumps(Context.BestPopulationFitness)
        ,"BestChromosomeFitness" : json.dumps(Context.BestChromosomeFitness)
        ,"ChromosomeFitnessDecay" : json.dumps(Context.ChromosomeFitnessDecay)
        ,"PopulationFitnessDecay" : json.dumps(Context.PopulationFitnessDecay)
}
Context.Data['Data'] = json.dumps(JsonData)
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
