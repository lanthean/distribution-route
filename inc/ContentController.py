##
# @Copyright (c) 2011, Bc. Martin Bortel
# @author Bc. Martin Bortel
# @project EA (MM2T 2011) at BUT http://vutbr.cz
##
from inc.baseconfig import config
from inc.tools import require_once


class ContentController:

    ##
    # @var <string> self.Dest = _REQUEST['LanguageShort']
    ##
    Dest = "run"
    Path = "scripts/"
    ScriptFile = ""

    def getContent(self):
        # could be used to override REQUEST['src']
        # value to something proprietary
        {
            "smthing": "smthingproprietary"
        }.get(self.Dest, "")

        self.ScriptFile = "{}{}{}.php".format(
                                config['basepath'],
                                self.Path,
                                self.Dest)
        try:
            require_once(self.ScriptFile)
            DestObj = self.Dest()
            DestObj.Render()
        except Exception as e:
            raise "Missing content .py file. Exception raised: {}".format(e)
        # eo getContent()
    # eo Content()
