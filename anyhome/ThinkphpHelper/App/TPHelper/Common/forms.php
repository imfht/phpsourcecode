<?php
function compileForms($f = '',$def = ''){
	if (!$f) return;
	if ($f['type'] == 'input') {
		return ipt($f['lable'],$f['fname'],$def,$f['placeholder']);
	}
}



function formGroup($label = '',$ipt = '',$labSpan = 2)
{
	$html = '<div class="form-group "><label class="control-label col-lg-2 ">'.$label.'</label><div class="col-lg-10">'.$ipt.'</div></div>';
	return $html;
}
//required

function iptValid($label = '',$fname ='',$fvalue = '',$placeholder = '',$labSpan = 2)
{

	$html = "<input type='text'  value='".$fvalue."' name='".$fname."' class='form-control required' placeholder='".$placeholder."'/>";
	return formGroup($label,$html);
}

function ipt($label = '',$fname ='',$fvalue = '',$placeholder = '',$labSpan = 2)
{

	$html = "<input type='text'  value='".$fvalue."' name='".$fname."' class='form-control' placeholder='".$placeholder."'/>";
	return formGroup($label,$html);
}

function ckb($label = '',$fname ='',$flbs = '',$fvs = '',$fvalue = '',$labSpan = 2,$iptSpan = 2){
	$flbs_arr = explode(',', $flbs);
	$fvs_arr = explode(',', $fvs);
	for ($i=0; $i <count($flbs_arr) ; $i++) { 
		$html .='<label class="checkbox-inline"><input name="'.$fname.'[]" type="checkbox" value="'.$fvs_arr[$i].'" />'.$flbs_arr[$i].'</label>';
	}
	return formGroup($label,$html);
}

function passwd($label = '',$fname ='',$fvalue = '',$placeholder = '',$labSpan = 2,$iptSpan = 2)
{

	$html = "<input type='password'  value='".$fvalue."' name='".$fname."' class='form-control' placeholder='".$placeholder."'/>";
	return formGroup($label,$html);
}

function textarea($label = '',$fname ='',$fvalue = '',$placeholder = '',$labSpan = 2,$iptSpan = 2)
{
	$html = "<textarea name='".$fname."' rows='2' class='form-control'>".$fvalue."</textarea>";
	return formGroup($label,$html);
}
function select($label = '',$fname ='',$opt = array(),$val = '',$placeholder = '',$labSpan = 2,$iptSpan = 3)
{
	$opt_arr = explode(",", $opt);
	$val_arr = explode(",", $val);
	$html = "<select name='".$fname."' class='".$iptSpan." form-control'>"; 
	for ($i=0; $i < count($opt_arr); $i++) { 
		$html .= "<option value='".$val_arr[$i]."'>".$opt_arr[$i]."</option>";
	}
	$html .= "</select>";
	return formGroup($label,$html);
}




?>