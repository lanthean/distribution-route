#!/usr/bin/env python
#-*- coding:utf-8 -*-
###
# @Author   http://martinbortel.cz
# @Contact  martin.bortel@gmail.com
# @Created  07/10/2018
#
# @Package  Distribution route
###

import inc.baseconfig as config
from inc import tools, Context, ContentController, Page, Randomizer
import resources

# @var Tools Tools
Tools = tools.Tools()

# context
# @var Context Context
Context = inc.Context(1)

# @var Tools->Log Log
Tools.Log = resources.Log()

##
# Engine
# require_once(config['basepath']."include/Gene.class.php")
# require_once(config['basepath']."include/Chromosome.class.php")
# require_once(config['basepath']."include/Population.class.php")
# require_once(config['basepath']."include/FitnessRelation.class.php")
# require_once(config['basepath']."include/Select.class.php")
# require_once(config['basepath']."include/CrossOver.class.php")

##
# UserInterfaces

# @var Content ContentController
Content = inc.ContentController()

# @var Template Template
Template = inc.Template()

# @var Page Page
Page = inc.Page()

# Engine
# @var GoogleAPI UIGoogleAPI
GoogleAPI = inc.UIGoogleAPI()

# @var Randomizer Randomizer
Randomizer = inc.Randomizer()


class DataDBObject(DbObject):
    DBTable = "temp"
    ASSOC = True
    # eo DataDBObject
