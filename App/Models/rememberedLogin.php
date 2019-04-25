<?php

	namespace App\Models;

	use PDO;

	use \App\Token;

	use App\Models\Users;

	class rememberedLogin extends \Core\Model
	{
		public static function confirmRememberToken($tokenz)
		{
			$token=new Token($tokenz);

			$hashed_token=$token->getHash();

			$db=static::getDB();

			$sql="SELECT * FROM rememberedlogins WHERE remembered_token=:hashed_token";

			$stmt=$db->prepare($sql);

			$stmt->bindValue(':hashed_token', $hashed_token, PDO::PARAM_STR);

			$stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

			$stmt->execute();

			return $stmt->fetch();
		}

		public function getUser()
		{
			return Users::findUserByID($this->user_id);
		}

		public function deleteCookie()
		{
			$db=static::getDB();

			$sql="DELETE FROM rememberedlogins WHERE remembered_token=:hashed_token";

			$stmt=$db->prepare($sql);

			$stmt->bindValue(':hashed_token', $this->remembered_token, PDO::PARAM_STR);

			$stmt->execute();

			return true;
		}
	}




?>