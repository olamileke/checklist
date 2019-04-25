<?php

	namespace App;

	use App\Config;

	class Token
	{
		protected $token;

		public function __construct($token_val=null)
		{
			if(is_null($token_val))
			{
				$this->token=bin2hex(openssl_random_pseudo_bytes(16));
			}
			else
			{
				$this->token=$token_val;
			}
		}

		public function getToken()
		{
			return $this->token;
		}

		public function getHash()
		{
			return hash_hmac('sha256', $this->token, Config::SECRET_KEY);
		}
	}




?>