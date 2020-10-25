<?php
/* DB Administration, handling all db transations across the site.
 * v0.1
 * wadelau@gmail.com
 * since Wed Jul 13 18:22:06 UTC 2011
 * Thu Sep 11 16:34:20 CST 2014
 */

require(__ROOT__."/inc/conn.class.php");
include(__ROOT__."/inc/mysql.class.php");
include(__ROOT__."/inc/mysqlix.class.php");
include(__ROOT__."/inc/pdox.class.php");
include(__ROOT__."/inc/sqlserver.class.php");
include(__ROOT__."/inc/oracle.class.php");

class DBA {
	
	var $conf = null; 
	var $dbconn = null;
	var $sql_operator_list = array(); # first chars of ansi sql operators
	var $Sql_Sep_List_Right = array();

	//-construct
	function __construct($dbconf=null){
		$dbconf = ($dbconf==null ? 'Config_Master' : $dbconf);
		//-
		$this->conf = new $dbconf; //- need to be changed to Config_zoneli when sync to product enviro.
		#$this->dbconn = new MySQLDB($this->conf);
		$dbDriver = GConf::get('dbdriver');
		$this->dbconn = new $dbDriver($this->conf);
		$this->sql_operator_list = array(' '=>1,'^'=>1,'~'=>1,':'=>1,'!'=>1,'/'=>1,
				'*'=>1,'&'=>1,'%'=>1,'+'=>1,'='=>1,'|'=>1,
				'>'=>1,'<'=>1,'-'=>1,'('=>1,')'=>1,','=>1);
		$this->Sql_Sep_List_Right = array(" "=>1, ")"=>1, ","=>1);
	}
	
	//- descontruct
	function __destruct(){
		$this->close();
		$this->dbconn = null;
	}

	/* 
	 * mandatory return $hm = (0 => true|false, 1 => string|array);
	 * Thu Jul 21 11:31:47 UTC 2011, wadelau@gmail.com
	 */
	function update($sql, $hmvars){
		$hm = array();
		$idxarr = $this->hm2idxArray($sql,$hmvars);
        #print_r($idxarr);
		$tmphm = $this->dbconn->query($sql,$hmvars,$idxarr);
		if($tmphm[0]){
			$hm[0] = true;
			$hm[1] = array('insertid'=>$this->dbconn->getInsertId(),'affectedrows'=>$this->dbconn->getAffectedRows());
		}
		else{
			$hm[0] = false;
			$hm[1] = $tmphm[1];
		}
		//error_log("<br/>/inc/dba.class.php: update sql:[".$sql."] result:[".$hm['insertid']."]");
		return $hm;
	}

	/* 
	 * mandatory return $hm = (0 => true|false, 1 => string|array);
	 */
	function select($sql, $hmvars){
		$hm = array();
		$result = 0;
		$idxarr = $this->hm2idxArray($sql,$hmvars);
    	#print_r($idxarr);
		$haslimit1 = 0;
		if((array_key_exists('pagesize',$hmvars) && $hmvars['pagesize'] == 1) || strpos($sql,"limit 1 ") != false){
			$result = $this->dbconn->readSingle($sql, $hmvars,$idxarr); # why need this? @todo need to be removed.
			$haslimit1 = 1;
		}
		else{
			$result = $this->dbconn->readBatch($sql, $hmvars,$idxarr);
		}
		if($result[0]){
			$hm[0] = true;
			$hm[1] = $result[1]; # single-dimension or double-dimension array
		}
		else{
			$hm[0] = false;
			$hm[1] = $result[1];
		}
    	#debug(__FILE__.": vars in dba::select: ".debug($hmvars));
		#error_log(__FILE__.": select sql:[".$sql."] result:[".$hm[0]."]");
		return $hm;
	}

	# added on Sun Jul 17 22:19:15 UTC 2011 by wadelau@gmail.com
	# + paramPosArr, 11:18 Thursday, April 30, 2020
	# sort the parameter in order
	# return sorted array 
	function hm2idxArray($sql, $hmvars){
		#$sql = "select * from order_maintbl where id in (88,87,84,83,79) and (istate=? or istate=?) order by id desc";
		#$hmvars['istate'] = 2;
		#$hmvars['istate.2'] = 3;
		#$hmvars['id'] = 86;
		$idxarr = array();
		$tmparr = array();
		$tmpposarr = array();
        $wherepos = strpos($sql, " where ");
        $selectpos = strpos($sql, "select ");
		$sqloplist = $this->sql_operator_list;
        #print_r($hmvars);
		//- compute position of each field
		if(is_array($hmvars)){
			foreach($hmvars as $k => $v){
		        if($k == ''){
		            #debug(__FILE__.": found n/a k:[$k], skip.");
		            continue;
		        }
		        $keyLen = strlen($k);
		        $keyPos = strpos($sql, $k);
		        if($keyPos !== false){
		            while($keyPos !== false){
        		        $preK = substr($sql, $keyPos-1, 1);
        		        $aftK = substr($sql, $keyPos+$keyLen, 1);
        		        #debug(__FILE__.": sql:[$sql] k:[$k] pos:[$keyPos] prek:[$preK] aftk:[$aftK]");
        		        if(isset($sqloplist[$preK]) && isset($sqloplist[$aftK])){
        		            if($selectpos !== false){
        		                if($keyPos > $wherepos){
        		                    $tmparr[$keyPos] = $k;
        		                }
        		                else{
        		                    # select fields
        		                }
        		            }
        		            else{
        		                $tmparr[$keyPos] = $k;
        		            }
        		        }
        		        else{
        		            #debug(__FILE__.": found illegal key preset. k:[$k] pos:[$keyPos] 
        		            #        prek:[$preK] aftk:[$aftK]");
        		        }
        		        $keyPos = strpos($sql, $k, $keyPos+$keyLen);
		            }
		        }
		        else{
		            #debug(__FILE__.": no such key:[$k] in sql:[$sql]");
		        }
		    }
		}
		else{
			error_log(__FILE__.": illegal array found with hmvars.");				
		}
		$sqlLen = strlen($sql);
		$tmpi = 0; $tmpidx = -1; 
		//- figure out each param mark, i.e., "?"
		$paramPosArr = array(); $minParamPos = $sqlLen - 1;
		$sqlSepListR = $this->Sql_Sep_List_Right;
		foreach($sqlSepListR as $sqlSepR=>$tmpv){
			$tmpidx = strpos($sql, '?'.$sqlSepR);	
			while($tmpidx > 0){
				$paramPosArr[$tmpi] = $tmpidx;
				if($tmpidx < $minParamPos){ $minParamPos = $tmpidx; }
				$tmpidx = strpos($sql, '?'.$sqlSepR, $tmpidx+1);		
				$tmpi++;
			}
			#debug("inc/Dba: sep:".$sqlSepR." tmpi:$tmpi paramPosArr:".serialize($paramPosArr));
		}
		if(substr($sql, -1) == '?'){
			$paramPosArr[$tmpi] = $sqlLen - 1;	
		}
		#debug("inc/Dba: paramPosArr:".serialize($paramPosArr));
		//- sort each field by its pos
		$keyPosArr = array(); $keyPosI = 0;
		$kSerial = array(); $tmpi = 0; $maxKeyPos = 0;
		for($i=0;$i<$sqlLen;$i++){
		    if(array_key_exists($i, $tmparr)){
		        $k = $tmparr[$i];
		        $ki = isset($kSerial[$k]) ? $kSerial[$k] : ''; 
		        $idxarr[$tmpi] = $tmparr[$i].($ki=='' ? '' : '.'.($ki+1));
		        $tmpi++;
		        $kSerial[$k]++;
				$keyPosArr[$keyPosI++] = $i;
				if($i > $maxKeyPos){ $maxKeyPos = $i; }
		    }
		    else{
		        # @todo;
		    }
		}
		#debug("inc/Dba: keyPosArr:".serialize($keyPosArr));
		//- match each param mark with its field
		$hasMark = false; $lastKeyPos = -1; $nextKeyPos = -1;
		if($minParamPos < $maxKeyPos){
			$idxArr2 = array(); $idxArrI = 0;
			$keyPos = 0; $paramPos = 0;
			foreach($keyPosArr as $kpos=>$vpos){
				$keyPos = $vpos;
				if($keyPos > 0){
					$nextKeyPos = $keyPosArr[$kpos+1];
					#debug("inc/Dba: kpos:$kpos keyPos:$keyPos nextKeyPos:$nextKeyPos");
					$hasMark = false;
					foreach($paramPosArr as $kppos=>$vppos){
						$paramPos = $vppos;
						if($paramPos > $keyPos){
							if($nextKeyPos > 0){
								if($paramPos < $nextKeyPos){
									$hasMark = true;
								}
							}
							else{
								$hasMark = true;	
							}
						}
						#debug("\tinc/Dba: kppos:$kpos paramPos:$paramPos hasMark:$hasMark");
						if($hasMark){
							$idxArr2[$idxArrI++] = $idxarr[$kpos];
							break;
						}
					}
				}
			}
			//- override
			$idxarr = $idxArr2; $idxArr2 = null;
		}
		else{
			//- bypass: (a, b, c) ...(?, ?, ?)
			//- bypass: single param: a=?
		}
		#debug("inc/Dba: idxArr:".serialize($idxarr)." sql:".$sql." hmvars:".serialize($hmvars));
		return $idxarr;
	}

	//-
	function showDb(){
		$this->dbconn->showConf();
	}

	//-
	function close(){
	    # @todo, long conn?
        if($this->dbConn){
            $this->dbconn->close();
        }
	    return true;
	}
}

?>
