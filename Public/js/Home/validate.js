"use strict"

$(document).ready(function() {
	var $form=$('form');

	var $elements=$('.elements');

	function validate()
	{
		var $count=0;

		$elements.each(function() {

			var $this=$(this);

			if(!$this.val())
			{
				$this.css('borderBottom', '1px solid #FF4D4D');
				$count++;
			}
		})

		if($count > 0)
		{
			return false;
		}

		return true;
	}

	$elements.keyup(function() {

		var $this=$(this);

		if(!$this.val())
		{
			$this.css('borderBottom', '1px solid #FF4D4D');
		}
		else
		{
			$this.css('borderBottom', '1px solid rgba(0,0,0,0.3)');
		}
	})


	$form.submit(function(e) {

		if(!validate())
		{
			e.preventDefault();
		}
	})
})