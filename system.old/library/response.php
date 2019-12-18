<?php
class Response {
    private $headers = array();
    private $level = 0;
    private $output;

    public function addHeader($header, $replace = true)
    {
        $this->headers[] = array($header, $replace);
    }

    public function resetHeaders()
    {
        $this->headers = array();
    }

    public function redirect($url, $status = 302)
    {
        header('Status: ' . $status);
        header('Location: ' . str_replace(array('&amp;', "\n", "\r"), array('&', '', ''), $url));
        exit();
    }

    public function setCompression($level)
    {
        $this->level = $level;
    }

    public function setOutput($output)
    {
        $this->output = $output;
    }

    private function compress($data, $level = 0)
    {
        if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false)) {
            $encoding = 'gzip';
        }

        if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'x-gzip') !== false)) {
            $encoding = 'x-gzip';
        }

        if (!isset($encoding)) {
            return $data;
        }

        if (!extension_loaded('zlib') || ini_get('zlib.output_compression')) {
            return $data;
        }

        if (headers_sent()) {
            return $data;
        }

        if (connection_status()) {
            return $data;
        }

        $this->addHeader('Content-Encoding: ' . $encoding);

        return gzencode($data, (int)$level);
    }

    public function output()
    {
        if ($this->output) {
            if ($this->level) {
                $output = $this->compress($this->output, $this->level);
            } else {
                $output = $this->output;
            }

            if (!headers_sent()) {
                foreach ($this->headers as $headerArray) {
                    header($headerArray[0], $headerArray[1]);
                }
            }

            echo $output;
        }
    }
}