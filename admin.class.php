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

    public function EditPresetDetails ($details) {
        return $details;
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