<?php
class ModelAuthToken extends Model {
	public function newToken($user, $scope, $client_id) {
        $checksum = md5sum($user. $scope. $client_id. date());
        $this->db->query("INSERT INTO " . DATABASE . ".tokens SET 
            user='".$this->db->escape($user)."',
            scope='".$this->db->escape($scope)."',
            client_id='".$this->db->escape($client_id)."',
            last_used=NOW(),
            checksum='".$this->db->escape($checksum)."'");
            
        $id = $this->db->getLastId();
        return $id . ',' . $checksum;
	}


}
?>
