<?php
/**
 * @Copyright (c) 2011, Bc. Martin Bortel
 * @author Bc. Martin Bortel <martin.bortel(at)gmail.com>
 * @project EA (2011) at BUT http://vutbr.cz
 */

/**
 * class to prepare table grid for method of dbcollection RenderGrid()
 */
class TblGrid {
	public $RowNum;

	public $Columns = array();
	public $Data = array();
	public $Title = "Grid Title";
        public $SubTitle = "Grid SubTitle";

	public $TblAlign = "center";
	public $TblWidth = "100%";
	public $TblBorder = "0px";
	public $TblCellSpacing = "2px";
	public $TblCellPadding = "0px";
	public $TblBgColor = "";

	public $TrBgColor = array("#888888","#DCDCDC","#223344","#006600");
	public $TdBgColor = "#66cc66";
        
        public $InsertButtonMenu = false;
        
	function Render(&$DbCollection){
		global $config, $Context, $Lang, $Tools, $User;

		//$DbCollection->GridEntry = new Entry();
//		switch($Context->User->Data[GroupId]){
//			case 1: 
//				$DbCollection->GridEntry = new RootEntry();
//				break;
//			case 2:
//				$DbCollection->GridEntry = new AdminEntry();
//				break;
//			default:
//				$DbCollection->GridEntry = new UserEntry();
//				break;
//		}
		//testing
                $DbCollection->GridEntry = new UserEntry();
                $DbCollection->GridEntry->Read = 1;
		$DbCollection->GridEntry->Write = 0;
		$DbCollection->GridEntry->Edit = 0;
		$DbCollection->GridEntry->Delete = 0;

//                $this->Title = $DbCollection->GridTitle;
//                $this->SubTitle = $DbCollection->GridSubTitle;

		$DbCollection->GridEntry->ListingClassName = get_class($DbCollection);
		//if(count($Context->User->Data) > 0){
		if($DbCollection->GridEntry->Write || $DbCollection->GridEntry->Edit || $DbCollection->GridEntry->Delete)
		$DbCollection->InsertButtonMenu = true;
		else
		$DbCollection->InsertButtonMenu = false;
		//        }
		$display = "";


		if($DbCollection->GridEntry->Read){
//        		$display .= "\n<h1>{$this->Title}</h1>";
//                        $display .= "\n<div width=\"200px\"><p>{$this->SubTitle}</p></div>";
			if($DbCollection->GridEntry->Write)
			$display .= $DbCollection->GridEntry->Write();
			$display .= "\n<TABLE ALIGN=\"{$this->TblAlign}\" WIDTH=\"{$this->TblWidth}\" BORDER=\"{$this->TblBorder}\" BGCOLOR=\"{$this->TblBgColor}\" CELLSPACING=\"{$this->TblCellSpacing}\" CELLPADDING=\"{$this->TblCellPadding}\">";
			$display .= "\n\t<tr bgcolor=\"{$this->TrBgColor[0]}\">";
			// ColumnModel
			if(!empty($DbCollection->BrowsedColumns)){

				$display .= "\n\t\t";
				foreach($DbCollection->BrowsedColumns as $key => $value){
					if(isset($value["header"])) $header = $value["header"];
					if(isset($value["autoexpand"])) $this->GridAutoExpandColumn = $key;
					if(isset($value["width"])) $width = "width: {$value["width"]}px;"; else $width = null;
					if(isset($value["align"])) $align = "text-align: {$value["align"]};"; else $align = "text-align: left;";

					if($this->GridAutoExpandColumn == $key) $value["width"] = "auto";
					$display .= "\n\t\t<td style=\"{$width} {$align} padding: 1px; margin: 0;\"><p class=\"td_header\">{$value['header']}</p></td>";
				}
				if($DbCollection->InsertButtonMenu)
				$display .= "<td width=\"10px\" style=\"text-align: center; padding: 1px; margin: 0;\"></td>"; //<p class=\"td_header\">{$Lang[Grid][action]}</p>
				$display .= "\n\t</tr>";
				//$DbCollection->Bind();
				$i=0;
				foreach($DbCollection->Items as $Item){
					if($i%2) $TrBgColor = $this->TrBgColor[0];
					else $TrBgColor = $this->TrBgColor[1];
					$i++;
					$display .= "\n\t<tr bgcolor=\"{$TrBgColor}\">";
					foreach($DbCollection->BrowsedColumns as $k => $v){
						foreach($Item->Data as $key => $value){
							if(is_string($key) && $key == $k)
							$display .= "\n\t\t<td style=\"text-align:margin: 0;\"><p class=\"td\">{$value}</p></td>";
						}
					}
					if($DbCollection->InsertButtonMenu){
						$display .= "\n\t\t<td align=\"center\" width=\"50px\">";
						if($DbCollection->GridEntry->Edit){
							$display .= $DbCollection->GridEntry->Edit($Item->Data[$Item->PK[0]]);
						}
						if($DbCollection->GridEntry->Delete){
							$display .= $DbCollection->GridEntry->Delete($Item->Data[$Item->PK[0]]);
						}
						$display .= "</td>";
					}
					$display .= "\n\t</tr>";
				}
			}else{
                                $sql = "SHOW COLUMNS FROM ".$DbCollection->GetDbTable();
				$rs  = $Tools->Db->Query($sql);
				if ($rs && $rs->recordcount() > 0) {
					$first = true;
					while (!$rs->eof()) {
						$Columns[] = $rs->fields['Field'];
						$rs->MoveNext();
					}
				}else{
                                    return false;
                                }
				if($this->InsertButtonMenu){
					$display .= "\n\t\t<td width=\"10px\" style=\"text-align: center;\"><p class=\"td\">{$Lang[Grid][akce]}</p></td>";
				}
				foreach($Columns as $column){
					$display .= "\n\t\t<td width=\"10px\"><p class=\"td\">{$column}</p></td>";
				}
				$display .= "\n\t</tr>";
				$DbCollection->Bind();
				$i = 0;
				foreach($DbCollection->Items as $Item){
					if($i%2) $TrBgColor = $this->TrBgColor[0];
					else $TrBgColor = $this->TrBgColor[1];
					$i++;
					$display .= "\n\t<tr bgcolor=\"{$TrBgColor}\">";
					if($DbCollection->InsertButtonMenu){
						$display .= "\n\t\t<td align=\"center\" width=\"50px\">";
						$display .= $DbCollection->GridEntry->Edit($Item->Data[$Item->PK[0]]);
						$display .= $DbCollection->GridEntry->Delete($Item->Data[$Item->PK[0]]);
						$display .= "</td>";
					}
					foreach($Item->Data as $key => $value){
						if(is_string($key))
						$display .= "\n\t\t<td><p class=\"td\">{$value}</p></td>";
					}
					$display .= "\n\t</tr>";
				}
			}


			$display .= "\n\t</tr>";
			$display .= "\n</TABLE>";

			// ----------------------- INSERT BUTTON TOOLBAR --------------------------------
		}else{
			$display = "Nemáte právo číst..";
		}
		return $display;

	}
}


/**
 * objekt zapouzdřující funkce administračního menu
 */
class Entry{
	public $Read = false;
	public $Write = false;
	public $Edit = false;
	public $Delete = false;

	public $DestinationUrl = "";
	public $IconUrl = "";
	public $ListingClassName;

	function __construct(){
		global $config;
		if($this->IconUrl == "")
		$this->IconUrl = "{$config['baseurl']}design/icons/FamFamFam/";
		if($this->DestinationUrl == "")
		$this->DestinationUrl = "{$config['baseurl']}popups/ListingDetail.php";
	}

	function Write(){}
	function Edit($id){}
	function Delete($id){}
}

class RootEntry extends Entry{
	function Write(){
		global $config, $Context, $Lang;
		$display = "\n<a href=\"#\" class=\"db\" onclick=\"window.open('{$this->DestinationUrl}?{$Context->Language}&ListingName={$this->ListingClassName}', 'Detail', 'width=800,height=600,top=250,left=320,menubar=yes,resizable=yes,left=0,top=0');return false\">
                        <img border=\"0\" src=\"{$this->IconUrl}database_add.png\" alt=\"{$Lang[Grid][add_new]}\" title=\"{$Lang[Grid][add_new]}\"/>
                        {$Lang[Grid][add_new]}
                        </a>";
                        return $display;
	}
	function Edit($id){
		global $config, $Context, $Lang;
		$display = "";
		$display .= "\n<a href=\"#\" onclick=\"window.open('{$this->DestinationUrl}?Lang={$Context->Language}&Id=$id&ListingName={$this->ListingClassName}', 'Detail', 'width=800,height=600,top=250,left=320,menubar=yes,resizable=yes,left=0,top=0');return false\">
                        <img border=\"0\" src=\"{$this->IconUrl}database_edit.png\" alt=\"{$Lang[Grid][edit]}\" title=\"{$Lang[Grid][edit]}\"/>
                        </a>";
		return $display;
	}
	function Delete($id){
		global $config, $Context, $Lang;
		$display = "";
		$display .= "\n<a href=\"#\" onclick=\"if(confirm('".$Lang['Grid']['confirm_detele_message']."')){window.open('{$this->DestinationUrl}?{$Context->Language}&Id=$id&ListingName={$this->ListingClassName}&remove',null, 'width=10,height=10,top=10000,left=10000,menubar=no,resizable=no,left=0,top=0');} return false\">
                        <img border=\"0\" src=\"{$this->IconUrl}database_delete.png\" alt=\"{$Lang[Grid][delete]}\" title=\"{$Lang[Grid][delete]}\"/>
                        </a>";
		return $display;
	}
}

class AdminEntry extends Entry{
	function Write(){
		global $config, $Context, $Lang;
		if($this->Write){
			$display = "\n<a href=\"#\" class=\"db\" onclick=\"window.open('{$this->DestinationUrl}?{$Context->Language}&ListingName={$this->ListingClassName}', 'Detail', 'width=800,height=600,top=250,left=320,menubar=yes,resizable=yes,left=0,top=0');return false\">
                            <img border=\"0\" src=\"{$this->IconUrl}database_add.png\" alt=\"{$Lang[Grid][add_new]}\" title=\"{$Lang[Grid][add_new]}\"/>
                            {$Lang[Grid][add_new]}
                            </a>";
		}
		return $display;
	}
	function Edit($id){
		global $config, $Context, $Lang;
		$display = "";
		if($this->Edit){
			$display .= "\n<a href=\"#\" onclick=\"var basestring = 'detail'+new Date().getTime(); window.open('{$this->DestinationUrl}?Lang={$Context->Language}&Id=$id&ListingName={$this->ListingClassName}', basestring, 'width=800,height=600,top=250,left=320,menubar=yes,resizable=yes,left=0,top=0');return false\">
                            <img border=\"0\" src=\"{$this->IconUrl}database_edit.png\" alt=\"{$Lang[Grid][edit]}\" title=\"{$Lang[Grid][edit]}\"/>
                            </a>";

		}
		return $display;
	}
	function Delete($id){
		global $config, $Context, $Lang;
		$display = "";
		if($this->Delete){
			$display .= "<a href=\"#\" onclick=\"if(confirm('".$Lang['Grid']['confirm_detele_message']."')){window.open('{$this->DestinationUrl}?{$Context->Language}&Id=$id&ListingName={$this->ListingClassName}&storno',null, 'width=10,height=10,top=10000,left=10000,menubar=no,resizable=no,left=0,top=0');} return false\">
                            <img border=\"0\" src=\"{$this->IconUrl}database_delete.png\" alt=\"{$Lang[Grid][delete]}\" title=\"{$Lang[Grid][delete]}\"/>
                            </a>";
		}
		return $display;
	}
}

class UserEntry extends Entry{
	function Write(){
		global $config, $Context, $Lang;
		if($this->Write){
			$display = "";
		}
		return $display;
	}
	function Edit($id){
		global $config, $Context, $Lang;
		$display = "";
		if($this->Edit){
			$display .= "<a href=\"#\" onclick=\"window.open('{$this->DestinationUrl}?Lang={$Context->Language}&Id=$id&ListingName={$this->ListingClassName}', 'Detail', 'width=800,height=600,top=250,left=320,menubar=yes,resizable=yes,left=0,top=0');return false\">
                                        <img border=\"0\" src=\"{$this->IconUrl}database_edit.png\" alt=\"{$Lang[Grid][edit]}\" title=\"{$Lang[Grid][edit]}\"/>
                                        </a>";

		}
		return $display;
	}
	function Delete($id){
		global $config, $Context, $Lang;
		$display = "";
		if($this->Delete){
			$display .= "<a href=\"#\">
                            <img border=\"0\" src=\"{$this->IconUrl}database_delete.png\" alt=\"{$Lang[Grid][delete]}\" title=\"{$Lang[Grid][delete]}\"/>
                            </a>";
		}
		return $display;
	}
}
?>