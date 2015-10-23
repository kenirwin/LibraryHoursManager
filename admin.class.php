<?
class HoursAdmin {
    private $js_includes = <<<EOT
	<!--[if lte IE 8]><script language="javascript" type="text/javascript" src="./lib/scripts/flot/excanvas.min.js"></script><![endif]-->
	<script language="javascript" type="text/javascript" src="./lib/scripts/flot/jquery.flot.js"></script>
	<script language="javascript" type="text/javascript" src="./lib/scripts/flot/jquery.flot.time.js"></script>
EOT;

    /* Display functions */
    
    public function TimeframePicker ($json) {
        $timeframes = json_decode($json);
        $table  = '<div id="timeframe-picker">'.PHP_EOL;
        $table .= '<div id="new-timeframe-button" class="button">New Timeframe</div>'.PHP_EOL;
        $table .= '<table id="timeframe-picker-table">'.PHP_EOL;
        foreach($timeframes as $t) {
            $table .= '<tr data-timeframe-id="'.$t->id.'" data-preset-id="'.$t->apply_preset_id.'"><td>'.$t->name.'</td><td>'.$t->first_date.'</td><td>'.$t->last_date.'</td><td>'.$t->rank.'</td><td><a href="timeframes.php?action[]=delete_timeframe&timeframe_id='.$t->id.'" class="button delete-button delete-timeframe-button">x</a></td></tr>'.PHP_EOL;
        }
        $table .='</table>'.PHP_EOL;
        $table .='</div>'.PHP_EOL;
        print $table;
    }

    public function EditTimeframeDetails ($json,$hours,$id) {
        if (isset($json)) { $details = json_decode($json); }
        //        print '<pre>';print_r($details); print '</pre>'; print '<hr>'.PHP_EOL;
        $table  = '<div id="edit-timeframe">'.PHP_EOL;
        $table .= '<h2>Timeframe Details</h2>'.PHP_EOL;
        $table .= '<form id="presets-editor" action="timeframes.php">'.PHP_EOL;
        $table .= $this->FormRow('preset_id', $details[0]->preset_id, 'hidden');
        $table .= $this->FormRow('timeframe_id', $details[0]->timeframe_id, 'hidden');
        $table .= $this->FormRow('name', $details[0]->name, 'text');
        $table .= $this->FormRow('first_date', $details[0]->first_date, 'text');
        $table .= $this->FormRow('last_date', $details[0]->last_date, 'text');
        //        $table .= $this->RankPulldown($details[0]->rank);
        $table .= $this->SettingsSelectorPulldown($hours, $id);
        $table .= $this->FormRow('action[]','submit_timeframe_details','hidden');
        $table .= '<div id="settings-placeholder"></div>'.PHP_EOL;
        $table .= '<input type="submit">'.PHP_EOL;
        $table .= '</form>'.PHP_EOL;
        return $table;
    }

    public function ShowPresetDetails($details,$id,$display_action="show") {
        $details = json_decode($details);
        if ($display_action == "show") {
            $edit_button = '<div class="button" id="edit-settings-button" data-preset-id="'.$details[0]->preset_id.'">Edit</a></div>';
        }
        else { 
            $edit_button = '';
        }
        if ($display_action=="show") {
            $table .= '<h2>Settings</h2>'.PHP_EOL;
            $table .= $edit_button.'<br/>'.PHP_EOL;
        }
        $table .= '<div id="show-or-edit-settings">'.PHP_EOL;
        if ($display_action == "edit") {
            //            print '<pre>'; print_r ($details); print '</pre>';
            $table .= $this->FormRow('action[]','submit_settings_details','hidden');
            $table .= $this->FormRow('preset_id',$id,'hidden');
            $table .= $this->FormRow('preset_name',$details[0]->name, 'text');
            $table .= 'rank: <select name="rank">'.PHP_EOL;
            $table .= ' <option>Select Rank</option>'.PHP_EOL;
            if ($details[0]->rank == 1) {$rankselect1 = ' selected';}
            elseif($details[0]->rank == 2) {$rankselect2 = ' selected'; }
            $table .= ' <option value="1"'.$rankselect1.'>1 - General Time Period</option>'.PHP_EOL;
            $table .= ' <option value="2"'.$rankselect2.'>2 - Special Time Period</option>'.PHP_EOL;
            $table .= '</select><br />'.PHP_EOL;
        }
        else { 
            $table .= "preset_name: ".$details[0]->name.'<br />';
        }
        $table .= $this->ShowDays($details, $display_action);
        if (isset($details[0]->preset_id)) {
            $table .= '<input type="hidden" name="action[]" value="submit_preset_values">'.PHP_EOL;
        }
        else { 
            $table .= '<input type="hidden" name="action[]" value="submit_new_preset">'.PHP_EOL;
        }
        $table .= '</div>'; //show-or-edit-settings
        return $table;
    }

    private function SettingsSelectorPulldown($hours,$id='') {
        $presets = json_decode($hours->GetJSON('presets'));
        $pulldown  = 'use preset settings: <select name="use_preset">'.PHP_EOL;
        $pulldown .= ' <option>Select one</option>'.PHP_EOL;
        $pulldown .= ' <option value="new">Create New Preset</option>'.PHP_EOL;
        foreach ($presets as $p) {
            //            print $p->id . ' ==? ' .$id.'<br>'.PHP_EOL;
            if ($p->rank == 1) { $rank_desc = "General Time Period"; }
            elseif($p->rank==2) { $rank_desc = "Special Time Period"; }
            if ($p->id == $id) { $selected = ' selected'; }
            else { $selected = ''; }
            $pulldown .= ' <option value="'.$p->id.'"'.$selected.'>'.$p->name.' (Rank: '.$p->rank.' - '.$rank_desc.')</option>'.PHP_EOL;
        }
        $pulldown .= '</select><br />'.PHP_EOL;
        return $pulldown;
    }

    private function RankPulldown($rank='') {
        $rankselect = array();
        $rankselect[$rank] = ' selected';
        $pulldown  = 'rank: <select name="rank">'.PHP_EOL;
        $pulldown .= ' <option>Select one</option>'.PHP_EOL;
        $pulldown .= ' <option value="1"'.$rankselect[1].'>1 - General Time Period</option>'.PHP_EOL;
        $pulldown .= ' <option value="2"'.$rankselect[2].'>2 - Special Time Period</option>'.PHP_EOL;
        $pulldown .= '</select>'.PHP_EOL;
        return $pulldown;
    }

    private function FormRow($key, $value, $type) {
        switch ($type) {
        case ('text'):
            return $key.': <input type="'.$type.'" name="'.$key.'" value="'.$value.'" size="50"><br />'.PHP_EOL;
            break;
        case ('hidden'):
            return '<input type="'.$type.'" name="'.$key.'" value="'.$value.'" size="50">'.PHP_EOL;
        }
    }
    
    private function ShowDays($arr,$display_action) {
        $days = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
        $table  = '<table>'.PHP_EOL;
        $table .= '<tr><td>Day</td><td>Open Time</td></td><td>Close Time</td><td>Open Past Midnight</td><td>Closed</td></tr>'.PHP_EOL;
        foreach ($days as $day) {
            $table .= '<tr><td>'.$day.'</td>';
            $table .= $this->FindDayValues($day, $arr, $display_action);
            $table .= '</tr>'.PHP_EOL;
            $table .= '</tr>';
        }
        $table .= '</table>'.PHP_EOL;
        return $table;
    }

    private function Checkbox($status="unchecked") {
        if ($status == "checked") {
            return '&#10004;';
        }
        else {
            return '&square;';
        }
    }

    private function FindDayValues($day, $arr, $display_action) {
        foreach ($arr as $day_settings) {
            if ($day_settings->day == $day) {
                if ($day_settings->closed == 'Y') {
                    if ($display_action == "edit") {
                        $closed = '<input type="checkbox" name="closed['.$day_settings->day.']" checked />'.PHP_EOL;
                    }
                    else {
                        $closed = $this->Checkbox("checked");
                    }
                }
                else {
                    if ($display_action == "edit") {
                        $closed = '<input type="checkbox" name="closed['.$day_settings->day.']"/>'.PHP_EOL;
                    }
                    else {
                        $closed = $this->Checkbox();
                    }
                }

                if ($day_settings->latenight == 'Y') {
                    if ($display_action == "edit") {
                        $open_late = '<input type="checkbox" name="latenight['.$day_settings->day.']" checked />'.PHP_EOL;
                    }
                    else {
                        $open_late = $this->Checkbox("checked");
                    }
                }
                else {
                    if ($display_action == "edit") {
                        $open_late = '<input type="checkbox" name="latenight['.$day_settings->day.']"/>'.PHP_EOL;
                    }
                    else { 
                        $open_late = $this->Checkbox();
                    }
                }

                $hidden_settings_key = '<input type="hidden" name="settings_key['.$day.']" value="'.$day_settings->settings_key.'" />';

                if ($display_action == "edit") {
                    return '<td>'.$hidden_settings_key.'<input type="text" name="opentime['.$day.']" value="'.$day_settings->opentime.'"></td><td><input type="text" name="closetime['.$day.']" value="'.$day_settings->closetime.'"></td><td>'.$open_late.'</td><td>'.$closed.'</td>'.PHP_EOL;
                }
                else {
                    return '<td>'.$day_settings->opentime.'</td><td>'.$day_settings->closetime.'</td><td>'.$open_late.'</td><td>'.$closed.'</td>'.PHP_EOL;
                }
            }
        }
        // if day settings not found
        if ($display_action == "edit") {
            $closed = '<input type="checkbox" name="closed['.$day.']"/>'.PHP_EOL;
            $open_late = '<input type="checkbox" name="latenight['.$day.']"/>'.PHP_EOL;
            return '<td><input type="text" name="opentime['.$day.']" value=""></td><td><input type="text" name="closetime['.$day.']" value=""></td><td>'.$open_late.'</td><td>'.$closed.'</td>'.PHP_EOL;
        }
        else { 
            return '<td></td><td></td><td>'.$open_late.'</td><td>'.$closed.'</td>'.PHP_EOL;
        }
    }

    /* Graphing functions */

    public function BuildGraphJS($timeframes, $exceptions) {
        $js  = $this->js_includes . PHP_EOL;
        $vars = $this->DefineGraphVars($timeframes,$exceptions);
        $js .= '<script type="text/javascript">'.PHP_EOL;
        $js .= '$(function() {'.PHP_EOL;
        $js .= $vars->vars;
        $js .= '   $.plot("#placeholder", '.$vars->idArray.', {'.PHP_EOL;
        $js .= '     xaxis: { mode: "time" },'.PHP_EOL;
        $js .= '     yaxis: { min:0, max: 4 },'.PHP_EOL;
        $js .= '     series: { points: { show: true }, lines: { show:true} }'.PHP_EOL;
        $js .= '   });'.PHP_EOL;
        $js .= '});'.PHP_EOL;
		// $js .= '$("#footer").prepend("Flot " + $.plot.version + " &ndash; ");'.PHP_EOL;
        $js .= '</script>'.PHP_EOL;
        return $js;
    }

    private function DefineGraphVars($timeframes, $exceptions) {
        $return = new stdClass();
        $all_graph_ids = array();
        $times = json_decode($timeframes);
        $exes  = json_decode($exceptions);
        foreach ($times as $tf) {
            $tf->lineID = preg_replace("/[ ,:']+/","",$tf->name);
            $tf->varstring = 'var '.$tf->lineID.' = [['.$this->jsTime($tf->first_date).','. $tf->rank .'], ';
            $tf->varstring.= '['.$this->jsTime($tf->last_date).','. $tf->rank. ']];'.PHP_EOL;
            $return->vars .= $tf->varstring;
            array_push($all_graph_ids, $tf->lineID);
        }
        foreach ($exes as $ex) {
            $ex->lineID = 'Exception'.preg_replace("/-/","",$ex->date);
            $ex->varstring = 'var '.$ex->lineID.' = [['.$this->jsTime($ex->date).',3]];'.PHP_EOL;
            $return->vars .= $ex->varstring;
            array_push($all_graph_ids, $ex->lineID);
        }

        $return->idArray = '[' . join(',',$all_graph_ids) . ']';
        return $return;

    }
    
    private function jsTime($str) {
        return strtotime($str)*1000;
    }
}


?>