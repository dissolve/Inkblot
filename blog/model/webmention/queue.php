<?php
class ModelWebmentionQueue extends Model {
    public function addEntry($source, $target, $vouch = null, $status_code = '202')
    {
        //find if this is an update
        $query = $this->db->query("
            SELECT * FROM " . DB_DATABASE . ".webmentions " .
            " WHERE source_url='" . $this->db->escape($source) . "' " .
            " AND target_url='" . $this->db->escape($target) . "'");
        $results = $query->row;
        if (empty($results)) {
            $this->db->query(
                "INSERT INTO " . DB_DATABASE . ".webmentions " .
                " SET source_url='" . $this->db->escape($source) . "', " .
                " target_url='" . $this->db->escape($target) . "', " .
                " `created_at` = NOW(), " .
                " status_code='" . $status_code . "', " .
                " status='queued'" . ($vouch ? ", " .
                " vouch_url='" . $this->db->escape($vouch) . "'" : "")
            );
            $id = $this->db->getLastId();
            return $id;
        } else {
            //this is an update or delete
            $this->db->query(
                "UPDATE " . DB_DATABASE . ".webmentions " .
                " SET status_code='" . $status_code . "', " .
                " status = 'queued'" . ($vouch ? ", " .
                " vouch_url='" . $this->db->escape($vouch) . "'" : "") . " " .
                " WHERE id = '" . (int)$results['id'] . "'"
            );
            return $results['id'];

        }
    }

    public function getEntry($id)
    {
        $res = $this->db->query(
            "SELECT * FROM " . DB_DATABASE . ".webmentions " .
            " WHERE id = '" . (int)$id . "'"
        );
        return $res->row;
    }

    public function setCallback($id, $callback_url)
    {
        $res = $this->db->query(
            "UPDATE " . DB_DATABASE . ".webmentions " .
            " set callback_url='" . $this->db->escape($callback_url) . "' " .
            " WHERE id = '" . (int)$id . "'"
        );
        return $res->row;
    }

    public function getUnhandledWebmentions()
    {
        $res = $this->db->query(
            "SELECT * FROM " . DB_DATABASE . ".webmentions " .
            " WHERE status_code != 200 " .
            " AND status_code != 410 " .
            " AND (admin_op != 'dismiss' OR admin_op is NULL)"
        );
        return $res->rows;
    }
    public function getUnhandledWebmentionCount()
    {
        $res = $this->db->query(
            "SELECT count(*) as count FROM " . DB_DATABASE . ".webmentions " .
            " WHERE status_code != 200 " .
            " AND status_code != 410 " .
            " AND (admin_op != 'dismiss' OR admin_op is NULL)"
        );
        return $res->row['count'];
    }

    public function dismiss($id)
    {
        $res = $this->db->query(
            "SELECT * FROM " . DB_DATABASE . ".webmentions " .
            " WHERE id = '" . (int)$id . "'"
        );
        if ($res->row['status_code'] != 200) {
            $this->db->query(
                "UPDATE " . DB_DATABASE . ".webmentions " .
                " SET admin_op = 'dismiss' " .
                " WHERE id = " . (int)$id
            );
        }
    }
    public function retry($id)
    {
        $res = $this->db->query(
            "SELECT * FROM " . DB_DATABASE . ".webmentions " .
            " WHERE id = '" . (int)$id . "'"
        );
        if ($res->row['status_code'] != 200) {
            $this->db->query(
                "UPDATE " . DB_DATABASE . ".webmentions " .
                " SET status_code = 202, " .
                " status='retry' " .
                " WHERE id = " . (int)$id
            );
        }
    }
    public function whitelistAndRetry($id)
    {
        $res = $this->db->query(
            "SELECT * FROM " . DB_DATABASE . ".webmentions " .
            " WHERE id = '" . (int)$id . "'"
        );
        if ($res->row['status_code'] != 200) {
            $res = $this->db->query(
                "SELECT * FROM " . DB_DATABASE . ".webmentions " .
                " WHERE id = '" . (int)$id . "'"
            );

            $this->load->model('webmention/vouch');
            $this->model_webmention_vouch->addWhitelistEntry($res->row['source_url']);

            $this->db->query(
                "UPDATE " . DB_DATABASE . ".webmentions " .
                " SET status_code = 202, " .
                " status='retry' " .
                " WHERE id = " . (int)$id
            );
        }
    }

}
