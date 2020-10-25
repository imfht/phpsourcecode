<?php
/* Copyright (c) 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: feedback_report_function.php,v 1.21 2013/07/07 21:31:13 alex Exp $
 *
 */
include_once("datetime_function.php");

function GetFeedbackReportOutput($project_id, $report_id, $action)
{
	global $STRING;

	if ($action == "email") {
		$triangle_img = "";
	} else {
		$triangle_img = '<img border="0" src="'.$GLOBALS["SYS_URL_ROOT"].'/images/triangle_s.gif" width="8" height="9">';
	}

	$userarray = GetAllUsers(1, 1);
	$project_sql = "select * from ".$GLOBALS['BR_project_table']." 
			where project_id=".$GLOBALS['connection']->QMagic($project_id);

	$project_result = $GLOBALS['connection']->Execute($project_sql) or DBError(__FILE__.":".__LINE__);
	$line = $project_result->Recordcount();
	if ($line == 1) {                  
		$project_name = $project_result->fields["project_name"];
	} else {
		return "";
	}

	if ($action != "new") {
		// Get the report data
		$report_sql = "select * from proj".$project_id."_feedback_table 
				where report_id=".$GLOBALS['connection']->QMagic($report_id);
		$report_result = $GLOBALS['connection']->Execute($report_sql) or DBError(__FILE__.":".__LINE__);
		$line = $report_result->Recordcount();
		if ($line != 1) {
			return "";
		}

		$summary = $report_result->fields["summary"];
		$cust_report_id = $report_result->fields["cust_report_id"];
		$created_by = $report_result->fields["created_by"];
		$customer_id = $report_result->fields["customer_id"];
		$created_date = $report_result->fields["created_date"];
		$created_date = $report_result->UserTimeStamp($created_date, GetDateTimeFormat());
		$type = $report_result->fields["type"];
		$priority = $report_result->fields["priority"];
		$status = $report_result->fields["status"];
		$version = $report_result->fields["version"];
		$fixed_in_version = $report_result->fields["fixed_in_version"];
		$reproducibility = $report_result->fields["reproducibility"];

		$log_sql = "select * from proj".$project_id."_feedback_content_table 
			where report_id=".$report_id." order by content_id ASC";
		$log_result = $GLOBALS['connection']->Execute($log_sql) or DBError(__FILE__.":".__LINE__);
		$log = "";
		while ($row = $log_result->FetchRow()) {
			$content_id = $row['content_id'];
			$customer_email = $row['customer_email'];
			$internal_user_id = $row['internal_user_id'];
			$internal_username = UidTOUsername($userarray, $row['internal_user_id']);
			$post_time = $log_result->UserTimeStamp($row['post_time'], GetDateTimeFormat());
			$filename = $row['filename'];
			$description = $row['description'];

			if ($customer_email != "") {
				$name_sql = "select realname from ".$GLOBALS['BR_customer_user_table']."
						where email=".$GLOBALS['connection']->QMagic($customer_email);
				$name_result = $GLOBALS['connection']->Execute($name_sql) or DBError(__FILE__.":".__LINE__);
				if ($name_result->Recordcount() > 0) {
					$realname = $name_result->fields["realname"];
				} else {
					$realname = "";
				}
				if ($realname != "") {
					$log .= "<p><b>[$realname ($customer_email)]</b> reported at $post_time";
				} else {
					$log .= "<p><b>[$customer_email]</b> reported at $post_time";
				}
			} else {
				$log .= "<p><b>[$internal_username]</b> reported at $post_time";
			}
			
			if ($filename != "") {
				if ($action == "email") {
					$log .= ". upload file: ";
					$log .= $filename;
				} else {
					$log .= ", upload file: <a href=\"feedback_report_download.php?project_id=".$project_id."&content_id=".$content_id."\" target=\"_blank\">";
					$log .= "<img border=\"0\" src=\"".$GLOBALS["SYS_URL_ROOT"]."/images/file.gif\" title=\"download\">";
					$log .="</a>";
				}
			}
			$log .= "</p>".$description."<hr>";
			
		}

		$get_map_sql = "select * from proj".$project_id."_feedback_map_table where
					feedback_report_id=".$report_id;
		$map_result = $GLOBALS['connection']->Execute($get_map_sql) or DBError(__FILE__.":".__LINE__);
		$line = $map_result->Recordcount();
		
		if ($line > 0) {
			$internal_report_id = $map_result->fields["internal_report_id"];
			$imported_report = '<a href="../report/report_show.php?project_id='.$project_id.'&report_id='.$internal_report_id.'" target="_blank"> '.$internal_report_id.' </a>';
		} else {
			$imported_report = "";
		}
	}

	if (($action == "new") || ($action == "update")) {
		$ValueArray = $_SESSION[SESSION_PREFIX.'back_array'];
		if ($action == "new") {
			$summary = $ValueArray['summary'];
			$type = $ValueArray['type'];
			$priority = $ValueArray['priority'];
			$status = $ValueArray['status'];
			$version = $ValueArray['version'];
			$reproducibility = $ValueArray['reproducibility'];
		}
		$description = $ValueArray['description'];
		
		$summary = '<input class="input-form-text-field" type="text" name="summary" value="'.$summary.'" size="78" maxlength="200">';

		$tmp_type = '<select name="type">';
		for ($i = 0; $i<sizeof($GLOBALS['type_array']); $i++) {
			if ($type == $i) {
				$tmp_type .= '<option value="'.$i.'" selected>'.$STRING[$GLOBALS['type_array'][$i]].'</option>';
			} else {
				$tmp_type .= '<option value="'.$i.'">'.$STRING[$GLOBALS['type_array'][$i]].'</option>';
			}
		}
		$type = $tmp_type.'</select>';

		$tmp_status = '<select name="status">';
		for ($i = 0; $i<sizeof($GLOBALS['feedback_status']); $i++) {
			if ($status == $i) {
				$tmp_status .= '<option value="'.$i.'" selected>'.$GLOBALS['feedback_status'][$i].'</option>';
			} else {
				$tmp_status .= '<option value="'.$i.'">'.$GLOBALS['feedback_status'][$i].'</option>';
			}
		}
		$status = $tmp_status.'</select>';

		$tmp_priority = '<select name="priority">';
		$tmp_priority .= '<option value="0">'.$STRING[$GLOBALS['priority_array'][0]].'</option>';
		for ($i = (sizeof($GLOBALS['priority_array']) - 1); $i > 0; $i--) {
			if ($priority == $i) {
				$tmp_priority .= '<option value="'.$i.'" selected>'.$STRING[$GLOBALS['priority_array'][$i]].'</option>';
			} else {
				$tmp_priority .= '<option value="'.$i.'">'.$STRING[$GLOBALS['priority_array'][$i]].'</option>';
			}
		}
		$priority = $tmp_priority.'</select>';

		$tmp_reproducibility = '<select name="reproducibility">';
		for ($i = 0; $i<sizeof($GLOBALS['reproducibility_array']); $i++) {
			if ($reproducibility == $i) {
				$tmp_reproducibility .= '<option value="'.$i.'" selected>'.$STRING[$GLOBALS['reproducibility_array'][$i]].'</option>';
			} else {
				$tmp_reproducibility .= '<option value="'.$i.'">'.$STRING[$GLOBALS['reproducibility_array'][$i]].'</option>';
			}
		}
		$reproducibility = $tmp_reproducibility.'</select>';

		$version = '<input class="input-form-text-field" type="text" name="version" value="'.$version.'" size="20" maxlength="40">';
		$description = '<textarea rows="16" name="description" style="width:100%" cols="80">'.$description.'</textarea>';
	} else {
		$type = $STRING[$GLOBALS['type_array'][$type]];
		$priority = $STRING[$GLOBALS['priority_array'][$priority]];
		$reproducibility = $STRING[$GLOBALS['reproducibility_array'][$reproducibility]];
		$status = $GLOBALS['feedback_status'][$status];
	}
	$return_string = "";

	$return_string .= '
	<table align="center" border="1" style="border-collapse: collapse" width="700" cellpadding="2" cellspacing="0">
		<tr>
			<td class="title" colspan="4" align="center">'.$project_name.'
				<input type="hidden" name="project_id" value="'.$project_id.'">
			</td>
		</tr>
		<tr>
			<td width="160" class="prompt">
				'.$triangle_img.$STRING['summary'].'
			</td>
			<td width="540" class="content" colspan="3">'.$summary.'</td>
		</tr>';
	if ($action != "new") {
		$return_string .= '
			<tr>
			<td width="160" class="prompt">
				'.$triangle_img.$STRING['created_by'].'
			</td>
			<td width="190" class="content">'.$created_by.'
			</td>
			<td class="prompt" width="160">
				'.$triangle_img.$STRING['created_date'].'
			</td>
			<td width="190" class="content">'.$created_date.'
			</td>
		</tr>';
	}
	$return_string .= '
		<tr>
			<td width="160" class="prompt">
				'.$triangle_img.$STRING['type'].'
			</td>
			<td width="190" class="content">'.$type.'
			</td>
			<td class="prompt" width="160">
				'.$triangle_img.$STRING['version'].'
			</td>
			<td width="190" class="content">'.$version.'
			</td>
		</tr>
		<tr>
			<td class="prompt">
				'.$triangle_img.$STRING['priority'].'
			</td>
			<td class="content">'.$priority.'
			</td>';
	if ($action == "new") {
		$return_string .= '
			<td class="prompt">
				'.$triangle_img.$STRING['reproducibility'].'
			</td>
			<td class="content" colspan="3">'.$reproducibility.'
			</td>
		</tr>';
	} else {
		$customer_array = GetAllCustomers();
		$return_string .= '
			<td class="prompt">
				'.$triangle_img.$STRING['status'].'
			</td>
			<td class="content">'.$status.'
			</td>
		</tr>
		<tr>
			<td class="prompt">
				'.$triangle_img.$STRING['reproducibility'].'
			</td>
			<td class="content">'.$reproducibility.'
			</td>
			<td class="prompt">
				'.$triangle_img.$STRING['reported_by_customer'].'
			</td>
			<td class="content">'.GetCustomerNameFromID($customer_array, $customer_id).'
			</td>
		</tr>';
	}
	if (($action == "new") || ($action == "update")) {
		$return_string .= '
		<tr>
			<td class="prompt">
				'.$triangle_img.$STRING['file_upload'].'
			</td>
			<td colspan="3" class="content">
				<input class="input-form-text-field" type="file" name="file" size="55" class="button">
				'.PrintTip($STRING['hint_title'], $STRING['file_upload_hint'].get_cfg_var("upload_max_filesize"), "return").'
			</td>
		</tr>';
	}
	if (($action != "email") && ($imported_report != "")) {
		$return_string .= '<tr>
			<td class="prompt" width="140">
				'.$triangle_img.$STRING['imported_report'].'
			</td>
			<td colspan="3" class="content">'.$imported_report.'
			</td>
		</tr>';
	}
	if ($action != "new") {
		$return_string .= '
		<tr>
			<td class="prompt prompt_align_top">
				'.$triangle_img.$STRING['logs'].'
			</td>
			<td colspan="3" class="content">'.$log.'
			</td>
		</tr>';
	}
	
	if (($action == "new") || ($action == "update")) {
		$return_string .= '
		<tr>
			<td class="prompt prompt_align_top">
				'.$triangle_img.$STRING['description'].
			PrintTip($STRING['hint_title'], $STRING['description_hint'], "return").
			'
			</td>
			<td colspan="3" class="content">
				'.$description.'
			</td>
		</tr>';
	}
	
	$return_string .= '
	</table>';

	return $return_string;
}
?>
