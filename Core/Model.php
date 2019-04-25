<?php

	namespace Core;

	use PDO;

	use \App\Config;

	abstract class Model
	{
		public static function getDB()
		{
			static $conn=null;

			if($conn === null)
			{
				try
				{
					$conn=new PDO("mysql:host=".Config::DBHOST.";dbname=".Config::DBNAME.";charset=utf8", Config::DBUSERNAME, Config::DBPASSWORD);

					return $conn;
				}
				catch(PDOException $e)
				{
					echo $e->getMessage();
				}
			}

			return $conn;
		}
	}
	



?>