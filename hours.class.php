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
            print ($query);
            print_r ($values);
            $stmt=$this->db->prepare($query);
            $stmt->execute($values);
            print '<li>Rows affected: ' . $stmt->rowCount() . '</li>'.PHP_EOL;
        } catch (PDOException $ex) {
            print $ex->getMessage;
        }
    }

    public function ListDailyHours ($format) {
        $start = date("Y-m-d");
        $end = $this->GetLastDate();
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
            return ($q);
        }
    }
    
    public function GetLastDate () {
        $q = "SELECT last_date FROM timeframes ORDER BY last_date DESC LIMIT 0,1";
        $stmt = $this->db->query($q);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return $row['last_date'];
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
        //        presets.id; settings.preset_id; timeframes.apply_preset_id;
        //        $q1 = 'DELETE FROM presets WHERE id = ?';
        //$q2 = 'DELETE FROM settings WHERE preset_id = ?';
        $q3 = 'DELETE FROM timeframes WHERE apply_preset_id = ?';
        $v = array($id);
        //$this->ExecutePrepared($q1,$v);
        //$this->ExecutePrepared($q2,$v);
        $this->ExecutePrepared($q3,$v);
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