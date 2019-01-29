#!/usr/bin/env python
# -*- coding:utf-8 -*-
##
# @Copyright (c) 2011, Bc. Martin Bortel
# @author Bc. Martin Bortel
# @project EA (MM2T 2011) at BUT http://vutbr.cz
##
from jinja2 import Environment, PackageLoader, select_autoescape
import inc.baseconfig as config


##
# @class Template
# Interface to use the Smarty template Engine
##
class Template:

    ##
    # @method Template
    # Initializes the Smarty template engine
    ##
    def __init__(self):
        self.env = Environment(
            loader=PackageLoader('distribution-route',
                                 config['basepath']+"/design"),
            autoescape=select_autoescape(['html', 'xml'])
        )
        # self.env.setCompileDir("{config['basepath']}design/smarty/templates_c")
        # self.env.setCacheDir("{config['basepath']}design/smarty/cache")
        # self.env.setConfigDir("{config['basepath']}design/smarty/configs")
        # ##
        # # prirazeni globalnich objektu
        # ##
        # self.env.assignGlobal('config', config)
        # # self.env.assignGlobal('Context', Context)
        # eo __init__()

    ##
    # @method  Root
    # sets templateroot to web root and returns filled template
    # @param   string TemplateFile = config['basepath'].TemplateFile
    # @param   array Data data to be filled to the template
    # @return  string/XHTML
    ##
    def Root(self, TemplateFile, Data):
        # # nastavuji root pro templaty
        # self.env.setTemplateDir(config['basepath'])
        #
        # # prirazuji data smarte
        # self.env.assign(Data)
        # return self.env.display(TemplateFile)

        # Using Jinja2
        template = self.env.get_template(TemplateFile)
        return template.render(the=Data)
        # eo Root()

    ##
    # @method  Main
    # sets templatesroot to design root and returns filled template
    # @param   string TemplateFile = config['basepath']."design/html/".TemplateFile
    # @param   array Data data to be filled to the template
    # @return  string/XHTML
    ##
    def Main(self, TemplateFile, Data):
        # # nastavuji root pro templaty
        # self.env.setTemplateDir(config['basepath'] + "design/html/")
        #
        # # prirazuji data smarte
        # self.env.assign(Data)
        # return self.env.display(TemplateFile, False)

        # Using Jinja2
        template = self.env.get_template(TemplateFile)
        return template.render(the=Data)
        # eo Main()

    ##
    # @method  Display
    # sets templatesroot to design root and returns filled template
    # @param   string TemplateFile = config['basepath']."design/html/".TemplateFile
    # @param   array Data data to be filled to the template
    # @return  string/XHTML
    ##
    def EchoMain(self, TemplateFile, Data):
        # # nastavuji root pro templaty
        # self.env.setTemplateDir("{config['basepath']}design/html/")
        # # prirazuji data smarty
        # self.env.assign(Data)
        # # echo smarty fetch(tempfile, display = TRUE)..
        # self.env.display(TemplateFile)

        # Using Jinja2
        template = self.env.get_template(TemplateFile)
        return template.render(the=Data)
        # eo EchoMain()
