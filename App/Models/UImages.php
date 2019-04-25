<?php

	namespace App\Models;

	use PDO;

	class UImages extends \Core\Model
	{
		public static function getImage($id)
		{
			$db=static::getDB();

			$sql="SELECT image FROM user_images WHERE user_id=:u_id";

			$stmt=$db->prepare($sql);

			$stmt->bindValue(':u_id', $id, PDO::PARAM_INT);

			$stmt->execute();

			return $stmt->fetch();
		}

		public static function changeImage($id, $img, $hasImg)
		{
			$db=static::getDB();

			if($hasImg == 'false')
			{
				$sql="INSERT INTO user_images(image,user_id) VALUES(:image, :u_id)";
			}
			else
			{
				$sql="UPDATE user_images SET image=:image WHERE user_id=:u_id";
			}

			$stmt=$db->prepare($sql);

			$stmt->bindValue(':image', $img, PDO::PARAM_STR);

			$stmt->bindValue(':u_id', $id, PDO::PARAM_INT);

			$stmt->execute();

			return true;
		}
	}



?>