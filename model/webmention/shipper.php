<?php
class ModelWebmentionShipper extends Model {


    private function getWebmentionURL($url)
    {
        require_once(DIR_BASE . 'libraries/php-mf2/Mf2/Parser.php');
        $c = curl_init();
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_URL, $url);
        curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($c, CURLOPT_MAXREDIRS, 20);
        $page_content = curl_exec($c);
        curl_close($c);
        unset($c);

        $parsed = Mf2\parse($page_content);

        if (isset($parsed['rels'])) {
            if (isset($parsed['rels']['webmention']) && isset($parsed['rels']['webmention'][0])) {
                return $parsed['rels']['webmention'][0];
            } elseif (isset($parsed['rels']['http://webmention.org/']) && isset($parsed['rels']['http://webmention.org/'][0])) {
                return $parsed['rels']['http://webmention.org/'][0];
            }
        } else {
            return null;
        }
    }

    public function sendMention($my_permalink, $target_url)
    {
        require_once(DIR_BASE . 'libraries/php-mf2/Mf2/Parser.php');
        $webmention_handler = $this->getWebmentionURL($target_url);
        if ($webmention_handler) {
            //set data
            $data = 'source=' . urlencode($my_permalink);
            $data .= '&target=' . urlencode($target_url);
            //$data .= '&callback=' .urlencode($callback_url); //not implemented

            $c = curl_init();
            curl_setopt($c, CURLOPT_URL, $webmention_handler);
            curl_setopt($c, CURLOPT_POST, true);
            curl_setopt($c, CURLOPT_POSTFIELDS, $fields_string);
            curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($c, CURLOPT_MAXREDIRS, 20);
            curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($c, CURLOPT_HEADER, true);
            curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($c, CURLOPT_TIMEOUT, 10);

            $body = curl_exec($c);
            $header = curl_getinfo($c);

            curl_close($c);
            unset($c);
            return curl_getinfo($c, CURLINFO_HTTP_CODE);
        } else {
            return false;
        }

    }
}
