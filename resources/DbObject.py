#!/usr/bin/env python
# -*- coding:utf-8 -*-
##
# @Copyright (c) 2011, Bc. Martin Bortel
# @author Bc. Martin Bortel <martin.bortel(at)gmail.com>
# @project EA (2011) at BUT http://vutbr.cz
##
# from inc.tools import date, is_array, is_int, is_string, Tools
import time
import pprint


class DBObject:

    ##
    #
    #  @var array Data je pole s indexem nazvu sloupce a hodnoutou dat
    ##
    Data = dict()
    ASSOC = True
    verbose = False
    ##
    #
    #  @var string DBTable obsahuje nazev tabulky, se kterou je DbObject svazan
    ##
    DBTable = str()

    ##
    #
    #  @var boolean DbExists je True pokud je zaznam ulozen v DB
    ##
    DbExists = False
    ##
    #
    # @var array PK pole nazvu primarnich klicu
    ##
    PK = dict()
    FK = dict()
    ##
    #
    # @var string TimeOfSave údaj o čase uložení DbObjectu
    ##
    TimeOfSave = str()
    InsertedID = int()
    SQL = str()

    Logging = True

    ##
    #
    # @method DbObject
    # konstruktor v zavislosti na vstupnich paramemtrech vytvori instanci objektu
    # @param (int || array || string) id
    #   id == None - vytvori novy prazdny objekt, ktery neni ulozen v DB
    #   id == int - vyhleda zaznam v DB pomoci primarniho klice (a hodnoty id)
    #   id == string - vyhleda zaznam v DB dle hodnoty id (id => WHERE)
    # 	 id == array - naplni pole self.Data polem id
    ##
    def __init__(self, id=None, id2=None, id3=None):
        where = " WHERE "

        rs = Tools.Db.Query("SHOW COLUMNS FROM:self.DBTable} \
                                WHERE `key` = 'PRI'")
        if (rs and rs.recordcount() > 0):
            while not rs.eof():
                self.PK.append(rs.fields['Field'])
                rs.MoveNext()

        if is_int(id):
            where = "{} = {}".format(self.PK[0], id)
            if (id2 is not None and is_int(id2)):
                where += " AND {} = {}".format(self.PK[1], id2)

            if (id3 is not None and is_int(id3)):
                where += " AND {} = {}".format(self.PK[2], id3)

            self.SQL = "SELECT# FROM:self.DBTable} WHERE where"
            rs = Tools.Db.Query(self.SQL, self.verbose)
            if (rs and rs.recordcount() > 0):
                self.Data = rs.fields
                self.DbExists = True
            else:
                self.DbExists = False

        elif is_array(id):
            self.Data = id
            self.DbExists = True
        elif is_string(id):
            self.SQL = "SELECT# FROM:self.DBTable} WHERE id"
            rs = Tools.Db.Query(self.SQL, self.verbose)

            if (rs and rs.recordcount() > 0):
                self.Data = rs.fields
                self.DbExists = True
            else:
                self.DbExists = False

        else:
            self.DbExists = False

        if self.ASSOC and len(self.Data) > 0:
            self.MakeDataAnAssocArr()

        # LogToFile::saveLog("DbObject: ".self.SQL)

        # eo __init__()

    def Refresh(self):
        where = dict()

        IsPKSet = True
        for value in self.PK:
            if not self.Data[value]:
                IsPKSet = False
                break

        if IsPKSet:
            for value in self.PK:
                where.append("value = '{self.Data[value]}'")

            sql = " AND ".join("SELECT# FROM:self.DBTable} WHERE ", where)
            rs = Tools.Db.Query(sql)
            if (rs and rs.recordcount() > 0):
                self.Data = rs.fields
                self.DbExists = True
        # eo Refresh()

    ##
    #
    # @method void
    #      pokud existuje zaznam v DB pod nazvem primarniho klice
    #      upravi stavajici zaznam pomoci Tools.Db metody Updatedict()
    #      pokud zaznam neexistuje, vytvori novy zaznam pomoci
    #      Tools.Db metody Insertdict()
    ##
    def Save(self):
        global Tools, Context

        self.TimeOfSave = date("Y-m-d H:i:s", time.time())

        where = dict()
# //        echo "<br />PRI (".get_class(self).":".self.GetDBTable()."):"
# //        print_r(self.PK)
# //        echo "<br />"
        for value in self.PK:
            where.append("value = '{self.Data[value]}'")

        if len(self.PK) > 0:
            sql = " AND ".join("SELECT COUNT(*) as `Count` FROM:self.DBTable} \
                                WHERE", where)
            rs = Tools.Db.Query(sql)
        else:
            rs = False

        if rs and rs.fields["Count"] > 0:
            self.Data["Updated"] = self.TimeOfSave
# //            self.Data["UpdatorUserId"] = Context.User.Data["UserId"]
            if(self.Logging):
                Tools.Log.LogToFile("database",
                    "UPDATE:{self.PK[0]}={self.Data[self.PK[0]]}\tTime::self.Data['Updated']}",
                    pprint.pprint(self.Data)
                    ,"message")  # //"UpdatorUserID::Context.User.Data['UserId']}/{self.Data['UpdatorUserId']}\tTime::self.Data['Updated']}", print_r(self.Data, True)
            for key, value in self.Data:
                key = "`{key}`"  # osetreni pro buildSQL() - konfilkt user, default, apod.

            result = Tools.Db.UpdateArray(self.DBTable, self.Data, self.verbose)
        else:
            self.Data["Created"] = self.TimeOfSave
# //            self.Data["CreatorUserId"] = intval(Context.User.Data["UserId"])
            if(self.Logging):
                Tools.Log.LogToFile("database",
                "INSERT\tTime::self.Data['Created']}",
                 pprint.pprint(self.Data)
                 ,"message")  #//"CreatorUserID::Context.User.Data['UserId']}/{self.Data['CreatorUserId']}\tTime::self.Data['Created']}"
            for key, value in self.Data:
                key = "`{key}`"  # osetreni pro buildSQL() - konfilkt user, default, apod.

            result = Tools.Db.InsertArray(self.DBTable, self.Data, self.verbose)
            if len(self.PK) > 0:
                if not self.Data[self.PK[0]]:
                    self.Data[self.PK[0]] = Tools.Db.Insert_ID

            self.InsertedID = Tools.Db.Insert_ID
        # eo Save()

    ##
    #
    # @method void
    #      podle zadaneho parametru where smaze zaznam v tabulce self.DBTable
    ##
    def remove(self, where):
        global Tools
        sql = "DELETE FROM:self.DBTable} WHERE " . where
        rs = Tools.Db.Query(sql)
        if (rs):
            self.DbExists = True
        # eo remove()

    def GetDBTable(self):
        return self.DBTable
        # eo GetDBTable()

    def MakeDataAnAssocArr(self):
        DataTMP = self.Data
        self.Data = dict()
        for k, v in DataTMP:
            if not k.isnumeric():
                self.Data[k] = v
        # eo MakeDataAnAssocArr()

    def setLogging(self, boo):
        self.Logging = boo
        # eo setLogging()

    def getLogging(self):
        print(self.__class__.__name__)
        return int(self.Logging)
        # eo getLogging()

    # eo DBObject


class DbObjectUI(DBObject):
    DBTable = str()


# DBObject = DBObject()
