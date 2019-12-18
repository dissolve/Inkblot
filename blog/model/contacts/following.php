<?php
class ModelContactsFollowing extends Model {

    public function followCard($card_data)
    {
        $this->db->query(
            "INSERT INTO " . DB_DATABASE . ".followings " .
            " SET name='" . $this->db->escape($card_data['name']) . "', " .
            " url='" . $this->db->escape($card_data['url']) . "', " .
            " photo='" . $this->db->escape($card_data['photo']) . "'"
        );
        $this->cache->delete('followings');
        return $this->db->getLastId();
    }

    public function unfollowCard($card_url)
    {
        $this->db->query(
            "UPDATE " . DB_DATABASE . ".followings " .
            " SET deleted=1 " .
            " WHERE url='" . $this->db->escape($card_url) . "' " .
            " LIMIT 1"
        );
        $this->cache->delete('followings');
    }

    public function getFollowings()
    {
        $followings = $this->cache->get('followings');
        if (!$followings) {
            $query = $this->db->query(
                "SELECT * " .
                " FROM " . DB_DATABASE . ".followings " .
                " WHERE deleted=0"
            );
            $followings = $query->rows;
            $this->cache->set('followings', $followings);
        }
        return $followings;
    }

    public function getFollowing($following_id)
    {
        $following = $this->cache->get('followings.id.' . $following_id);
        if (!$following) {
            $query = $this->db->query(
                "SELECT * " .
                " FROM " . DB_DATABASE . ".followings " .
                " WHERE following_id=" . (int)$following_id . " " .
                " LIMIT 1"
            );
            $following = $query->row;
            $this->cache->set('followings.id.' . $following_id, $following);
        }
        return $following;
    }

}
