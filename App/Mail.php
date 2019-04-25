<?php

	namespace App;

	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;

	use \App\Config;

	require 'vendor/autoload.php';

	class Mail
	{
		public static function send($address, $name, $subject, $msghtml, $msgplain, $img=null, $imgcid=null)
		{
			$mail= new PHPMailer(TRUE);

			try
			{
				$mail->setFrom('dont-reply@checklist.com', 'Checklist');

				$mail->addAddress($address, $name);

				$mail->Subject=$subject;

				$mail->isHTML(true);

				if(!is_null($img) && !is_null($imgcid))
				{
					$mail->AddEmbeddedImage($img, $imgcid);
				}

				$mail->Body=$msghtml;

				$mail->AltBody=$msgplain;				

				$mail->isSMTP();

				$mail->SMTPAutoTLS = false;

				$mail->Host=Config::SMTP_SERVER;

				$mail->SMTPAuth = TRUE;

				$mail->SMTPSecure='tls';

				$mail->Username=Config::SMTP_USERNAME;

				$mail->Password=Config::SMTP_PASSWORD;

				$mail->Port=465;

				$mail->send();

				return true;
			}
			catch (Exception $e)
			{
			   /* PHPMailer exception. */
			   echo $e->errorMessage();
			}
		    catch (\Exception $e)
			{
			   /* PHP exception (note the backslash to select the global namespace Exception class). */
			   echo $e->getMessage();
			}
		}
	}





?>