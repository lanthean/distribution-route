#!/usr/bin/env python
# -*- coding:utf-8 -*-
##
# @Copyright (c) 2011, Bc. Martin Bortel
# @author Bc. Martin Bortel <martin.bortel(at)gmail.com>
# @package ea
# @project EA (2011) at BUT http://vutbr.cz
##

from inc.baseconfig import config
import inc.Context

class LogToFile:

    @staticmethod
    def saveLog(section, message, description=""):
        Now = time()
        TextToFile = ("\n\n@Date:\t{} \
                        \n@IP:\t{} \
                        \n@Message:\t{} \
                        \n@Script:\t{} \
                        \n@Line:\t{} \
                        ").format(
                            date("Y-m-d H:i:s", Now),
                            Context.SessionID,
                            message,
                            location['Script'],
                            location['Line']
                            )

        if (description != ""):
            TextToFile += "\n@Description:\t{}".format(description)

        with open(config["basepath"] + "Logs/section.log", "a+") as LogFile:
            LogFile.write(TextToFile)

        # eo saveLog()
