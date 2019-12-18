<?php
class ModelAuthMpsyndicate extends Model {
    public function addSite($site_url, $token, $label = null)
    {
        $this->db->query("INSERT INTO " . DB_DATABASE . ".mp_syndicate SET 
            " . ($label ? " label='" . $this->db->escape($label) . "'," : "" ) . "
            site_url='" . $this->db->escape($site_url) . "',
            token='" . $this->db->escape($token) . "'");

        $this->cache->delete('mpsyndicate');

        $id = $this->db->getLastId();
        return $id ;
    }

    public function getSiteList()
    {
        $data = $this->cache->get('mpsyndicate.all');
        if (!$data) {
            $query = $this->db->query(
                "SELECT COALESCE( label, site_url) as name,
                site_url as url
                FROM " . DB_DATABASE . ".mp_syndicate"
            );

            $data = $query->rows;
            $this->cache->set('mpsyndicate.all', $data);
        }

        return $data;
    }

    public function getDataForName($sitename)
    {
        $data = $this->cache->get('mpsyndicate.site.' . $sitename);
        if (!$data) {
            $query = $this->db->query(
                "SELECT * 
                FROM " . DB_DATABASE . ".mp_syndicate
                WHERE label = '" . $this->db->escape($sitename) . "'
                ORDER BY mp_syndicate_site_id DESC
                LIMIT 1"
            );

            if (!empty($query->row)) {
                $data = $query->row;
            } else {
                $query = $this->db->query(
                    "SELECT * 
                    FROM " . DB_DATABASE . ".mp_syndicate
                    WHERE site_url = '" . $this->db->escape($sitename) . "'
                    ORDER BY mp_syndicate_site_id DESC
                    LIMIT 1"
                );

                if (!empty($query->row)) {
                    $data = $query->row;
                } else {
                    $data = array();
                }
            }


            $this->cache->set('mpsyndicate.site.' . $sitename, $data);
        }

        return $data;
    }


}
