<?php
require_once PATH_LIB . 'Logger.php';
require_once PATH_LIB . 'BaseDbi.php';

class DbiSale extends BaseDbi
{
    private static $instance;
    
    protected function __construct()
    {
        $this->db = 'yizhong';
        $this->init();
    }
    
    public static function getDbi()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function addAgency($name, $contact, $tel, $province, $city, $county, $type, $intension)
    {
        $sql = "insert into agency (agency_name, agency_contact, agency_tel, 
                agency_province, agency_city, agency_county, type, agency_intension) 
                values ('$name', '$contact', '$tel', '$province', '$city', '$county', '$type', '$intension')";
        $id = $this->insertData($sql);
        if (VALUE_DB_ERROR === $id) {
            return VALUE_DB_ERROR;
        }
        return $id;
    }
    public function addBid($hospitalId, $agencyId, $product, $amount, $bidTime, $content)
    {
        $sql = "insert into bid (hospital_id, agency_id, product, amount, bid_time, content)
                values ('$hospitalId', '$agencyId', '$product', '$amount', '$bidTime', '$content')";
        $id = $this->insertData($sql);
        if (VALUE_DB_ERROR === $id) {
            return VALUE_DB_ERROR;
        }
        return $id;
    }
    public function addCounty($county, $combinaion, $content, $agencyId)
    {
        $sql = "insert into county (county_id, combination, content, agency_id) values ('$county', '$combinaion', '$content', '$agencyId')";
        $id = $this->insertData($sql);
        if (VALUE_DB_ERROR === $id) {
            return VALUE_DB_ERROR;
        }
        return $id;
    }
    /*
    public function addHospital($name, $contact, $tel, $province, $city, $county, $agencyId, $intension)
    {
        $sql = "insert into hospital (hospital_name, hospital_contact, hospital_tel, province, city, county, agency_id, hospital_intension) 
                values ('$name', '$contact', '$tel', '$province', '$city', '$county', '$agencyId', '$intension')";
        $id = $this->insertData($sql);
        if (VALUE_DB_ERROR === $id) {
            return VALUE_DB_ERROR;
        }
        return $id;
    }
    */
    public function addHospital($name, array $data)
    {
        $sql1 = "insert into hospital (hospital_name";
        $sql2 = "values ('$name'";
        foreach ($data as $key => $value) {
            $sql1 .= ", $key";
            $sql2 .= ", '$value'";
        }
        $sql = $sql1 . ') ' . $sql2 . ')';
        $id = $this->insertData($sql);
        if (VALUE_DB_ERROR === $id) {
            return VALUE_DB_ERROR;
        }
        return $id;
    }
    public function addRecord($userId, $recordText, $hospitalId, $agencyId)
    {
        $sql = "insert into record (user_id, record_text, hospital_id, agency_id)
                values ('$userId', '$recordText', '$hospitalId', '$agencyId')";
        $id = $this->insertData($sql);
        if (VALUE_DB_ERROR === $id) {
            return VALUE_DB_ERROR;
        }
        return $id;
    }
    public function addUser($name)
    {
        $sql = "insert into user (user_name) values ('$name')";
        $id = $this->insertData($sql);
        if (VALUE_DB_ERROR === $id) {
            return VALUE_DB_ERROR;
        }
        return $id;
    }
    public function editById($table, $id, $data)
    {
        if ($table == 'county') {
            return $this->updateTableByKey($table, 'id', $id, $data);
        }
        return $this->updateTableByKey($table, $table . '_id', $id, $data);
    }
    public function existedId($table, $id)
    {
        return $this->existData($table, $table . '_id = ' . $id);
    }
    public function existedAgency($name, $tel)
    {
        return $this->existData('agency', "agency_name = '$name' and agency_tel = '$tel'");
    }
    public function existedHospital($name, $tel)
    {
        return $this->existData('hospital', "hospital_name = '$name' and hospital_tel = '$tel'");
    }
    public function getBid($hospitalId, $agencyId, $startTime, $endTime)
    {
        $sql = "select * from bid where 1 ";
        if ($hospitalId != null) {
            $sql .= " and hospital_id = '$hospitalId' ";
        }
        if ($agencyId != null) {
            $sql .= " and agency_id = '$agencyId' ";
        }
        if ($startTime != null) {
            $sql .= " and bid_time >= '$startTime' ";
        }
        if ($endTime != null) {
            $sql .= " and bid_time <= '$endTime' ";
        }
        return $this->getDataAll($sql);
    }
    public function getRecord($hospitalId, $agencyId, $userId, $startTime, $endTime)
    {
        $sql = "select * from record where 1 ";
        if ($hospitalId != null) {
            $sql .= " and hospital_id = '$hospitalId' ";
        }
        if ($agencyId != null) {
            $sql .= " and agency_id = '$agencyId' ";
        }
        if ($userId != null) {
            $sql .= " and user_id = '$userId' ";
        }
        if ($startTime != null) {
            $sql .= " and create_time >= '$startTime' ";
        }
        if ($endTime != null) {
            $sql .= " and create_time <= '$endTime' ";
        }
        return $this->getDataAll($sql);
    }
}
