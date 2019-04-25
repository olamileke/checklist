"use strict"

$(document).ready(function() {

	var $host=location.hostname;

	var $roundupbtn=$('header button');

	$roundupbtn.click(function() {

		showLoader();
		
		$.ajax('http://'+$host+'/project/sendroundup',{
			type:'POST',
			dataType:'text',
			success:function(data) {
				removeLoader('Roundup emails succesfully sent');
			}
		})
		
	})

})