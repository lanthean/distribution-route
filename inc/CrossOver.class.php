<?php
/**
 * @Copyright (c) 2011, Bc. Martin Bortel
 * @author Bc. Martin Bortel <martin.bortel(at)gmail.com>
 * @package ea
 * @project EA (MM2T 2011) at BUT http://vutbr.cz
 */
class CrossOver {
    public static function run($ParentDBOs, $crossPoint = 0, $method = "_bussinessTraveler"){
        $chromosome1 = $ParentDBOs[0]->Data['GeneIDs'];
        $chromosome2 = $ParentDBOs[1]->Data['GeneIDs'];
        
        if($crossPoint == 0)
            $crossPoint = CrossOver::getCrossPointBASIC(strlen($chromosome1));
        //run desired method on parents
        $IDs = CrossOver::$method($chromosome1, $chromosome2, $crossPoint);
        //update DBOs and return in same fashion as input
        if(count($IDs)>1){
            $ChromosomeDBOs[0]->Data['GeneIDs'] = $IDs[0];
            $ChromosomeDBOs[1]->Data['GeneIDs'] = $IDs[1];
            
            return $ChromosomeDBOs;
        }else{
            $ChromosomeDBOs[0] = new Chromosome();
            $ChromosomeDBOs[0]->Data['GeneIDs'] = $IDs;

            return $ChromosomeDBOs;
        }
    }
    /**
     * Basic CrossOver method: not used for Bussiness Traveler issue.
     * @param string $chromosome1
     * @param string $chromosome2
     * @param string $crossPoint
     * @return array
     */
    public static function _1k($chromosome1, $chromosome2, $crossPoint){
        $start = 0;
        //strip parents gene ids of ","
        $chromosome1 = str_replace(",", "", $chromosome1);
        $chromosome2 = str_replace(",", "", $chromosome2);
        //start parsing
        $chr1_p1 = substr($chromosome1, $start, $crossPoint);
        $chr2_p1 = substr($chromosome2, $start, $crossPoint);
        $start = $crossPoint;
        $chr1_p2 = substr($chromosome2, $start);
        $chr2_p2 = substr($chromosome1, $start);
        //@TODO[0] DONE vymen casti stringu chr1/chr2
        $chr1 = $chr1_p1.$chr1_p2;
        $chr2 = $chr2_p1.$chr2_p2;
        
        return array($chr1, $chr2);
    }
    /**
     *
     * @global obj $Randomizer
     * @param string $chromosome1
     * @param string $chromosome2
     * @return string 
     */
    public static function _bussinessTraveler($chromosome1, $chromosome2){
        global $Randomizer;
        $ChildGeneIDs = array();
        //strip parents gene ids of ","
        $chr1Arr = explode(",", $chromosome1);
        $chr2Arr = explode(",", $chromosome2);
        //get the smallest geneID from chromosome, and the biggest
        $Randomizer->Min = 0;
        //set max to array length - 2 (to get the right amount of result)
        $Randomizer->Max = count($chr1Arr)-2;
        //set amount of randomly generated values
        $Randomizer->N = 1;
        //set number of digits after decimal point (number of decimals)
        $Randomizer->Depth = 0;
        
        //may new life be born
        $i=0;
        while((count($ChildGeneIDs) < count($chr1Arr))){
            if($i<100) $Randomizer->GetRandValues();
            else{//handbreak, if rand gets into a loop while charging the last missing piece
                foreach($chr1Arr as $ID)
                    if(!in_array($ID, $ChildGeneIDs)) $ChildGeneIDs[] = $ID;
                break;
            }
            $RandPos = $Randomizer->RArr[0];
            if(!in_array($chr1Arr[$RandPos], $ChildGeneIDs)){
                //get Fitness between randomly select gene and his sucessor in both chromosomes.
                if($chr1Arr[$RandPos] == $chr1Arr[$RandPos+1] || $chr1Arr[$RandPos] == $chr2Arr[$RandPos+1]){
                    continue;
                }
                $FR1 = new FitnessRelation(intval($chr1Arr[$RandPos]), intval($chr1Arr[$RandPos+1]));
                $FR2 = new FitnessRelation(intval($chr1Arr[$RandPos]), intval($chr2Arr[$RandPos+1]));
                //child gets randomly generated gene
                $ChildGeneIDs[] = $chr1Arr[$RandPos];
                //if fitness rand-vs-successor from chr1 is lower than from chr2, child is gonna get the chr1 gene
                if($FR1->Data['Data'] < $FR2->Data['Data']){
                    if(!in_array($chr1Arr[$RandPos+1], $ChildGeneIDs))
                        $ChildGeneIDs[] = $chr1Arr[$RandPos+1];
                }else{
                    if(!in_array($chr2Arr[$RandPos+1], $ChildGeneIDs))
                        $ChildGeneIDs[] = $chr2Arr[$RandPos+1];
                }
            }
        $i++;}
        //return new gene ids  
        return implode(",", $ChildGeneIDs);
    }
    
    
    public static function getCrossPointBASIC($length){
        (int) $r = $length / 2;
        return $r;
    }
}

?>
