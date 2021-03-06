<?php

class AuthenticationQueries {

	private $tables		= array(
								'users'				=> 'users',
								'user_sessions'		=> 'user_sessions'
								);
	
	public $queries;

	function __construct()
	{
		$this->queries 	= array(
									'createUser'		=>	"INSERT INTO {$this->tables['users']} (`user_email`, `password_hash`)
															VALUES(:user_email, :password_hash)",

									'getPasswordHash'	=>	"SELECT `password_hash` FROM {$this->tables['users']} 
															 WHERE `user_email` = :user_email ",

									'getUserId'			=>	"SELECT `id` FROM {$this->tables['users']}
															WHERE `user_email` = :user_email",

									'saveSession'		=>	"INSERT INTO {$this->tables['user_sessions']} (`owner`, `session_token`)
															VALUES(:id, :session_token)",

									'updateEmail'		=>	"UPDATE `{$this->tables['users']}`
															SET `user_email` = :new_user_email
															WHERE `id` = :id",

									'session_expired'	=>	"SELECT `timestamp` FROM `{$this->tables['user_sessions']}`
															WHERE `session_token` = :session_token
															AND `owner` = :user_id",

									'makeAdmin'			=>	"UPDATE `{$this->tables['users']}`
															SET `admin` = '1'
															WHERE `id` = :id ",

									'revokeAdmin'		=>	"UPDATE `{$this->tables['users']}`
															SET `admin` = '0'
															WHERE `id` = :id "
								);
	}
}