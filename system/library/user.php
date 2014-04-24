<?php
class User {
	private $user_id;
	private $username;
	private $permission = array();

	public function __construct($registry) {
		$this->db = $registry->get('db');
		$this->request = $registry->get('request');
		$this->session = $registry->get('session');

		if (isset($this->session->data['user_id'])) {
			$this->user_id = 1; //$user_query->row['user_id'];
			$this->username = ADMIN_USER; //$user_query->row['username'];			
		}
	}

	public function login($username, $password) {
        if($username == ADMIN_USER && $password == ADMIN_PASSWORD){
			$this->session->data['user_id'] = 1;//$user_query->row['user_id'];

			$this->user_id = 1; //$user_query->row['user_id'];
			$this->username = $username; //$user_query->row['username'];			


			return true;
		} else {
			return false;
		}
	}

	public function logout() {
		unset($this->session->data['user_id']);

		$this->user_id = '';
		$this->username = '';
	}

	public function hasPermission($key, $value) {
		if (isset($this->permission[$key])) {
			return in_array($value, $this->permission[$key]);
		} else {
			return false;
		}
	}

	public function isLogged() {
		return $this->user_id;
	}

	public function getId() {
		return $this->user_id;
	}

	public function getUserName() {
		return $this->username;
	}
}
