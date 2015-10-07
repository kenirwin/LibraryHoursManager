<?
class HoursAdmin {
    public $all_flot_ids = array();
    public $all_flot_ids_str;

    public $js_includes = <<<EOT
	<link href="./flot/examples.css" rel="stylesheet" type="text/css">
	<!--[if lte IE 8]><script language="javascript" type="text/javascript" src="./flot/excanvas.min.js"></script><![endif]-->
	<script language="javascript" type="text/javascript" src="./flot/jquery.js"></script>
	<script language="javascript" type="text/javascript" src="./flot/jquery.flot.js"></script>
	<script language="javascript" type="text/javascript" src="./flot/jquery.flot.time.js"></script>
EOT;
    public function BuildGraphJS($json) {
        $js  = $this->js_includes . PHP_EOL;
        $vars = $this->DefineGraphVars($json);
        $js .= '<script type="text/javascript">'.PHP_EOL;
        $js .= '$(function() {'.PHP_EOL;
        $js .= $vars->vars;
        $js .= '   $.plot("#placeholder", '.$vars->idArray.', {'.PHP_EOL;
        $js .= '     xaxis: { mode: "time" }'.PHP_EOL;
        $js .= '   });'.PHP_EOL;
        $js .= '});'.PHP_EOL;
		// $js .= '$("#footer").prepend("Flot " + $.plot.version + " &ndash; ");'.PHP_EOL;
        $js .= '</script>'.PHP_EOL;
        return $js;
    }

    private function DefineGraphVars($json) {
        $return = new stdClass();
        $all_graph_ids = array();
        $times = json_decode($json);
        foreach ($times as $tf) {
            $tf->lineID = preg_replace("/[ ,:']+/","",$tf->name);
            $tf->varstring = 'var '.$tf->lineID.' = [['.$this->jsTime($tf->first_date).','. $tf->rank .'], ';
            $tf->varstring.= '['.$this->jsTime($tf->last_date).','. $tf->rank. ']];'.PHP_EOL;
            $return->vars .= $tf->varstring;
            array_push($all_graph_ids, $tf->lineID);
        }
        $return->idArray = '[' . join(',',$all_graph_ids) . ']';
        return $return;
    }

    
    private function jsTime($str) {
        return strtotime($str)*1000;
    }
}


?>