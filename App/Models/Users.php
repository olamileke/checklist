<?php

	namespace App\Models;

	use PDO;

	use \App\Token;

	use \App\Auth;

	class Users extends \Core\Model
	{
		public $errors=[];

		public $route;

		public $image;

		public $numproject;

		public $numcprojects;

		public $numtasks=0;

		public $numctasks=0;

		public $tprojects;

		public $projects;

		protected $token_val;

		public $settings;

		public $token;

		public $time_expiry;

		public function __construct($data=[])
		{
			// CONSTRUCTING PROPERTIES FROM THE SIGNUP DATA

			foreach($data as $key => $value)
			{
				$this->$key=$value;
			}
		}

		public function save()
		{
			$this->validate();

			if(empty($this->errors))
			{
				// SAVING THE NEW USER DATA

				$db=static::getDB();

				$password_hash=password_hash($this->password, PASSWORD_DEFAULT);

				$token=new Token();

				$token_hash=$token->getHash();

				$this->token_val=$token->getToken();

				$sql="INSERT INTO users(fname, lname, email, password, activation_token) VALUES(:fname, :lname, :email, :password, :token)";

				$stmt=$db->prepare($sql);

				$stmt->bindValue(':fname', ucfirst($this->fname), PDO::PARAM_STR);

				$stmt->bindValue(':lname', ucfirst($this->lname), PDO::PARAM_STR);

				$stmt->bindValue(':email', $this->email, PDO::PARAM_STR);

				$stmt->bindValue(':password', $password_hash, PDO::PARAM_STR);

				$stmt->bindValue(':token', $token_hash, PDO::PARAM_STR);

				$stmt->execute();

				return true;
			}
			else
			{
				return false;
			}
		}


		public function validate()
		{
			// VALIDATING THE SIGNUP FORM DATA

			if(empty($this->fname))
			{
				$this->errors[]='Please fill the First Name field';
			}

			if(empty($this->lname))
			{
				$this->errors[]='Please fill the Last Name field';
			}

			if(empty($this->email))
			{
				$this->errors[]='Please fill the Email field';
			}

			if(empty($this->password))
			{
				$this->errors[]='Please fill out the Password field';
			}
		}

		public static function returnLastUser()
		{
			$db=static::getDB();

			$sql="SELECT id, lname, email FROM users ORDER BY id DESC LIMIT 1";

			$stmt=$db->prepare($sql);

			$stmt->execute();

			return $stmt->fetch();
		}

		public static function findUserByEmail($email)
		{
			$db=static::getDB();

			$sql="SELECT * FROM users WHERE email=:email";

			$stmt=$db->prepare($sql);

			$stmt->bindValue('email', $email, PDO::PARAM_STR);

			$stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

			$stmt->execute();

			return $stmt->fetch();
		}
		

		public static function findUserByID($id)
		{
			$db=static::getDB();

			$sql="SELECT * FROM users WHERE id=:id";

			$stmt=$db->prepare($sql);

			$stmt->bindValue(':id', $id, PDO::PARAM_INT);

			$stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

			$stmt->execute();

			return $stmt->fetch();
		}

		public static function findUserByActivateToken($tokenz)
		{
			$token=new Token($tokenz);

			$hash_token=$token->getHash();

			$db=static::getDB();

			$sql="SELECT * FROM users WHERE activation_token=:hash_token";

			$stmt=$db->prepare($sql);

			$stmt->bindValue(':hash_token', $hash_token, PDO::PARAM_STR);

			$stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

			$stmt->execute();

			return $stmt->fetch();
		}


		public static function deleteActivateToken()
		{
			$user=Auth::getUser();

			if($user)
			{
				$db=static::getDB();

				$sql="UPDATE users SET activation_token=null WHERE id=:id";

				$stmt=$db->prepare($sql);

				$stmt->bindValue(':id', $user->id, PDO::PARAM_INT);

				$stmt->execute();

				return true;
			}
		}

		public static function authenticate($email, $password)
		{
			$user=static::findUserByEmail($email);

			if($user)
			{
				if(password_verify($password, $user->password))
				{
					if(static::checkActivated($user->id))
					{
						return $user;
					}

					return false;
				}
			}

			return false;
		} 

		public function getToken()
		{
			return $this->token_val;
		}

		public static function checkToken($tokenz)
		{
			$token=new Token($tokenz);

			$token_hash=$token->getHash();

			$db=static::getDB();

			$sql="SELECT * FROM users WHERE activation_token=:token_hash";

			$stmt=$db->prepare($sql);

			$stmt->bindValue(':token_hash', $token_hash, PDO::PARAM_STR);

			$stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

			$stmt->execute();

			$user=$stmt->fetch();

			if($user)
			{
				if(static::activateAccount($user->id))
				{
					return true;
				}
			}
		}

		public static function checkActivated($id)
		{
			$db=static::getDB();

			$sql="SELECT is_activated FROM users WHERE id=:id";

			$stmt=$db->prepare($sql);

			$stmt->bindValue(':id', $id, PDO::PARAM_INT);

			$stmt->execute();

			if($stmt->fetch()[0] == 1)
			{
				return true;
			}

			return false;
		}

		public static function activateAccount($id)
		{
			$db=static::getDB();

			$sql="UPDATE users SET is_activated=1 WHERE id=:id";

			$stmt=$db->prepare($sql);

			$stmt->bindValue(':id', $id, PDO::PARAM_INT);

			$stmt->execute();

			return true;
		}

		public function setPasswordResetToken($hash_token)
		{
			$db=static::getDB();

			$sql="UPDATE users SET password_reset_token=:hash_token WHERE id=:id";

			$stmt=$db->prepare($sql);

			$stmt->bindValue('hash_token', $hash_token, PDO::PARAM_STR);

			$stmt->bindValue('id', $this->id, PDO::PARAM_INT);

			$stmt->execute();

			return true;
		}

		public static function confirm_reset_token($hashed_token)
		{
			$db=static::getDB();

			$sql="SELECT * FROM users WHERE password_reset_token=:hashed_token";

			$stmt=$db->prepare($sql);

			$stmt->bindValue(':hashed_token', $hashed_token, PDO::PARAM_STR);

			$stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

			$stmt->execute();

			return $stmt->fetch();
		}

		public static function changePassword($id, $password)
		{
			$db=static::getDB();

			$sql="UPDATE users SET password=:pword WHERE id=:id";

			$stmt=$db->prepare($sql);

			$password_hash=password_hash($password, PASSWORD_DEFAULT);

			$stmt->bindValue(':id', $id, PDO::PARAM_INT);

			$stmt->bindValue(':pword', $password_hash, PDO::PARAM_STR);

			$stmt->execute();

			return static::reset_password_token($id);
		}

		public static function reset_password_token($id)
		{
			$db=static::getDB();

			$sql="UPDATE users SET password_reset_token=null WHERE id=:id";

			$stmt=$db->prepare($sql);

			$stmt->bindValue(':id', $id, PDO::PARAM_INT);

			$stmt->execute();

			return true;
		}

		public function rememberLogin()
		{
			$token=new Token();

			$this->token=$token->getToken();

			$hashed_token=$token->getHash();

			$this->time_expiry=time() + (30 * 24 * 60 * 60);

			$db=static::getDB();

			$sql="INSERT INTO rememberedlogins(remembered_token,user_id,time_expiry) VALUES(:hash_token, :u_id, :time_exp)";

			$stmt=$db->prepare($sql);

			$stmt->bindValue(':hash_token', $hashed_token, PDO::PARAM_STR);

			$stmt->bindValue(':u_id', $this->id, PDO::PARAM_INT);

			$stmt->bindValue(':time_exp', date('Y-m-d h:i:s', $this->time_expiry), PDO::PARAM_STR);

			$stmt->execute();

			return true;
		}

		public static function getAllUsers()
		{
			$db=static::getDB();

			$sql="SELECT * FROM users";

			$stmt=$db->prepare($sql);

			$stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

			$stmt->execute();

			return $stmt->fetchAll();

		}

		public function getLastID()
		{
			$db=static::getDB();

			$sql="SELECT id FROM users ORDER BY id DESC LIMIT 1";

			$stmt=$db->prepare($sql);

			$stmt->execute();

			return $stmt->fetch()[0];
		}
	}



?>