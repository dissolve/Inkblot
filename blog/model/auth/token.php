<?php
class ModelAuthToken extends Model {
	public function newToken($user, $scope, $client_id) {
        $checksum = md5($user. $scope. $client_id. date('Y_z_H_i_s').DATABASE.DB_HOSTNAME );
        $this->db->query("INSERT INTO " . DATABASE . ".tokens SET 
            user='".$this->db->escape($user)."',
            scope='".$this->db->escape($scope)."',
            client_id='".$this->db->escape($client_id)."',
            last_used=NOW(),
            checksum='".$this->db->escape($checksum)."'");
            
        $id = $this->db->getLastId();
        return $id . ',' . $checksum;
	}

    public function getAuthFromToken($token){
        $token_bits = explode(',', $token);
        $token_id = $token_bits[0];
        $checksum = $token_bits[1];

        $results = $this->db->query("SELECT * FROM " . DATABASE . ".tokens WHERE token_id=". (int)$token_id. " AND checksum = '".$this->db->escape($checksum)."' LIMIT 1"); //TODO some expiration date

        if($results->row){
            $this->db->query("UPDATE " . DATABASE . ".tokens SET last_used=NOW() WHERE token_id=". (int)$token_id);
        }

        return $results->row;
    }


}
?>
