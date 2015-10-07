<?
class HoursAdmin {
    private $js_includes = <<<EOT
	<link href="./flot/examples.css" rel="stylesheet" type="text/css">
	<!--[if lte IE 8]><script language="javascript" type="text/javascript" src="./flot/excanvas.min.js"></script><![endif]-->
	<script language="javascript" type="text/javascript" src="./flot/jquery.js"></script>
	<script language="javascript" type="text/javascript" src="./flot/jquery.flot.js"></script>
	<script language="javascript" type="text/javascript" src="./flot/jquery.flot.time.js"></script>
EOT;

    /* Display functions */
    
    public function PresetsPicker ($json) {
        $presets = json_decode($json);
        $table = '<table id="presets-picker">'.PHP_EOL;
        foreach($presets as $p) {
            $table .= '<tr data-preset-id="'.$p->apply_preset_id.'"><td>'.$p->name.'</td><td>'.$p->first_date.'</td><td>'.$p->last_date.'</td><td>'.$p->rank.'</td></tr>'.PHP_EOL;
        }
        $table .='</table>'.PHP_EOL;
        print $table;
    }

    public function EditPresetDetails ($json) {
        $details = json_decode($json);
        //        print_r($details); print '<hr>'.PHP_EOL;
        $table .= '<form id="presets-editor" action="edit.php">'.PHP_EOL;
        $table .= $this->FormRow('name', $details[0]->name, 'text');
        $table .= $this->FormRow('first_date', $details[0]->first_date, 'text');
        $table .= $this->FormRow('last_date', $details[0]->last_date, 'text');
        $table .= $this->FormDays($details);
        $table .= '<input type="hidden" name="action" value="submit_preset_values">'.PHP_EOL;
        $table .= '<input type="submit">'.PHP_EOL;
        $table .= '</form>'.PHP_EOL;
        return $table;
    }

    private function FormRow($key, $value, $type) {
        return $key.': <input type="'.$type.'" name="'.$key.'" value="'.$value.'" size="50"><br />'.PHP_EOL;
    }

    private function FormDays($arr) {
        $days = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
        $table  = '<table>'.PHP_EOL;
        $table .= '<tr><td>Day</td><td>Open Time</td></td><td>Close Time</td><td>Open Past Midnight</td><td>Closed</td></tr>'.PHP_EOL;
        foreach ($days as $day) {
            $table .= '<tr><td>'.$day.'</td>';
            $table .= $this->FindDayValues($day, $arr);
            $table .= '</tr>'.PHP_EOL;
            $table .= '</tr>';
        }
        $table .= '</table>'.PHP_EOL;
        return $table;
    }

    private function FindDayValues($day, $arr) {
        foreach ($arr as $day_settings) {
            if ($day_settings->day == $day) {
                if ($day_settings->closed == 'Y') {
                    $closed = '<input type="checkbox" name="closed['.$day_settings->day.']" checked />'.PHP_EOL;
                }
                else {
                    $closed = '<input type="checkbox" name="closed['.$day_settings->day.']"/>'.PHP_EOL;
                }

                if ($day_settings->latenight == 'Y') {
                    $open_late = '<input type="checkbox" name="latenight['.$day_settings->day.']" checked />'.PHP_EOL;
                }
                else {
                    $open_late = '<input type="checkbox" name="latenight['.$day_settings->day.']"/>'.PHP_EOL;
                }



                return '<td><input type="text" name="opentime['.$day_settings->day.']" value="'.$day_settings->opentime.'"></td><td><input type="text" name="closetime['.$day_settings->day.']" value="'.$day_settings->closetime.'"></td><td>'.$open_late.'</td><td>'.$closed.'</td>'.PHP_EOL;
            }
        }
        // if day settings not found
        $closed = '<input type="checkbox" name="closed['.$day_settings->day.']"/>'.PHP_EOL;
        $open_late = '<input type="checkbox" name="latenight['.$day_settings->day.']"/>'.PHP_EOL;
        return '<td><input type="text" name="opentime['.$day_settings->day.']" value=""></td><td><input type="text" name="closetime['.$day_settings->day.']" value=""></td><td>'.$open_late.'</td><td>'.$closed.'</td>'.PHP_EOL;
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