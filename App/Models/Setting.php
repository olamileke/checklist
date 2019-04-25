<?php

	namespace App\Models;

	use PDO;

	class Setting extends \Core\Model
	{

		public static function create($id)
		{
			$db=static::getDB();

			$sql="INSERT INTO settings(user_id) VALUES(:u_id)";

			$stmt=$db->prepare($sql);

			$stmt->bindValue(':u_id', $id, PDO::PARAM_INT);

			$stmt->execute();

			return true;
		}

		public static function changeField($id, $var)
		{
			$db=static::getDB();

			$sql="UPDATE settings SET $var=:value WHERE user_id=:u_id";

			$stmt=$db->prepare($sql);

			$stmt->bindValue('value', !static::getField($id, $var), PDO::PARAM_INT);

			$stmt->bindValue('u_id', $id, PDO::PARAM_INT);

			$stmt->execute();

			return true;
		}


		public static function getField($id, $var)
		{
			$db=static::getDB();

			$sql="SELECT $var FROM settings WHERE user_id=:u_id";

			$stmt=$db->prepare($sql);

			$stmt->bindValue('u_id', $id, PDO::PARAM_INT);

			$stmt->execute();

			return $stmt->fetch()[0];
		}


		public static function getSettings($id)
		{
			$db=static::getDB();

			$sql="SELECT * FROM settings WHERE user_id=:u_id";

			$stmt=$db->prepare($sql);

			$stmt->bindValue('u_id', $id, PDO::PARAM_INT);

			$stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

			$stmt->execute();

			return $stmt->fetch();
		}
	}






?>