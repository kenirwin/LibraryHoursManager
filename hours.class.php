<?
class Hours {
    private $db;
    private $days = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');

    public function __construct() {
        include_once ("config.php");
        try { 
            $this->db = new PDO("mysql:host=$hostname;dbname=$database;charset=$charset", "$username", "$password");
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $ex) {
            echo $ex->getMessage();
        }
    }
    
    private function ExecutePrepared($query,$values) {
        try {
            //            print ($query);
            //print_r ($values);
            $stmt=$this->db->prepare($query);
            $stmt->execute($values);
            return $stmt;
        } catch (PDOException $ex) {
            print $ex->getMessage();
            return $ex->getMessage();
        }
    }

    public function ListDailyHours ($format,$req) {
        if (isset($req['first_date']) && $req['first_date'] != '') {
            $start = $req['first_date'];
        }
        else { $start = $this->GetDate('first'); }
        if (isset($req['last_date']) && $req['last_date'] != '') {
            $end = $req['last_date'];
        }
        else { $end = $this->GetDate("last"); }
        $date = $start; 
        $output = "";
        while ($date <= $end) {
            if ($format == "xmlIthaca") {
                $output .=  $this->GetHoursByDate($date, $format);
            }
            else {
                $output .= $date . ": " . $this->GetHoursByDate($date) .'<br>'. PHP_EOL;
            }
            $date = date("Y-m-d", strtotime ($date . " + 1 day"));
        }
        return $output;
    }
    
    public function GetHoursByDate ($date, $format="text") {
        $q = "SELECT * FROM exceptions WHERE `date` = ?";
        $stmt = $this->db->prepare($q);
        $stmt->execute(array($date));
        if ($stmt->rowCount() == 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row['closed'] == "Y") {
                $hours = "CLOSED";
            }
            else {
                $hours = $row['opentime'] .' - '. $row['closetime'];
            }
        }
        else {
            $hours = $this->GetHoursFromTimeframe($date);
        }
        if ($format == "text") { 
            return $hours;
        }
        elseif ($format == "xmlIthaca") {
            return '<day date="'.$date.'">'.$hours.'</day>'.PHP_EOL;
        }
    }
    
    public function GetHoursFromTimeframe($date) {
        $day_of_week = date("l", strtotime($date));
        $q = "SELECT settings.* FROM settings,timeframes,presets WHERE timeframes.first_date <= ? and timeframes.last_date >= ? and apply_preset_id = preset_id and preset_id = presets.id and settings.day = ? ORDER BY presets.rank DESC LIMIT 0,1";
        
        $stmt = $this->db->prepare($q);
        $stmt->execute(array($date,$date,$day_of_week));
        
        if ($stmt->rowCount() == 1) {
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if ($row['closed'] == "Y") {
                    return "CLOSED";
                }
                else {
                    return $row['opentime'] . ' - '. $row['closetime'];
                }
            }
        }
        else {
            return ('Hours Unknown');
        }
    }
    
    public function GetDate ($position) { // position is "first" or "last"
        if ($position == "last") {
            $q = "SELECT last_date FROM timeframes ORDER BY last_date DESC LIMIT 0,1";
        }
        elseif ($position == "first") {
            $q = "SELECT first_date FROM timeframes ORDER BY first_date ASC LIMIT 0,1";
        }
        $stmt = $this->db->query($q);
        while ($row = $stmt->fetch()) {
            return $row[0];
        }
    } 
    

    // Insert & Update functions

    public function UpdatePreset($req) {
        $req = json_decode($req);
        if (isset($req->preset_id) && ($req->preset_id != '')) {
            $q2 = 'update presets SET name=?, rank=? WHERE id=?';
            $v2 = array($req->preset_name, $req->rank, $req->preset_id);
            $this->ExecutePrepared($q2,$v2);
        }
        else { //if new preset
            $q1 = 'INSERT INTO presets (name,rank) VALUES (?,?)';
            $v1 = array ($req->preset_name,$req->rank);
            print "<li>$q1</li>";
            print_r ($v1);
            $this->ExecutePrepared($q1,$v1);
            $req->preset_id = $this->db->lastInsertId();
        }
        return $req->preset_id;
        print '<hr>'.PHP_EOL;
    }
    
    public function UpdateSettings($req, $new_preset_id='') {
        $req = json_decode($req);
        print "<h2>$new_preset_id</h2>";
        if (is_numeric($new_preset_id)) { $req->preset_id = $new_preset_id; }
        $settings_fields = array('opentime','closetime','latenight','closed');
        $onetime_fields = array('name','first_date','last_date');
        
        foreach ($onetime_fields as $f) {
            $values[$f] = $req->$f;
        }
        foreach ($this->days as $day) {
            if (isset($req->settings_key->$day)) { 
                $query_type = 'update';
                $settings_key = $req->settings_key->$day;
            }
            else {
                $query_type = 'insert';
            }
            if ($req->closed->$day == "on") {
                //$settings_values = array('','','N','Y');
                list($req->opentime->$day, $req->closetime->$day,$latenight,$closed) = array('','','N','Y');                
            }
            else { //else, if not closed....
                $settings_values = array();
                $closed = 'N';
                if ($req->latenight->$day == "on") {
                    $latenight = 'Y';
                }
                else { $latenight = 'N'; }
                $settings_values = array($req->opentime->$day, $req->closetime->$day, $latenight, $closed);
            }
            if ($query_type == 'update') {
                $q= 'UPDATE `settings` SET opentime="'.$req->opentime->$day.'", closetime="'.$req->closetime->$day.'", latenight="'.$latenight.'", closed="'.$closed.'" WHERE settings_key='.$settings_key;
            }
            elseif ((isset($req->opentime->$day) & isset($req->closetime->$day)) || $closed == 'Y') {
                $q= 'INSERT INTO `settings`(day,preset_id,opentime,closetime,latenight,closed) VALUES ("'.$day.'","'.$req->preset_id.'","'.$req->opentime->$day.'", "'.$req->closetime->$day.'", "'.$latenight.'", "'.$closed.'")';
            }
            if (isset($q)) {
                print '<li>'.$q;
                try {
                    $stmt = $this->db->query($q);
                    print "<li>AFFECTED: ".$stmt->rowCount();
                } catch (PDOException $ex) {
                    print '<li>'.$ex->getMessage();
                }
            }
        }
        print '<hr>'.PHP_EOL;
    }
    
    public function UpdateTimeframe($req, $new_preset_id) {
        $req = json_decode($req);
        print_r($req);
        $req->first_date = date("Y-m-d", strtotime ($req->first_date));
        $req->last_date = date("Y-m-d", strtotime ($req->last_date));
        if (isset($req->timeframe_id) && ($req->timeframe_id != '')) {
            $q = 'update timeframes SET name=?,first_date=?,last_date=? WHERE timeframe_id = ?';
            $v = array($req->name,$req->first_date,$req->last_date,$req->timeframe_id);
            $this->ExecutePrepared($q,$v);
        }
        else { //if new timeframe
            if (is_numeric($new_preset_id)) { $req->use_preset = $new_preset_id; }
            $q = 'INSERT INTO timeframes (name,first_date,last_date,apply_preset_id) VALUES (?,?,?,?)';
            $v = array($req->name,$req->first_date,$req->last_date,$req->use_preset);
            $this->ExecutePrepared($q,$v);
        }
        print '<hr>'.PHP_EOL;
    }

    public function DeleteTimeframe($id) {
        $q = 'DELETE FROM timeframes WHERE timeframe_id = ?';
        $v = array($id);
        $this->ExecutePrepared($q,$v);
    }

    // Exceptions / jTables functions

    public function ExceptionsList($jtSorting='date',$jtStartIndex=0,$jtPageSize=100) {        
        $jTableResults = array();
        $rows = array();
        $q = 'SELECT COUNT(*) AS RecordCount FROM exceptions';
        $stmt = $this->ExecutePrepared($q);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $recordCount = $row['RecordCount'];
        $q2 = 'SELECT * FROM exceptions ORDER BY '.$jtSorting.' LIMIT '.$jtStartIndex.', '.$jtPageSize.';';
        $stmt = $this->ExecutePrepared($q2);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $rows[] = $row;
        }
        $jTableResult['Result'] = 'OK';
        $jTableResult['TotalRecordCount'] = $recordCount;
        $jTableResult['Records'] = $rows;
        return (json_encode($jTableResult));
    }

    public function ExceptionsCreate($req) {
        $jTableResult = array();
        $q = 'INSERT INTO exceptions (date,opentime,closetime,latenight,closed) VALUES (?,?,?,?,?)';
        $v = array ($req['date'], $req['opentime'], $req['closetime'], $req['latenight'], $req['closed']);
        $stmt = $this->ExecutePrepared($q,$v);
        
        $q2 = 'SELECT * FROM exceptions WHERE except_id = LAST_INSERT_ID();';
        $stmt = $this->ExecutePrepared($q2);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $jTableResult['Result'] = 'OK';
        $jTableResult['Record'] = $row;
        return (json_encode($jTableResult));
    }
    
    public function ExceptionsUpdate($req) {
        $q = 'UPDATE exceptions SET opentime=?,closetime=?,latenight=?,closed=? WHERE except_id=?';
        $v = array ($req['opentime'], $req['closetime'],$req['latenight'],$req['closed'],$req['except_id']);
        $stmt = $this->ExecutePrepared($q,$v);
        $jTableResult = array();
        $jTableResult['Result'] = 'OK';
        return (json_encode($jTableResult));
    }
    
    public function ExceptionsDelete($req) {
        $q = 'DELETE FROM exceptions WHERE except_id = ?';
        $v = array ($req['except_id']);
        $stmt = $this->ExecutePrepared($q,$v);
        $jTableResult = array();
        $jTableResult['Result'] = 'OK';
        return (json_encode($jTableResult));
    }

    public function PrintGenerateForm () {
        $first = $this->GetDate('first');
        $last  = $this->GetDate('last');
        $form  = '<form action="generate.php">'.PHP_EOL;
        $form .= '';
        $form .= '<form>'.PHP_EOL;
        return $form;
    }
    
    // JSON functions
    
    public function GetJSON ($table, $conditions=array()) {
        $allowed = array('exceptions','presets','settings','timeframes');
        if (in_array($table,$allowed)) {
            $q = 'SELECT * FROM '.$table;
            $stmt = $this->db->prepare($q);
            $stmt->execute();
            return json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        }
    }

    public function GetTimeframesAndRanks () {
        $q = 'SELECT timeframes.name as name, timeframes.timeframe_id as id,first_date,last_date,apply_preset_id,rank FROM timeframes,presets where timeframes.apply_preset_id = presets.id';
        $stmt = $this->db->prepare($q);
        $stmt->execute();
        return json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function GetTimeframeDetails ($id) {
        $q = 'SELECT * FROM settings,timeframes,presets where presets.id = settings.preset_id and apply_preset_id = presets.id and settings.preset_id = ?';
        $stmt = $this->db->prepare($q);
        $stmt->execute(array($id));
        return json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function GetSettingsDetails ($id) {
        $q = 'SELECT settings.*, presets.name, presets.id FROM settings,presets WHERE settings.preset_id = presets.id and settings.preset_id = ?';
        $stmt = $this->db->prepare($q);
        $stmt->execute(array($id));
        return json_encode($stmt->fetchall(PDO::FETCH_ASSOC));
    }
} 



?>