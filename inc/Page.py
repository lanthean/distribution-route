##
# @Copyright (c) 2011, Bc. Martin Bortel
# @author Ing. Martin Bortel <martin.bortel(at)gmail.com>
# @package ea
# @project EA (MM2T 2011) at BUT http://vutbr.cz

from inc.baseconfig import config
import inc.Template
import inc.Context
# import inc.tools as Tools
import os


class Page:

    # @var <string> název souboru šablony
    PageTemplate = str()

    # @var <dict> pole javascriptů, které budou umístěny v záhlaví html stránky
    JavaScripts = dict()

    # @var <dict> pole javascriptů, které budou umístěny v těle html stránky
    JavaScriptsToBody = dict()

    # @var <dict> pole javascriptových souboru,
    # které budou přidány do záhlaví html stránky
    JavaScriptFiles = dict()

    # @var <dict> pole css souboru, které budou přidány do záhlaví html stránky
    CSSFiles = dict()

    # @var <dict> pole css doplňkových deklaraci,
    # které budou přidány do záhlaví html stránky
    CSSs = dict()

    # @var <dict> data stránky pro templatování
    Data = dict()

    # @var <string> Titulek stránky - záhlaví html <title></title>
    Title = str()

    # @var <srting> klíčová slova stránky - záhlaví html <meta keywords="" />
    KeyWords = str()

    # @var <string> popis stránky - záhlaví html <meta description="" />
    Description = str()

    Debug = False

    def __init__(self):
        self.Title = "Cargo v1.0.1"
        self.KeyWords = "Evolutionary Algorithms, route planning optimization"

        self.RegisterCSSFile("main", "style.css")

        # self.RegisterJavaScriptFile("http://maps.google.com/maps?file=api&v=2&key=:config['APIKey']&sensor=False")
        self.RegisterJavaScriptFile(
            "http://maps.google.com/maps/api/js?sensor=False")
        self.RegisterJavaScriptFile(
            config['baseurl']+"resources/jquery/jquery.js")
    # eo __init__()

    """
     @method RegisterJavaScript Metoda přidá skript do pole self.JavaScripts
    # @param <string> ScriptName název skriptu (popis v komentáři)
    # @param <string> Script tělo skriptu
    # @uses
    #      RegisterJavaScript("jsblock", "
    #          var f = def(s):
    #              ..some code..
    #      ")
    """
    def RegisterJavaScript(self, ScriptName, Script):
        if ScriptName and Script:
            if ScriptName in self.JavaScripts:
                self.JavaScripts[ScriptName] = Script
        # eo RegisterJavaScript()

    """
      @method RegisterJavaScriptToBody
    #  Metoda přidá skript do pole self.JavaScriptsToBody
    # @param <string> ScriptName název skriptu (popis v komentáři)
    # @param <string> Script tělo skriptu
    # @uses
    #      RegisterJavaScriptToBody("jsblock", "
    #          var f = def(s):
    #              ..some code..
    #      ")
    """
    def RegisterJavaScriptToBody(self, ScriptName, Script):
        if ScriptName and Script:
            if ScriptName in self.JavaScriptsToBody:
                self.JavaScriptsToBody[ScriptName] = Script
        # eo RegisterJavaScriptToBody()

    """
     @method RegisterJavaScriptFile
    # Metoda přidá js soubor do pole self.JavaScriptsFiles
    # @param <string> filename js soubor - url
    # @uses
    #      RegisterJavaScriptFile("url")
    """
    def RegisterJavaScriptFile(self, filename):
        if filename not in self.JavaScriptFiles:
            self.JavaScriptFiles.append(filename)
        # eo RegisterJavaScriptFile()

    """
     @method RegisterCSSFile Metoda přidá js soubor do pole self.CSSFiles
    # @param <string> filename css soubor
    """
    def RegisterCSSFile(self, Name, Filename, PathByHand=False):
        if Filename in self.CSSFiles:
            if(PathByHand):
                Filename = Filename
            else:
                Filename = "{}design/css/{}".format(
                                config['baseurl'], Filename)

            self.CSSFiles[Name] = Filename
        # eo RegisterCSSFile()

    """
     @method RegisterCSS Metoda přidá js soubor do pole self.CSSFiles
    # @param <string> filename css soubor
    """
    def RegisterCSS(self, cssName, css):
        if cssName and css:
            if cssName in self.CSSs:
                self.CSSs[cssName] = css
        # eo RegisterCSS()

    """
     @method GenerateHeaderContent Metoda pro vygenerování záhlaví html stránky
    # @return <type> Metoda vrací obsah záhlaví html stránky
    """
    def GenerateHeaderContent(self):
        result = ""
        if self.Title:
            result += "<title>{}</title>".format(self.Title)

        if(os.path("config['basepath']/design/icons/favicon.ico")):
            result += "<link rel=\"shortcut icon\" \
                href=\":config['baseurl']design/icons/favicon.ico\" />"

        result += "<meta http-equiv=\"Content-Type\" \
                content=\"text/html; charset=utf-8\" />"

        if self.KeyWords:
            result += "\n<meta name=\"keywords\" \
                content=\"{}\" />".format(self.KeyWords)

        if self.Description:
            result += "\n<meta name=\"description\" \
                content=\"{}\" />".format(self.Description)

        if self.CSSFiles:
            result += "\n<!-- include CSS files -->"
            for name, url in self.CSSFiles:
                result += "\n<link rel=\"stylesheet\" \
                    type=\"text/css\" href=\"{}\" />".format(url)

        if self.CSSs:
            result += "\n\n<!-- include CSS text/style -->"
            for key, value in self.CSSs:
                result += "\n<!-- CSS block {} Begin -->".format(key)
                var = "<style"
                pos = var.find(value)
                if not pos:
                    result += "\n<style type=\"text/css\">\n{}\n\
                        </style>\n".format(value)
                else:
                    var_n = dict(">\n", "\n</")  # var_n -> new string
                    var_r = dict(">", "</")      # var_r -> replaced string
                    value = value.replace(var_r, var_n)
                    result += "\n{}".format(value)

                result += "<!-- CSS block {} End -->\n".format(key)

        if self.JavaScriptFiles:
            result += "\n\n <!-- include JavaScript files -->"
            for url in self.JavaScriptFiles:
                result += "\n<script type=\"text/javascript\" src=\"{}\">\
                    </script>".format(url)

        if self.JavaScripts:
            result += "\n\n <!-- include JavaScript script/text -->"
            for key, value in self.JavaScripts:
                result += "\n<!-- JavaScriptBlock {} Begin -->".format(key)
                var = "<script"
                pos = value.find(var)
                if pos:
                    result += "\n<script type=\"text/javascript\">\n{}\n\
                        </script>\n".format(value)
                else:
                    var_n = dict(">\n", "\n</")  # var_n -> new string
                    var_r = dict(">", "</")      # var_r -> replaced string
                    value = value.replace(var_r, var_n)
                    result += "\n{}".format(value)

                result += "<!-- JavaScriptBlock {} End -->\n".format(key)

        result += "\n"
        return result

    """
     @method GenerateBodyScripts Metoda
    # pro generování skriptů do těla html stránky
    # @return <string> metoda vrací jsskript v tagu <script></script>
    """
    def GenerateBodyScripts(self):
        Bresult = ""
        if self.JavaScriptsToBody:
            Bresult += "\n\n <!-- include JavaScript script/text -->"
            for key, value in self.JavaScriptsToBody:
                Bresult += "\n<!-- JavaScriptBlock {} Begin -->".format(key)
                var = "<script"
                pos = value.find(var)
                if pos:
                    Bresult += "\n<script type=\"text/javascript\">\n{}\n\
                        </script>\n".format(value)
                else:
                    var_n = dict(">\n", "\n</")  # var_n -> new string
                    var_r = dict(">", "</")  # var_r -> replaced string
                    value = value.replace(var_r, var_n)
                    Bresult += "\n{}".format(value)

                Bresult += "<!-- JavaScriptBlock {} End -->\n".format(key)

        Bresult += "\n"
        return Bresult

    """
     @method Render Metoda pro finální sestavení html stránky
    # @global <type> Template
    # @param <string> type Proměnná určuje, která z funkcí třídy
    #   Template se má použít
    # @return <type> html výstup z templatovacího enginu
    # @uses Page->Render("Admin"|"Main"|"MainAdmin"|"Menu"|"Listing"|"Addon")
    """
    def Render(self, type="Main"):

        self.Data['headcontent'] = self.GenerateHeaderContent()
        self.Data['bodyScripts'] = self.GenerateBodyScripts()

        if(self.Debug):
            self.Data['debug'] = ""
            if(is_dict(Context.DebugOutput)):
                for k, v in enumerate(Context.DebugOutput):
                    if not k.isnumeric():
                        self.Data['debug'] += "k: v<br />"
                    else:
                        self.Data['debug'] += "v<br />"

        if not self.PageTemplate:
            return "Není PageTemplate.."
        elif not self.Data:
            return "Nejsou data.."
        else:
            return inc.Template.type(self.PageTemplate, self.Data)

    # eo Render()
# eo Page
