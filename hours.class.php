<?
class Hours {
    public $db;

    public function __construct() {
        include_once ("config.php");
        try { 
            $this->db = new PDO("mysql:host=$hostname;dbname=$database;charset=$charset", "$username", "$password");
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $ex) {
            echo $ex->getMessage();
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
        $hours = $this->GetHoursFromPreset($date);
    }
    if ($format == "text") { 
        return $hours;
    }
    elseif ($format == "xmlIthaca") {
        return '<day date="'.$date.'">'.$hours.'</day>'.PHP_EOL;
    }
}

public function GetHoursFromPreset($date) {
    $day_of_week = date("l", strtotime($date));
    $q = "SELECT settings.* FROM settings,timeframes WHERE timeframes.first_date <= ? and timeframes.last_date >= ? and apply_preset_id = preset_id and day = ?";

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
} 
?>