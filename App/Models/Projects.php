<?php
	
	namespace App\Models;

	use PDO;

	class Projects extends \Core\Model
	{
		public $image;

		public $numtasks;

		public $numcompletedtasks=0;

		public $uncompletedtasks;

		public $url;

		public $tasks;

		public $percent;

		public function __construct($data=[])
		{
			foreach($data as $key => $value)
			{
				$this->$key=$value;
			}
		}

		public function save($user_id)
		{
			$db=static::getDB();

			$sql="INSERT INTO projects(name, user_id, date_due, time_due) VALUES(:name, :user_id ,:datez, :timez)";

			$stmt=$db->prepare($sql);

			$stmt->bindValue(':name', $this->projectname, PDO::PARAM_STR);

			$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);

			$stmt->bindValue(':datez', $this->date_due, PDO::PARAM_STR);

			$stmt->bindValue(':timez', $this->time_due, PDO::PARAM_STR);

			$stmt->execute();

			return true;
		}

		public function returnLastID()
		{
			$db=static::getDB();

			$sql="SELECT id FROM projects ORDER BY id DESC LIMIT 1";

			$stmt=$db->prepare($sql);

			$stmt->execute();

			return $stmt->fetch();
		}

		public static function getProjects($id, $start, $end)
		{
			$db=static::getDB();

			$sql="SELECT * FROM projects WHERE user_id=:id ORDER BY id DESC LIMIT :start, :endz";

			$stmt=$db->prepare($sql);

			$stmt->bindValue(':id', $id, PDO::PARAM_INT);

			$stmt->bindValue(':start', $start, PDO::PARAM_INT);

			$stmt->bindValue(':endz', $end, PDO::PARAM_INT);

			$stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

			$stmt->execute();

			return $stmt->fetchAll();
		}

		public static function getAllProjects($id)
		{
			$db=static::getDB();

			$sql="SELECT * FROM projects WHERE user_id=:id ORDER BY id DESC";

			$stmt=$db->prepare($sql);

			$stmt->bindValue(':id', $id, PDO::PARAM_INT);

			$stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

			$stmt->execute();

			return $stmt->fetchAll();
		}

		public static function getAvailableProjects($id)
		{
			$db=static::getDB();

			$sql="SELECT date_due, time_due,id FROM projects WHERE user_id=:id AND is_completed=0 AND is_expired=0";

			$stmt=$db->prepare($sql);

			$stmt->bindValue(':id', $id, PDO::PARAM_INT);

			$stmt->execute();

			return $stmt->fetchAll();
		}

		public static function getAvailableProjectz($id)
		{
			$db=static::getDB();

			$sql="SELECT * FROM projects WHERE user_id=:id AND is_completed=0 AND is_expired=0";

			$stmt=$db->prepare($sql);

			$stmt->bindValue(':id', $id, PDO::PARAM_INT);

			$stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

			$stmt->execute();

			return $stmt->fetchAll();
		}

		public static function getNumAllProjects($id)
		{
			$db=static::getDB();

			$sql="SELECT count(*) FROM projects WHERE user_id=:id";

			$stmt=$db->prepare($sql);

			$stmt->bindValue(':id', $id, PDO::PARAM_INT);

			$stmt->execute();

			return $stmt->fetch();
		}

		public static function getProject($id)
		{
			$db=static::getDB();

			$sql="SELECT * FROM projects WHERE id=:id";

			$stmt=$db->prepare($sql);

			$stmt->bindValue(':id', $id, PDO::PARAM_INT);

			$stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

			$stmt->execute();

			return $stmt->fetch();
		}

		public static function getNumUserProjects($id)
		{
			$db=static::getDB();

			$sql="SELECT count(*) FROM projects WHERE user_id=:id";

			$stmt=$db->prepare($sql);

			$stmt->bindValue(':id', $id, PDO::PARAM_INT);

			$stmt->execute();

			return $stmt->fetch();
		}

		public static function getNumUserCProjects($id)
		{
			$db=static::getDB();

			$sql="SELECT count(*) FROM projects WHERE user_id=:id AND is_completed=1";

			$stmt=$db->prepare($sql);

			$stmt->bindValue(':id', $id, PDO::PARAM_INT);

			$stmt->execute();

			return $stmt->fetch();	
		}

		public static function complete($id)
		{
			$db=static::getDB();

			$sql="UPDATE projects SET is_completed=1 WHERE id=:id";

			$stmt=$db->prepare($sql);

			$stmt->bindValue(':id', $id, PDO::PARAM_INT);

			$stmt->execute();

			return true;
		}

		public static function returnStatus($id)
		{
			$db=static::getDB();

			$sql="SELECT is_completed, is_expired FROM projects WHERE id=:id";

			$stmt=$db->prepare($sql);

			$stmt->bindValue(':id', $id, PDO::PARAM_INT);

			$stmt->execute();

			return $stmt->fetch();
		}

		public static function getTime($id)
		{
			$db=static::getDB();

			$sql="SELECT date_due, time_due FROM projects WHERE id=:id";

			$stmt=$db->prepare($sql);

			$stmt->bindValue(':id', $id, PDO::PARAM_INT);

			$stmt->execute();

			return $stmt->fetch();
		}

		public static function setExpired($id)
		{
			$db=static::getDB();

			$sql="UPDATE projects SET is_expired=1 WHERE id=:id";

			$stmt=$db->prepare($sql);

			$stmt->bindValue(':id', $id, PDO::PARAM_INT);

			$stmt->execute();

			return true;
		}
	}


?>