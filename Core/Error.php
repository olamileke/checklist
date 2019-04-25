<?php

	namespace Core;

	use \App\Config;

	class Error
	{
		public static function errorHandler($level, $message, $file, $line)
		{
			throw new \ErrorException($message,0,$level,$file,$line);
		}

		public static function exceptionHandler($exception)
		{
			$code=$exception->getCode();

			if($code != 404)
			{
				$code=500;
			}

			$errorfile=dirname(__DIR__).'/'.'logs/'.date('Y-m-d').'.txt';

			ini_set('error_log', $errorfile);

			if(Config::SHW_ERRORS)
			{
				$message="<h2>Fatal Error</h2>";
				$message.="<p>Uncaught Exception</p>";
				$message.="<p>Message:".$exception->getMessage()."</p>";
				$message.="<p>Stack Trace:".$exception->getTraceAsString()."</p>";
				$message.="<p>Found in ".$exception->getFile()." on line ".$exception->getLine()."</p>";

				echo $message;
			}
			else
			{
				$message="<h2>Fatal Error</h2>";
				$message.="<p>Uncaught Exception</p>";
				$message.="<p>Message:".$exception->getMessage()."</p>";
				$message.="<p>Stack Trace:".$exception->getTraceAsString()."</p>";
				$message.="<p>Found in ".$exception->getFile()." on line ".$exception->getLine()."</p>";

				error_log($message);

				if($code == 404)
				{					
					echo '<h2>Error 404</h2><p>The Requested file cannot be found</p>';
				}
			}
		}
	}


?>