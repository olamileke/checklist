<?php

	namespace App\Models;

	use PDO;

	class Images extends \Core\Model
	{
		public static function save($imgname, $id)
		{
			$db=static::getDB();

			$sql="INSERT INTO project_images(image, image_id) VALUES(:image, :img_id)";

			$stmt=$db->prepare($sql);

			$stmt->bindValue(':image', $imgname, PDO::PARAM_STR);

			$stmt->bindValue(':img_id', $id, PDO::PARAM_INT);

			$stmt->execute();

			return true;
		}

		public static function getImage($id)
		{
			$db=static::getDB();

			$sql="SELECT image FROM project_images WHERE image_id=:id";

			$stmt=$db->prepare($sql);

			$stmt->bindValue(':id', $id, PDO::PARAM_INT);

			$stmt->execute();

			return $stmt->fetch();
		}
	}


?>