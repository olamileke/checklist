"use strict"

$(document).ready(function() {

	var $host=location.hostname;

	var $switchbtn=$('.switch');

	$switchbtn.click(function() {

		var $this=$(this);

		$this.toggleClass('switchOn');

		var $num=$this.parent().find('span').text();

		$.ajax('http://'+$host+'/settings/change', {
			type:'POST',
			dataType:'text',
			data:{
				'num':$num
			}
		})
	})
})