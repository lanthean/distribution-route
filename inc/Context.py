##
# @Copyright (c) 2011, Bc. Martin Bortel
# @author Bc. Martin Bortel <martin.bortel(at)gmail.com>
# @package ea
# @project EA (MM2T 2011) at BUT http://vutbr.cz
##
import request
import json

import inc.common
from inc.tools import is_array
from resources.DbObject import DbObjectUI


class Context(DbObjectUI):
    DBTable = "Context"
    SessionID = int()
    DebugOutput = dict()
    Debug = False

    POST = dict()
    REQUEST = dict()
    Path = ""

    DontUseEA = False

    ActivePopulationID = 1
    LastPopulationFitness = int()
    Round = 0

    ##
    #
    # @var array
    # @uses array(
    #  "PopulationID" => Fitness
    # )
    ##
    BestPopulationFitness = {
        "id": 0
        ,"value": 0
        }
    PopulationFitnessDecay = int()
    MaxPopulationFitnessDecay = 100
    BestChromosomeFitness = {
        "id": 0
        ,"value": 0
        }
    ChromosomeFitnessDecay = int()
    MaxChromosomeFitnessDecay = 20

    MaxPopulationNumber = 100

    NumberOfGenes = int()
    TravelMethod = str()
    EndCondition = 10  # 100 cyklu

    def __init__(self, id=None, id2=None, id3=None):
        self.SessionID = request.REQUEST['REMOTE_ADDR']
        if request.POST:
            self.POST = request.POST
        if request.REQUEST:
            self.REQUEST = request.REQUEST

        if self.POST['TravelMethod']:
            self.TravelMethod = self.POST['TravelMethod']
        if self.POST['MaxPopulationFitnessDecay']:
             self.MaxPopulationFitnessDecay = self.POST['MaxPopulationFitnessDecay']
        if self.POST['MaxPopulationNumber']:
             self.MaxPopulationNumber = self.POST['MaxPopulationNumber']

        super(Context, self).__init__(id, id2, id3)

        # restore Context from db
        if(len(self.Data) > 0):
            ContextData = json.loads(self.Data['Data'], 1)
            self.BestPopulationFitness = json.loads(
                ContextData['BestPopulationFitness'], 1)
            self.BestChromosomeFitness = json.loads(
                ContextData['BestChromosomeFitness'], 1)
            self.ChromosomeFitnessDecay = json.loads(
                ContextData['ChromosomeFitnessDecay'], 1)
            self.PopulationFitnessDecay = json.loads(
                ContextData['PopulationFitnessDecay'], 1)
        # eo __init__()

    ##
    # @method getPath
    # Returns usable url from seourl - it counts on the fact,
    # that as first REQUEST param is 'src',
    # so it strictly uses ampersands..
    # @return str/None string/None -> success/fail
    ##
    def getPath(self):
        self.Path = ""
        if(is_array(self.REQUEST)):
            for k, v in self.REQUEST:
                if k is not 'src':
                    self.Path += "&{}={}".format(k, v)
            return self.Path
        return None
        # eo getPath()

    ##
    # @method EndCondition
    # Returns bool depending on if the end condition was matched.
    # @return boolean True/False pokracujeme/konec - byla splnena podminka
    ##
    def EndCondition(self):
        if self.ActivePopulationID > self.MaxPopulationNumber:
            return False
        elif(self.PopulationFitnessDecay > self.MaxPopulationFitnessDecay):
            return False
        # pro parametrizaci spousteni - ukonceni behu po drivejsi populaci
        elif(self.ActivePopulationID > self.REQUEST['EndPopulationID']):
            return False
        else:
            return True
        # eo EndCondition()
    # eo Context
