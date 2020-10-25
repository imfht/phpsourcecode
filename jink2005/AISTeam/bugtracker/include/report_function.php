<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: report_function.php,v 1.37 2013/07/07 21:31:13 alex Exp $
 *
 */
/* 
Caller should include:
	include("project_function.php");
	include("customer_function.php");
	include("misc.php");
*/
include_once("../include/datetime_function.php");

function GetReportOutput($project_id, $report_id, $action)
{
	global $STRING;
	if ($action == "email") {
		$triangle_img = "";
	} else {
		$triangle_img = '<img border="0" src="'.$GLOBALS["SYS_URL_ROOT"].'/images/triangle_s.gif" width="8" height="9">';
	}

	$project_sql = "select * from ".$GLOBALS['BR_project_table']." 
		where project_id=".$GLOBALS['connection']->QMagic($project_id);

	$project_result = $GLOBALS['connection']->Execute($project_sql) or DBError(__FILE__.":".__LINE__);
	$line = $project_result->Recordcount();
	if ($line == 1) {                  
		$project_name = $project_result->fields["project_name"];
		$version_pattern = $project_result->fields["version_pattern"];
	} else {
		return "";
	}
	
	$status_array = GetStatusArray();  
	$userarray = GetAllUsers(1, 1);

	// Get the report data
	$report_sql = "select * from proj".$project_id."_report_table 
			where report_id=".$GLOBALS['connection']->QMagic($report_id);
	$report_result = $GLOBALS['connection']->Execute($report_sql) or DBError(__FILE__.":".__LINE__);
	$line = $report_result->Recordcount();
	if ($line != 1) {
		return "";
	}

	$summary = $report_result->fields["summary"];
	$reported_by = $report_result->fields["reported_by"];
	$created_date = $report_result->UserTimeStamp($report_result->fields["created_date"], GetDateTimeFormat());
    
	$assign_to = $report_result->fields["assign_to"];

	$priority = $report_result->fields["priority"];
	$status = $report_result->fields["status"];
	$statusclass = GetStatusClassByID($status_array, $status);

	$fixed_by = $report_result->fields["fixed_by"];
	if (($fixed_by != -1) && ($fixed_by != "")) {
		$fixed_date = $report_result->UserTimeStamp($report_result->fields["fixed_date"], GetDateTimeFormat());
	} else {
		$fixed_date = "";
	}
      
	$verified_by = $report_result->fields["verified_by"];
	if (($verified_by != -1) && ($verified_by != "")) {
		$verified_date = $report_result->UserTimeStamp($report_result->fields["verified_date"], GetDateTimeFormat());
	} else {
		$verified_date = "";
	}

	$version= $report_result->fields["version"];
	$fixed_in_version= $report_result->fields["fixed_in_version"];
	
	$area = trim($report_result->fields["area"]);
	$minor_area = ($report_result->fields["minor_area"]);
	if ($report_result->fields["estimated_time"] != "") {
		$estimated_time = $report_result->UserTimeStamp($report_result->fields["estimated_time"], GetDateFormat());
	} else {
		$estimated_time = "";
	}
	
	$type = $report_result->fields["type"];
	$reproducibility = $report_result->fields["reproducibility"];

	$reported_by_customer = $report_result->fields["reported_by_customer"];

	$log_sql = "select log_id,user_id,post_time,filename,description from proj".$project_id."_report_log_table 
			where report_id=".$GLOBALS['connection']->QMagic($report_id)." order by log_id ASC";
	$log_result = $GLOBALS['connection']->Execute($log_sql) or DBError(__FILE__.":".__LINE__);
	$log = "";
	while ($row = $log_result->FetchRow()) {
		$log_id = $row['log_id'];
		$username = UidTOUsername($userarray, $row['user_id']);
		$post_time = $log_result->UserTimeStamp($row['post_time'], GetDateTimeFormat());
		$filename = $row['filename'];
		$description = $row['description'];
		$log .= "<p><b>[$username]</b> reported at $post_time \n";
		if ($filename != "") {
			if ($action == "email") {
				$log .= ". upload file: ";
				$log .= $filename."\n";
			} else {
				if ($GLOBALS['SYS_FILE_IN_DB'] == 1) {
					$log .= ", upload file: <a href=\"report_download.php?project_id=".$project_id."&log_id=".$log_id."\" target=\"_blank\">";
				} else {
					$log .= ", upload file: <a href=\"upload/project".$project_id."/".$filename."\" target=\"_blank\">";
				}
				$log .= "<img border=\"0\" src=\"".$GLOBALS["SYS_URL_ROOT"]."/images/file.gif\" title=\"download\">";
				$log .="</a>\n";
			}
		}
		$log .= "</p>".$description."<hr>\n";
	}

	$return_string='
	<table align="center" border="1" style="border-collapse: collapse" width="';

	if ($action == "show_printable") {
		$tablesize = "95%";
	} else {
		$tablesize = "700";
	}

	$return_string .= $tablesize.'" cellpadding="2" cellspacing="0">
	<tr>
		<td width="100%"';
	 
	if ($action == "email") {
		$return_string .= 'bgcolor="#FFCC00"';
	} else {
		$return_string .= 'class="title"';
	}

	$return_string .= 'align="center" colspan="4"><b>';

	$return_string .= $project_name;
		
	$return_string .='</b>
		</td>
	</tr>
	<tr>
		<td width="140" class="prompt" nowrap>
			'.$triangle_img.$STRING['id'].'
		</td>
		<td width="210" class="content">'.$report_id.'
		</td>
		<td width="140" class="prompt" nowrap>
			'.$triangle_img.$STRING['type'].' 
		</td>
		<td width="210" class="content">';

	if ($action == "update") {
		$return_string .= "<select size=\"1\" name=\"type\">";
		for ($i = 1; $i < sizeof($GLOBALS['type_array']); $i++) {
			if ($i == $type) {
				$return_string .= "<option selected value=\"$i\">".$STRING[$GLOBALS['type_array'][$i]]."</option>";
			} else {
				$return_string .= "<option value=\"$i\">".$STRING[$GLOBALS['type_array'][$i]]."</option>";
			}
		}
		$return_string .= "</select>";
	} else {
		$return_string .= $STRING[$GLOBALS['type_array'][$type]];
	}
	$return_string .='</td>
	</tr>
	<tr>
		<td class="prompt" nowrap>
			'.$triangle_img.$STRING['summary'].'</td>
		<td colspan="3" class="content">';

	if ($action == "update") {
		$return_string .= '<input class="input-form-text-field" type="text" name="summary" size="78" value="'.$summary.'" maxlength="200">';
	} else {
		$return_string .= $summary;
	}

	$return_string .='</td>
	</tr>
	<tr>
		<td class="prompt" nowrap>
			'.$triangle_img.$STRING['reported_by'].'</td>
		<td class="content">';
	$return_string .= UidTOUsername($userarray, $reported_by);
	$return_string .= '</td>
		<td class="prompt" nowrap>
			'.$triangle_img.$STRING['created_date'].'</td>
		<td class="content">'.$created_date.'</td>
	</tr>
	<tr>
		<td class="prompt" nowrap>
			'.$triangle_img.$STRING['fixed_by'].'</td>
		<td class="content">';
	if ($action == "update") {
		$return_string .= '<input type="hidden" name="orig_fixed_by" value="'.$fixed_by.'">';
		$return_string .= '<select size="1" name="fixed_by">';
		$return_string .= "<option value=\"-1\"> </option>";
		for ($i=0; $i<sizeof($userarray); $i++) {
			$user_id = $userarray[$i]->getuserid();
			if ($user_id == 0) {
				continue;
			}
			if ($user_id == $fixed_by) {
				$return_string .= "<option selected value=\"".$user_id."\">".$userarray[$i]->getusername()."</option>";
			} else {
				if ($userarray[$i]->getdisabled() == 1) {
					continue;
				}
				if (CheckProjectAccessable($_GET['project_id'], $user_id) == FALSE) {
					continue;
				}
				$return_string .= "<option value=\"".$user_id."\">".$userarray[$i]->getusername()."</option>";
			}
		}
		$return_string .= '</select>';
	} else {
		if ($fixed_by != ""){
			$return_string .= UidTOUsername($userarray, $fixed_by);
		}
	}
		
	$return_string .= '</td>';
	
	if ($action != "update") {
		$return_string .= '</td>
		<td class="prompt" nowrap>
			'.$triangle_img.$STRING['fixed_date'].'</td>
		<td class="content">';
		$return_string .= $fixed_date.'</td>
		</tr>
		<tr>';
	}
		
	$return_string .= '
		<td class="prompt" nowrap>
			'.$triangle_img.$STRING['verified_by'].'</td>
		<td class="content">';

	if ($action == "update") {
		$return_string .= '<select size="1" name="verified_by">';
		$return_string .= "<option value=\"-1\"> </option>";
		for ($i=0; $i<sizeof($userarray); $i++) {
			$user_id = $userarray[$i]->getuserid();
			if ($user_id == 0) {
				continue;
			}
			if ($user_id == $verified_by) {
				$return_string .= "<option selected value=\"".$user_id."\">".$userarray[$i]->getusername()."</option>";
			} else {
				if ($userarray[$i]->getdisabled() == 1) {
					continue;
				}
				if (CheckProjectAccessable($_GET['project_id'], $user_id) == FALSE) {
					continue;
				}
				$return_string .= "<option value=\"".$user_id."\">".$userarray[$i]->getusername()."</option>";
			}
		}
		$return_string .= '</select>';
	} else {
		if ($verified_by != "") {
			$return_string .= UidTOUsername($userarray, $verified_by);
		}
	}
		
	$return_string .= '</td>';

	if ($action != "update") {
		$return_string .= '</td>
		<td class="PROMPT" nowrap>
			'.$triangle_img.$STRING['verified_date'].'</td>
		<td class="content">';
		$return_string .= $verified_date.'</td>';
	}
		
	$return_string .= '
	</tr>
	<tr>
		<td class="PROMPT" nowrap>
			'.$triangle_img.$STRING['priority'].'</td>
		<td class="content">';
	if ($action == "update") {
		$return_string .= '<select size="1" name="priority">';
		$return_string .= "<option value=\"0\">".$STRING[$GLOBALS['priority_array'][0]]."</option>";
 		for ($i = (sizeof($GLOBALS['priority_array']) - 1); $i > 0; $i--) {
			if ($i == $priority) {
				$return_string .= "<option value=\"$i\" selected>".$STRING[$GLOBALS['priority_array'][$i]]."</option>";
			} else {
				$return_string .= "<option value=\"$i\">".$STRING[$GLOBALS['priority_array'][$i]]."</option>";
			}
		}
		$return_string .= '</select>';
	} else {
		$return_string .= $STRING[$GLOBALS['priority_array'][$priority]];
	}

	$return_string .= '</td>
		<td class="prompt" nowrap>
			'.$triangle_img.$STRING['status'].'</td>
		<td class="content">';

	if ($action == "update") {
		$return_string .= '<input type="hidden" name="orig_status" value="'.$status.'">';
		$return_string .= '<select size="1" name="status">';
        //return_string .= '<select size="1" name="status" onChange="change_assign_to()">';  
		// Get allowed status for the user
		$group_status_sql = "select * from ".$GLOBALS['BR_group_allow_status_table']." where group_id=".$GLOBALS['connection']->QMagic($_SESSION[SESSION_PREFIX.'gid']);
		$group_status_result = $GLOBALS['connection']->Execute($group_status_sql) or DBError(__FILE__.":".__LINE__);
		$allow_status_array = array();
		while ($row = $group_status_result->FetchRow()) {
			$allow_status_id = $row['status_id'];
			array_push($allow_status_array, $allow_status_id);
		}
		for ($i=0; $i<sizeof($status_array); $i++) {
			$status_id = $status_array[$i]->getstatusid();
			$status_name = htmlspecialchars($status_array[$i]->getstatusname());
			// Show all status for admin
			if ($_SESSION[SESSION_PREFIX.'gid'] == 0) {
				if ($status_id == $status) {
					$return_string .= "<option value=\"$status_id\" SELECTED>$status_name</option>";
				} else {
					$return_string .= "<option value=\"$status_id\">$status_name</option>";
				}
				// for non-admin users, show the statuses they can use.
			} else {
				if (IsInArray($allow_status_array, $status_id) == -1) {
					continue;
				} else {
					if ($status_id == $status){
						$return_string .= "<option  value=\"".$status_id."\" selected>".$status_name."</option>";
					}else{
						$return_string .= "<option  value=\"".$status_id."\">".$status_name."</option>";
					}
				}
			}
		}
		$return_string .= '</select>';
	} else {
		if ($statusclass) {
			$return_string .= htmlspecialchars($statusclass->getstatusname());
		}
	}

	$return_string .= '</td>
	</tr>
	<tr>
		<td class="prompt" nowrap>
			'.$triangle_img.$STRING['assign_to'].'</td>
		<td class="content">';

	if ($action == "update") {
		$return_string .= '<input type="hidden" name="orig_assign_to" value="'.$assign_to.'">';
		$return_string .= '<select  size="1" name="assign_to">';
		$return_string .= "<option value=-1></option>";
		for ($i=0; $i<sizeof($userarray); $i++) {
			$user_id = $userarray[$i]->getuserid();
			if ($user_id == 0) {
				continue;
			}
			if ($user_id == $assign_to){
				$return_string .= "<option selected value=\"".$user_id."\">".$userarray[$i]->getusername()."</option>";
			} else {
				if ($userarray[$i]->getdisabled() == 1) {
					continue;
				}
				if (CheckProjectAccessable($_GET['project_id'], $user_id) == FALSE) {
					continue;
				}
				$return_string .= "<option value=\"".$user_id."\">".$userarray[$i]->getusername()."</option>";
			}
		}
		$return_string .= '</select>';
	} else {
		if ($assign_to != "") {
			$return_string .= UidToUsername($userarray, $assign_to);
		}		
	}
	

	$return_string .= '</td>
		<td class="prompt" nowrap>
			'.$triangle_img.$STRING['area_minor_area'].'</td>
		<td class="content">';

	if ($action == "update") {

		$all_area = "select * from ".$GLOBALS['BR_proj_area_table']." where project_id=".$GLOBALS['connection']->QMagic($project_id)." and area_parent=0 order by area_name";
		$root_area_result = $GLOBALS['connection']->Execute($all_area) or DBError(__FILE__.":".__LINE__);
		$line = $root_area_result->Recordcount();
		if ($line == 0) {
			$return_string .= '<input class="input-form-text-field" type="text" name="area" value="'.$area.'" size="10" maxlength="40">/
							<input class="input-form-text-field" type="text" name="minor_area" value="'.$minor_area.'" size="10" maxlength="40">';
		} else {

			$return_string .= "<select size=\"1\" name=\"area\" onChange=\"AreaChange()\"><option></option>";
			$selected_area="";
			while ($root_area_row = $root_area_result->FetchRow()){
				$area_name = $root_area_row["area_name"];
				if ($area_name==$area) {
					$return_string .= "<option selected>$area_name</option>";
					$selected_area = $root_area_row["area_id"];
				} else {
					$return_string .= "<option>$area_name</option>";
				}
			}
			$return_string .= "</select>/<select size=\"1\" name=\"minor_area\" onChange=\"UpdateAssignTo()\">";
           
			if ($selected_area != 0) {
				$all_minor_area = "select * from ".$GLOBALS['BR_proj_area_table']." where project_id='".$project_id."' and area_parent=".$GLOBALS['connection']->QMagic($selected_area)." order by area_name";
				$minor_area_result = $GLOBALS['connection']->Execute($all_minor_area) or DBError(__FILE__.":".__LINE__);
				$selected_area="";
				while ($minor_area_row = $minor_area_result->FetchRow()) {
					$minor_area_name = $minor_area_row["area_name"];
					if ($minor_area_name == $minor_area) {
						$return_string .= "<option selected>$minor_area_name</option>";
					} else {
						$return_string .= "<option>$minor_area_name</option>";
					}
				}
			}
           
			$return_string .= "</select>";
		}
	} else {
		$return_string .= $area.' / '.$minor_area;
	}


	$return_string .= '</td>
	</tr>
	<tr>
		<td class="prompt" nowrap>
			'.$triangle_img.$STRING['version'].'</td>
		<td class="content">';
	if ($action == "update") {
		if ((trim($version)!= "") || ($version_pattern == "") ) {
			$return_string .= '<input class="input-form-text-field" type="text" name="version" size="20" value="'.$version.'" maxlength="40">';
        } else {
			for ($i = 0; $i <= strlen($version_pattern); $i++) {
				if ($version_pattern{$i} == '%') {
					$return_string .= '<select size="1" name="version'.$i.'">';
					$return_string .= '<option value="-1"></option>';
					for ($j = 0; $j <= 9; $j++) {
						$return_string .= '<option value="'.$j.'">'.$j.'</option>';
					}
					$return_string .= '</select>';
				} else if ($version_pattern{$i} == '@') {
					$return_string .= '<select size="1" name="version'.$i.'">';
					$return_string .= '<option value="-1"></option>';
					for ($j = ord("a"); $j <= ord("z"); $j++) {
						$return_string .= '<option value="'.chr($j).'">'.chr($j).'</option>';
					}
					$return_string .= '</select>';
				} else {
					$return_string .= " <b>".$version_pattern{$i}."</b> ";
				}
			}
		}
	} else {
		$return_string .= $version;
	}
		
	$return_string .= '</td>
		<td class="prompt" nowrap>
			'.$triangle_img.$STRING['fixed_in_version'].'</td>
		<td class="content">';
	if ($action == "update") {
		if ((trim($fixed_in_version) != "") || ($version_pattern == "") ) {
			$return_string .= '<input class="input-form-text-field" type="text" name="fixed_in_version" size="20" value="'.$fixed_in_version.'" maxlength="40">';
		} else {
			for ($i = 0; $i <= strlen($version_pattern); $i++) {
				if ($version_pattern{$i} == '%') {
					$return_string .= '<select size="1" name="fixed_in_version'.$i.'">';
					$return_string .= '<option value="-1"></option>';
					for ($j = 0; $j <= 9; $j++) {
						$return_string .= '<option value="'.$j.'">'.$j.'</option>';
					}
					$return_string .= '</select>';
				} else if ($version_pattern{$i} == '@') {
					$return_string .= '<select size="1" name="fixed_in_version'.$i.'">';
					$return_string .= '<option value="-1"></option>';
					for ($j = ord("a"); $j <= ord("z"); $j++) {
						$return_string .= '<option value="'.chr($j).'">'.chr($j).'</option>';
					}
					$return_string .= '</select>';
				} else {
					$return_string .= " <b>".$version_pattern{$i}."</b> ";
				}
			}
		}
	} else {
		$return_string .= $fixed_in_version;
	}	

	$return_string .= '</td>
	</tr>
	<tr>
		<td class="prompt" nowrap>
			'.$triangle_img.$STRING['reproducibility'].'</td>
		<td class="content">';
	if ($action == "update") {
		$return_string .= '<select size="1" name="reproducibility">';
		for ($i=0; $i<sizeof($GLOBALS['reproducibility_array']); $i++) {
			if ($reproducibility == $i) {
				$return_string .= "<option value=\"$i\" selected>".$STRING[$GLOBALS['reproducibility_array'][$i]]."</option>";
			} else {
				$return_string .= "<option value=\"$i\">".$STRING[$GLOBALS['reproducibility_array'][$i]]."</option>";
			}
		}
		$return_string .= '</select>';
	} else {
		$return_string .= $STRING[$GLOBALS['reproducibility_array'][$reproducibility]];
	}

	$return_string .= '</td>
		<td class="prompt" nowrap>
			'.$triangle_img.$STRING['estimated_time'].'</td>
		<td class="content">';
	
	if ($action == "update") {
		if ($estimated_time == "") {
			$estimated_time_year = 0;
			$estimated_time_month = 0;
			$estimated_time_day = 0;
		} else {
			$estimated_time_year = substr($estimated_time, 0, 4);
			$estimated_time_month = substr($estimated_time, 5, 2);
			$estimated_time_day = substr($estimated_time, 8, 2);
		}
		$return_string .= '<select size="1" name="estimated_time_year">';
		$return_string .= "<option value=\"0\"></option>";
		for ($i = 2002; $i<=(date('Y')+5); $i++) {
			if ($i == $estimated_time_year) {
				$return_string .= "<option SELECTED>$i</option> \n";
			} else {
				$return_string .= "<option>$i</option> \n";
			}
		}
		$return_string .= '</select>/<select size="1" name="estimated_time_month">
				<option value="0"></option>';
		for ($i=1; $i<=12; $i++) {
			if ($i == $estimated_time_month) {
				$return_string .= "<option SELECTED>$i</option> \n";
			} else {
				$return_string .= "<option>$i</option> \n";
			}
		}
		$return_string .= '</select>/<select size="1" name="estimated_time_day">
						<option value="0"></option>';
		for ($i=1; $i<=31; $i++) {
			if ($i == $estimated_time_day) {
				$return_string .= "<option SELECTED>$i</option> \n";
			} else {
				$return_string .= "<option>$i</option> \n";
			}
		}
		$return_string .= '</select>';
	} else {
		$return_string .= $estimated_time;
	}
	$return_string .= '</td>
	</tr>
	<tr>
		<td class="prompt" nowrap>
			'.$triangle_img.$STRING['reported_by_customer'].'</td>
		<td class="content" colspan="3">';

	$customer_array = GetAllCustomers();
	if ($action == "update") {
		$return_string .= '<select size="1" name="reported_by_customer">
				<option value=-1> </option>';

		$visible_sql="select customer_id from ".$GLOBALS['BR_proj_customer_access_table']." where project_id='".$_GET['project_id']."'";   
		$visible_result = $GLOBALS['connection']->Execute($visible_sql) or DBError(__FILE__.":".__LINE__);
		$visible_customers = array();
		while ($row = $visible_result->FetchRow()) {
			array_push($visible_customers, $row['customer_id']);
		}
		for ($i = 0; $i<sizeof($customer_array); $i++) {
			if (-1 == IsInArray($visible_customers, $customer_array[$i]->getcustomerid())) {
				continue;
			}
			if ($customer_array[$i]->getcustomerid() == $reported_by_customer) {
				$selected = "selected";
			} else {
				$selected = "";
			}
			$return_string .= '<option value="'.$customer_array[$i]->getcustomerid().'" '.$selected.'>'.$customer_array[$i]->getcustomername().'</option>';
		}
		$return_string .= '</select>';
	} else {
		
		$return_string .= GetCustomerNameFromID($customer_array, $reported_by_customer);
	}


	$return_string .= '</td>
	</tr>
	<tr>
		<td class="prompt" nowrap>
			'.$triangle_img.$STRING['see_also'].'</td>';
	
	$return_string .= '<td colspan="3" class="content">';
	
	// 找出所有的 also see
	$other_seealso_project = "select see_also_project from proj".$project_id."_seealso_table 
			where report_id='$report_id' group by see_also_project order by see_also_project";
	$other_seealso_result = $GLOBALS['connection']->Execute($other_seealso_project) or DBError(__FILE__.":".__LINE__);

	$project_array = GetAllProjects();

	// 在上面找出表格後，再以程式為單位去找出每個程式來的 also see
	$seealso="";
	$count_seealso_project = 1;
	while($row = $other_seealso_result->FetchRow()) {
		$other_project_id = $row["see_also_project"];
		if ($action == "update") {
			// 取出所有討論區名稱
			// 讓使用者選擇要將這個 report link 到哪一個討論區
			$seealso .= "<select size=\"1\" name=\"seealso_project_id".$count_seealso_project."\"> \n";
			for ($i=0; $i < sizeof($project_array); $i++) {
				$visible_project_id = $project_array[$i]->getprojectid();
				$visible_project_name = $project_array[$i]->getprojectname();
				if ($visible_project_id == $other_project_id) {
					$seealso .= "<option value=\"$visible_project_id\" selected>$visible_project_name</option> \n";
				} else {
					if (CheckProjectAccessable($visible_project_id, $_SESSION[SESSION_PREFIX.'uid']) == TRUE) {
						$seealso .= "<option value=\"$visible_project_id\">$visible_project_name</option> \n";
					}
				}
			}// end of 列出所有的程式下拉選單
			$seealso .= "</select>";
			$seealso .= ' <input class="input-form-text-field" type="text" name="seealso'.$count_seealso_project.'" size="15" value="';
		} else {
			$projectclass = GetProjectFromID($project_array, $other_project_id);
			$other_project_name = $projectclass->getprojectname();
			$seealso .= $other_project_name.": ";
		}
		
		$get_this_seealso = "select * from proj".$project_id."_seealso_table 
			where report_id='$report_id' and see_also_project='$other_project_id' 
			order by see_also_id";
		$get_this_seealso_result = $GLOBALS['connection']->Execute($get_this_seealso) or DBError(__FILE__.":".__LINE__);
		while ($final_see_id_row = $get_this_seealso_result->FetchRow()) {
			$final_see_id = $final_see_id_row["see_also_id"];
			if ($action == "update") {
				$seealso .= $final_see_id.", ";
			} else {
				$seealso .= "<a href=report_show.php?project_id=$other_project_id&report_id=$final_see_id>$final_see_id</a>, ";
			}
		}
		if ($action == "update") {
			$seealso .= "\"> \n";
			$count_seealso_project++;
		}
		$seealso.= "<br>";
	}

	$return_string .= $seealso;

	if ($action == "update") {
		$return_string .= "<input type=hidden name=count_seealso_project value=$count_seealso_project> \n";
		$return_string .= '<select size="1" name="seealso_project_id'.$count_seealso_project.'">';

		for ($i=0; $i < sizeof($project_array); $i++) {
			$visible_project_id = $project_array[$i]->getprojectid();
			if (CheckProjectAccessable($visible_project_id, $_SESSION[SESSION_PREFIX.'uid']) == TRUE) {
				$visible_project_name = $project_array[$i]->getprojectname();
				if ($visible_project_id == $project_id) {
					$return_string .= "<option value=\"$visible_project_id\" selected>$visible_project_name</option> \n";
				} else {
					$return_string .= "<option value=\"$visible_project_id\">$visible_project_name</option> \n";
				}
			}
		}
		$return_string .= "</select> \n";
 
		$return_string .= '<input class="input-form-text-field" type="text" name="seealso'.$count_seealso_project.'" size="15">';
		$return_string .= '&nbsp;'.PrintTip($STRING['hint_title'], $STRING['see_also_hint'], "return");
	}
		
	$return_string .= '</td>';

	if ($action == "update") {
		$return_string .= '
		<tr><td class="prompt" nowrap>
				'.$triangle_img.$STRING['file_upload'].'
			</td>
			<td class="content" colspan="3">
				<input class="input-form-text-field" type="file" name="file" size="55">';
		$return_string .= '&nbsp;'.PrintTip($STRING['hint_title'], $STRING['file_upload_hint'].' '.get_cfg_var("upload_max_filesize"), "return");
	}

	$return_string .= '
		</td>
	</tr>
	<tr>
		<td class="prompt prompt_align_top" nowrap>
			'.$triangle_img.$STRING['logs'].'</td>
		<td class="content" colspan="3">'.$log.'</td>
	</tr>';

	if ($action == "update") {
		$return_string .= '<tr>
		<td class="prompt prompt_align_top" nowrap>
			'.$triangle_img.$STRING['description'].'</td>
			<td class="content" colspan="3">
			<textarea rows="18" name="description" style="width:100%">'.$_SESSION[SESSION_PREFIX.'back_array']['description'].'</textarea></td>
		</tr>';
	}
		
	$return_string .= '
	</table>';
	
	return $return_string;
}

function GetPrintableOutput($project_id, $report_id)
{
	return GetReportOutput($project_id, $report_id, "show_printable");
}
?>
