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
    
    public function addAgency($name, $contact, $tel, $status, $province, $city, $county, $address, 
            $type, $intension, $content, $source, $userId, $totalBidTimes)
    {
        $sql = "insert into agency (agency_name, agency_contact, agency_tel, status, agency_province, agency_city, 
                agency_county, address, type, agency_intension, content, source, user_id, total_bid_times) 
                values ('$name', '$contact', '$tel', '$status', '$province', '$city', '$county', '$address', 
                '$type', '$intension', '$content', '$source', '$userId', '$totalBidTimes')";
        $id = $this->insertData($sql);
        if (VALUE_DB_ERROR === $id) {
            return VALUE_DB_ERROR;
        }
        return $id;
    }
    public function addBid($hospitalId, $agencyId, $product, $amount, $bidTime, $content, $source)
    {
        $sql = "insert into bid (hospital_id, agency_id, product, amount, bid_time, content, source)
                values ('$hospitalId', '$agencyId', '$product', '$amount', '$bidTime', '$content', '$source')";
        $id = $this->insertData($sql);
        if (VALUE_DB_ERROR === $id) {
            return VALUE_DB_ERROR;
        }
        return $id;
    }
    public function addCounty($county, $combinaion, $content, $agencyId)
    {
        $sql = "insert into county (county, combination, content, agency_id) values ('$county', '$combinaion', '$content', '$agencyId')";
        $id = $this->insertData($sql);
        if (VALUE_DB_ERROR === $id) {
            return VALUE_DB_ERROR;
        }
        return $id;
    }
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
    public function addPlan($hospitalId, $agencyId, $planText, $deadLine, $userId)
    {
        $sql = "insert into plan (hospital_id, agency_id, plan_text, dead_line, user_id)
                values ('$hospitalId', '$agencyId', '$planText', '$deadLine', '$userId')";
        $id = $this->insertData($sql);
        if (VALUE_DB_ERROR === $id) {
            return VALUE_DB_ERROR;
        }
        return $id;
    }
    public function addRecord($userId, $recordText, $hospitalId, $agencyId, $recordTime, 
            $yuanzhang, $fenguanyuanzhang, $xinneike, $xindiantushi, $style, $planId)
    {
        $sql = "insert into record (user_id, record_text, hospital_id, agency_id, record_time, 
                yuanzhang, fenguanyuanzhang, xinneike, xindiantushi, style, plan_id)
                values ('$userId', '$recordText', '$hospitalId', '$agencyId', '$recordTime', 
                '$yuanzhang', '$fenguanyuanzhang', '$xinneike', '$xindiantushi', '$style', '$planId')";
        $id = $this->insertData($sql);
        if (VALUE_DB_ERROR === $id) {
            return VALUE_DB_ERROR;
        }
        return $id;
    }
    public function addRelation($hospitalId, $agencyId, $createTime, $content, $bidTimes, $source)
    {
        $sql = "insert into relation (hospital_id, agency_id, create_time, content, bid_times, source)
                values ('$hospitalId', '$agencyId', '$createTime', '$content', '$bidTimes', '$source')";
        $id = $this->insertData($sql);
        if (VALUE_DB_ERROR === $id) {
            return VALUE_DB_ERROR;
        }
        return $id;
    }
    public function addUser($name, $tel, $duty, $area, $enterTime)
    {
        $sql = "insert into user (user_name, user_tel, duty, area, enter_time) 
                values ('$name', '$tel', '$duty', '$area', '$enterTime')";
        $id = $this->insertData($sql);
        if (VALUE_DB_ERROR === $id) {
            return VALUE_DB_ERROR;
        }
        return $id;
    }
    public function deleteInfo($table, $id)
    {
        $sql = "delete from $table where $table" . '_' . 'id = ' . $id;
        return $this->deleteData($sql);
    }
    public function editById($table, $id, $data)
    {
        return $this->updateTableByKey($table, $table . '_id', $id, $data);
    }
    public function existedId($table, $id)
    {
        return $this->existData($table, $table . '_id = ' . $id);
    }
    public function existedAgency($name, $tel)
    {
        $where = empty($name) ? "agency_tel = '$tel'" : "agency_name = '$name' and agency_tel = '$tel'";
        return $this->existData('agency', $where);
    }
    public function existedHospital($name, $tel)
    {
        return $this->existData('hospital', "hospital_name = '$name' and hospital_tel = '$tel'");
    }
    public function getAgencyList($name, $province, $city, $county, $user, $intension, $type)
    {
        $sql = 'select a.agency_id, agency_name, agency_contact, agency_tel, agency_province, agency_city, agency_county, 
                a.address, a.status, a.type, total_bid_times, a.content, a.source, u.user_name, agency_intension, 
                a.create_time, total_bid_times
                from agency as a
                left join `user` as u on a.user_id = u.user_id
                where 1';
        if (!empty($name)) {
            $sql .= " and (agency_name like '%$name%' or agency_contact like '%$name%')";
        }
        if (!empty($province)) {
            $sql .= " and agency_province = '$province'";
        }
        if (!empty($city)) {
            $sql .= " and agency_city = '$city'";
        }
        if (!empty($county)) {
            $sql .= " and agency_county = '$county'";
        }
        if (!empty($user)) {
            $sql .= " and u.user_name = '$user'";
        }
        if (!empty($intension)) {
            $sql .= " and agency_intension >= '$intension'";
        }
        if (empty($type)) {
            $sql .= " and a.type = '0'";
        }
        return $this->getDataAll($sql);
    }
    public function getBid($hospitalId, $agencyId, $startTime, $endTime)
    {
        $sql = "select b.*, h.hospital_name, a.agency_name
                from bid as b left join hospital as h on b.hospital_id = h.hospital_id
                left join agency as a on b.agency_id = a.agency_id where 1 ";
        if ($hospitalId != null) {
            $sql .= " and b.hospital_id = '$hospitalId' ";
        }
        if ($agencyId != null) {
            $sql .= " and b.agency_id = '$agencyId' ";
        }
        if ($startTime != null) {
            $sql .= " and bid_time >= '$startTime' ";
        }
        if ($endTime != null) {
            $sql .= " and bid_time <= '$endTime' ";
        }
        return $this->getDataAll($sql);
    }
    public function getCounty($county, $agencyId)
    {
        $sql = "select c.*, a.agency_name from county as c
                left join agency as a on c.agency_id = a.agency_id where 1 ";
        if ($county != null) {
            $sql .= " and c.county = '$county' ";
        }
        if ($agencyId != null) {
            $sql .= " and c.agency_id = '$agencyId' ";
        }
        return $this->getDataAll($sql);
    }
    public function getHospitalList($name, $province, $city, $county, $agency, $user, $intension)
    {
        /*
        $sql = 'select hospital_id, hospital_name, hospital_contact, hospital_tel, province, city, county,
                a.agency_name, u.user_name, hospital_intension, h.create_time, h.content, h.`status`
                from hospital as h left join agency as a on h.agency_id = a.agency_id
                left join `user` as u on h.user_id = u.user_id where 1';
        */
        $sql = 'select h.hospital_id, hospital_name, hospital_contact, hospital_tel, province, city, county,
                a.agency_name, u.user_name, hospital_intension, h.create_time, h.content, h.`status`, 
                h.stage, h.question, h.scheme, h.xindian_paidui, h.success_rate, 
                p.plan_text, p.dead_line, p.create_time as plan_time, 
                r.record_text, r.record_time,  r.yuanzhang, r.fenguanyuanzhang, r.xinneike, r.xindiantushi
                from hospital as h left join agency as a on h.agency_id = a.agency_id
                left join `user` as u on h.user_id = u.user_id
                left join (select p.plan_text, p.dead_line, p.create_time, p.hospital_id from plan as p inner join 
                (select max(plan_id) as id, hospital_id from plan group by hospital_id) as tp on p.plan_id = tp.id) as p 
                on h.hospital_id = p.hospital_id
                left join (select r.record_text, r.record_time, r.yuanzhang, r.fenguanyuanzhang, r.xinneike, r.xindiantushi, r.hospital_id 
                from record as r inner join 
                (select max(record_id) as id, hospital_id from record group by hospital_id) as tr on r.record_id = tr.id) as r 
                on h.hospital_id = r.hospital_id
                where 1';
        if (!empty($name)) {
            $sql .= " and h.hospital_name like '%$name%'";
        }
        if (!empty($province)) {
            $sql .= " and h.province = '$province'";
        }
        if (!empty($city)) {
            $sql .= " and h.city = '$city'";
        }
        if (!empty($county)) {
            $sql .= " and h.county = '$county'";
        }
        if (!empty($agency)) {
            $sql .= " and a.agency_name = '$agency'";
        }
        if (!empty($user)) {
            $sql .= " and u.user_name = '$user'";
        }
        if (!empty($intension)) {
            $sql .= " and hospital_intension >= '$intension'";
        }
        return $this->getDataAll($sql);
    }
    public function getMainList($table)
    {
        $sql = "select $table" . "_id, $table" . "_name from $table";
        return $this->getDataAll($sql);
    }
    public function getInfoAll($table)
    {
        $sql = "select * from $table";
        return $this->getDataAll($sql);
    }
    public function getInfoOne($table, $id)
    {
        $sql = "select * from $table where $table" . "_id = '$id' limit 1";
        return $this->getDataRow($sql);
    }
    public function getPlan($hospitalId, $agencyId, $userId, $startTime, $endTime, $status)
    {
        $sql = "select p.*, h.hospital_name, a.agency_name, u.user_name
                from plan as p left join hospital as h on p.hospital_id = h.hospital_id
                left join agency as a on p.agency_id = a.agency_id
                left join user as u on p.user_id = u.user_id where 1 ";
        if ($hospitalId != null) {
            $sql .= " and p.hospital_id = '$hospitalId' ";
        }
        if ($agencyId != null) {
            $sql .= " and p.agency_id = '$agencyId' ";
        }
        if ($userId != null) {
            $sql .= " and p.user_id = '$userId' ";
        }
        if ($status != null) {
            $sql .= " and p.status = '$status' ";
        }
        if ($startTime != null) {
            $sql .= " and p.dead_line >= '$startTime' ";
        }
        if ($endTime != null) {
            $sql .= " and p.create_time <= '$endTime' ";
        }
        $sql .= ' order by p.plan_id desc';
        return $this->getDataAll($sql);
    }
    public function getRecord($hospitalId, $agencyId, $userId, $startTime, $endTime, $planId)
    {
        $sql = "select r.*, h.hospital_name, a.agency_name, u.user_name 
                from record as r left join hospital as h on r.hospital_id = h.hospital_id
                left join agency as a on r.agency_id = a.agency_id 
                left join user as u on r.user_id = u.user_id where 1 ";
        if ($hospitalId != null) {
            $sql .= " and r.hospital_id = '$hospitalId' ";
        }
        if ($agencyId != null) {
            $sql .= " and r.agency_id = '$agencyId' ";
        }
        if ($userId != null) {
            $sql .= " and r.user_id = '$userId' ";
        }
        if ($planId != null) {
            $sql .= " and r.plan_id = '$planId' ";
        }
        if ($startTime != null) {
            $sql .= " and record_time >= '$startTime' ";
        }
        if ($endTime != null) {
            $sql .= " and record_time <= '$endTime' ";
        }
        $sql .= ' order by r.record_id desc';
        return $this->getDataAll($sql);
    }
    public function getRelation($hospitalId, $agencyId)
    {
        $sql = "select r.relation_id, r.hospital_id, r.agency_id, r.create_time, r.content, r.source, 
                h.hospital_name, a.agency_name, ifnull(rgp.bid_times, 0) as bid_times
                from relation as r 
                left join hospital as h on r.hospital_id = h.hospital_id
                left join agency as a on r.agency_id = a.agency_id
                left join (select hospital_id, agency_id, count(bid_id) as bid_times from bid group by hospital_id, agency_id) as rgp
                on r.hospital_id = rgp.hospital_id and r.agency_id = rgp.agency_id
                where 1 ";
        if ($hospitalId != null) {
            $sql .= " and r.hospital_id in ($hospitalId) ";
        }
        if ($agencyId != null) {
            $sql .= " and r.agency_id = '$agencyId' ";
        }
        return $this->getDataAll($sql);
    }
}
