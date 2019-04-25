<?php

	namespace App\Models;

	use PDO;

	class Tasks extends \Core\Model
	{
		public static function save($name, $id)
		{
			$db=static::getDB();

			$sql="INSERT INTO TASKS(name, project_id) VALUES(:name, :p_id)";

			$stmt=$db->prepare($sql);

			$stmt->bindValue(':name', $name, PDO::PARAM_STR);

			$stmt->bindValue(':p_id', $id, PDO::PARAM_INT);

			$stmt->execute();

			return true;
		}

		public static function getTasks($id)
		{
			$db=static::getDB();

			$sql="SELECT * FROM tasks WHERE project_id=:id";

			$stmt=$db->prepare($sql);

			$stmt->bindValue(':id', $id, PDO::PARAM_INT);

			$stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

			$stmt->execute();

			return $stmt->fetchAll();
		}

		public static function getCountTasks($id)
		{
			$db=static::getDB();

			$sql="SELECT count(*) FROM tasks WHERE project_id=:id";

			$stmt=$db->prepare($sql);

			$stmt->bindValue(':id', $id, PDO::PARAM_INT);

			$stmt->execute();

			return $stmt->fetch();
		}

		public static function getCountCompletedTasks($id)
		{
			$db=static::getDB();

			$sql="SELECT count(*) FROM tasks WHERE project_id=:id AND is_done=1";

			$stmt=$db->prepare($sql);

			$stmt->bindValue(':id', $id, PDO::PARAM_INT);

			$stmt->execute();

			return $stmt->fetch();
		}

		public static function updateTasks($status, $id)
		{
			$db=static::getDB();

			$sql="UPDATE tasks SET is_done=:status WHERE id=:id";

			$stmt=$db->prepare($sql);

			$stmt->bindValue(':status', $status, PDO::PARAM_INT);

			$stmt->bindValue(':id', $id, PDO::PARAM_INT);

			$stmt->execute();

			return true;
		}
	}





?>