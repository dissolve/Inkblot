<?php
class Request {
    public $get = array();
    public $post = array();
    public $cookie = array();
    public $files = array();
    public $server = array();

    public function __construct()
    {
        //$_GET = $this->clean($this->proper_parse_str($_SERVER['QUERY_STRING']));
        //$_POST = $this->clean($this->proper_parse_str(file_get_contents("php://input")));
        //$_GET = $this->clean($_GET);
        //$_POST = $this->clean($_POST);
        $_REQUEST = $this->clean($_REQUEST);
        $_COOKIE = $this->clean($_COOKIE);
        $_FILES = $this->clean($_FILES);
        $_SERVER = $this->clean($_SERVER);

        $this->get = $_GET;
        $this->post = $_POST;
        $this->request = $_REQUEST;
        $this->cookie = $_COOKIE;
        $this->files = $_FILES;
        $this->server = $_SERVER;
    }

    /*
    //lets force PHP to behave like standard PHP parsing
    public function proper_parse_str($str) {
      # result array
      $arr = array();

      # split on outer delimiter
      $pairs = explode('&', $str);

      # loop through each pair
      foreach ($pairs as $i) {
        # split into name and value
        list($name,$value) = explode('=', $i, 2);

        # if name already exists
        if( isset($arr[$name]) ) {
          # stick multiple values into an array
          if( is_array($arr[$name]) ) {
            $arr[$name][] = urldecode($value);
          }
          else {
                $arr[$name] = array($arr[$name], urldecode($value));
          }
        }
        # otherwise, simply stick it in a scalar
        else {
          $arr[$name] = urldecode($value);
        }
      }

      # return result array
      return $arr;
    }
     */



    public function clean($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                unset($data[$key]);

                $data[$this->clean($key)] = $this->clean($value);
            }
        } else {
            $data = htmlspecialchars($data, ENT_COMPAT, 'UTF-8');
        }

        return $data;
    }
}
