#!/usr/bin/env python
# -*- coding:utf-8 -*-
##
# @Copyright (c) 2011, Bc. Martin Bortel
# @author Bc. Martin Bortel
# @project EA (MM2T 2011) at BUT http://vutbr.cz
##
import datetime
import json

from inc.baseconfig import config
import resources.LogToFile
import resources.Db
import inc.common
import inc.Context


def require_once(filename, glob=globals(), modulename=''):
    filename = filename[:-3] if filename.endswith('.py') else filename;
    module = modulename if (modulename != '') else filename
    glob[module] = __import__(filename, globals(), locals(), [], -1)


def date(unixtime, format='%m/%d/%Y %H:%M'):
    d = datetime.datetime.fromtimestamp(unixtime)
    return d.strftime(format)


def is_int(var):
    return isinstance(var, int)


def is_string(var):
    return isinstance(var, str)


def is_array(var):
    return isinstance(var, (list, dict))


class Tools:

    def __init__():
        self.Log = str()
        self.Db = str()

        self.Db = Db()
        # eofunc Tools()

    def LogToFile(section, message, description=""):
        LogToFile.saveLog(section, message, description)
        # eo LogToFile()

    def Redirect(url):
        header("Location: {}".format(url))
        # eo Redirect()

    ##
    # static Tools functions
    # @param int startID
    # @return array IDs
    @staticmethod
    def selectCouples(startID, n=10):
        c = 0
        endID = startID + n
        IDs = array()
        i = startID
        while i < endID:
            j = i+1
            while j < (endID+1):
                IDs[c]["id"] = i
                IDs[c]["id2"] = j
                j += 1
                c += 1
            i += 1

        return IDs
        # eo selectCouples()

    @staticmethod
    def saveGoogleAPI2DB():
        TmpDBO = DataDBObject()
        TmpDBO.Data['Data'] = json_encode(GoogleAPI)
        TmpDBO.Save()
        # eo saveGoogleAPI2DB()

    @staticmethod
    def restoreGoogleAPIFromDB():

        TmpDBO = DataDBObject(1)
        GAPIStatic = json.loads(TmpDBO.Data['Data'], 1)
        GoogleAPI.Request = GAPIStatic['Request']
        GoogleAPI.Data = GAPIStatic['Data']
        # eo restoreGoogleAPIFromDB()

    ##
    # Metoda pro vyklizeni databaze (TRUNCATE)
    # Vola postupne metodu Tools.emptyDBTable
    @staticmethod
    def clearDB():
        Tools.emptyDBTable('Parent')
        Tools.emptyDBTable('Population')
        Tools.emptyDBTable('Chromosome')
        Tools.emptyDBTable('FitnessRelation')
        Tools.emptyDBTable('Gene')
        Tools.emptyDBTable('temp')
        Tools.emptyDBTable('Context')
        # eo clearDB()

    ##
    # @global type Tools
    # @param type DBTable
    # @param type verbose
    @staticmethod
    def emptyDBTable(DBTable, verbose=False):
        global Tools, Context
        sql = "TRUNCATE TABLE `{DBTable}`"
        rs = Tools.Db.Query(sql, verbose)
        if rs:
            Context.DebugOutput['dna::emptyDBTable_'.DBTable] = "{DBTable} successfuly truncated"
        else:
            Context.DebugOutput['dna::emptyDBTable_'.DBTable] = "ERROR:{DBTable} not truncated<br />MySQL:" + mysql_error()

        # eo emptyDBTable()

    ##
    # @method DiacriticsOff removes diacritics from letters over entire string
    # @param string str
    # @return string without diacritics
    ##
    # @staticmethod
    # def DiacriticsOff(str):
    #     Diacritics = {"ě","Ě","š","Š","č","Č","ř","Ř","ž","Ž","ý","Ý","á","Á","í","Í","é","É","ň","Ň","ď","Ď","ť","Ť"}
    #     SaveStr = {"e","E","s","S","c","C","r","R","z","Z","y","Y","a","A","i","I","e","E","n","N","d","D","t","T"}
    #
    #     return str.replace(Diacritics, SaveStr)
    #     # eo DiacriticsOff()

    @staticmethod
    def ScriptNameFromURL2Context():

        if not Context.REQUEST['src']:
            Context.REQUEST['src'] = Context.REQUEST['path']

        src = Context.REQUEST['src'].split("/")
        Context.REQUEST['src'] = src[0]
# //        echo __CLASS__
# //        print_r(Context.REQUEST)
# //        print_r(src)
        # eo ScriptNameFromURL2Context()

    # eo Tools
