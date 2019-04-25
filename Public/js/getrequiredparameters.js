
function setExpired($id)
{
	return $.ajax('http://'+$host+'/project/sendexpiredmail', {
		type:'POST',
		dataType:'text',
		data:{
			'id':$id,
			'date':getDate(),
			'time':getTime()
		},
	})
}

// Function to get the current date

function getDate()
{
	var $monthArray=['January', 'February', 'March', 'April', 'May', 'June',' July', 'August', 'September', 'October', 'November', 'December'];

	var $today=new Date();

	var $d=$today.getDate();

	var $m=$today.getMonth();

	var $y=$today.getFullYear();

	var $date= $d + ' ' + $monthArray[$m] + ' ' + $y;

	return $date;
}

// Function to get the current time

function getTime()
{
	var $today=new Date();

	var $h=$today.getHours();

	var $m=$today.getMinutes();

	var $s=$today.getSeconds();

	var $suffix;

	if($h > 12)
	{
		$h=$h - 12;
		$suffix='pm';
	}
	else
	{
		$suffix='am';
	}

	if($m.length == 1)
	{
		$m='0'+$m;
	}

	var $time=$h + ':' + $m + ' ' + $suffix;

	return $time;
}