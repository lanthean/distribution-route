#!/usr/bin/env python
# -*- coding:utf-8 -*-
###
# @Author   http://martinbortel.cz
# @Contact  martin.bortel@gmail.com
# @Created  07/10/2018
#
# @Package  Distribution route
#
import sqlite3

import inc.baseconfig as config
import inc.Context as Context
import resources.LogToFile as LogToFile


class Db:
    def Db(self):
        self.conn = sqlite3.connect(config['dbfilename'])
        # eo Db()

    def Query(self, sql, verbose=False):
        if (verbose):
            # print( sql ) #old version
            Context.DebugOutput.append(sql)     # from v.1.0.2 use Context

        try:
            result_set = self.conn.execute(sql)
        except sqlite3.Error as e:
            LogToFile.saveLog("dbconn", "error", e + " SQL: sql")
            # print( "<br />MySQL: "+mysql_error()+", SQL: {sql}" )
            ret = False
        ret = result_set
        return ret
        # eo Query()

    def Find(RS, FieldName, Value):
        RS.movefirst()
        while (not RS.eof() and RS.fields[FieldName] != Value):
            RS.movenext()

        return not RS.eof()
        # eo Find()

    def InsertArray(self, table, array, verbose=False):

        rs = self.Db.query("SHOW COLUMNS FROM " + table)
        if (not rs):
            return False

        sqlKeys = array()
        sqlValues = array()
        for k, v in array:
            if (Db.Find(rs, "Field", k) and k != "0"):
                sqlKeys.append("`{k}`")
                sqlValues.append("'" + str(v) + "'")

        sql = "INSERT INTO table ("
        + sqlKeys.join(", ") + ") VALUES ("
        + sqlValues.join(", ") + ")"

        result = Db.Query(sql, verbose)
        self.Insert_ID = self.conn.Insert_ID()
        # eo InsertArray()

    def UpdateArray(table, array, verbose=False):
        rs = Db.query("SHOW COLUMNS FROM " + table)
        if (not rs):
            return False

        sqlKeys = array()
        sqlValues = array()
        for k, v in array:
            if (Db.Find(rs, "Field", k) and k != "0"):
                sqlKeys.append("`{k}`")
                sqlValues.append("v")

        rs = Db.Query("SHOW COLUMNS FROM " + table + " WHERE `key` = 'PRI'")

        if(rs and rs.recordlen() > 0):
            where = " WHERE "
            first = True
            while (not rs.eof()):
                if (not first):
                    where += " AND "

                where += "`{rs.fields['Field']}` = '{array[rs.fields['Field']]}'"
                first = False
                rs.MoveNext()

            sql = ""
            i = 0
            while i < len(sqlKeys):
                sql += sqlKeys[i] + " = '"
                + str(sqlValues[i]) + "',"
                i += 1

            sql = sql[0:-1]
            sql = "UPDATE table SET " + sql + " " + where
            return Db.Query(sql, verbose)
        else:
            return False
        # eo UpdateArray()

    # eo class Db
