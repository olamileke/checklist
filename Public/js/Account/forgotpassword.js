"use strict"

$(document).ready(function() {

	var $form=$('form');

	var $email=$form.find('input[type="email"]');


	// CUSTOM VALIDATE METHOD TO VALIDATE THE EMAIL FIELD

	function validate()
	{
		if(!$email.val())
		{
			$email.css('border', '1px solid red');
			return false;
		}

		return true;
	}


	$email.keyup(function() {

		var $this=$(this);

		if(!$this.val())
		{
			$this.css('border', '1px solid red');
		}
		else
		{
			$this.css('border', '1px solid #FFF');
		}

	})

	$form.submit(function(e) {

		if(!validate())
		{			
			e.preventDefault();
		}
	})
})