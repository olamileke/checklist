"use strict"

$(document).ready(function() {

	var $loginform=$('form');

	var $elements=$('.elements');


	// CUSTOM VALIDATION METHOD

	function validate()
	{

		var $count=0;

		$elements.each(function() {
			var $this=$(this);

			if(!$this.val())
			{
				$this.css('borderBottom', '1px solid #FF4242');
				$count++;
			}
		})

		if($count > 0)
		{
			return false;
		}

		return true;
	}

	// SIMPLE JS VALIDATION OF THE EMAIL AND PASSWORD FIELDS

	$elements.keyup(function() {
		var $this=$(this);

		if(!$this.val())
		{
			$this.css('borderBottom', '1px solid #FF4242');
		}
		else
		{
			$this.css('borderBottom', '1px solid #FFF');
		}
	})

	$loginform.submit(function(e) {

		if(!validate())
		{
			e.preventDefault();
		}
	})	
})