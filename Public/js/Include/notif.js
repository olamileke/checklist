
var $notifbell=$('.fa-bell');
var $notifmsg=$('#before_notif');
var $loader_cont=$('.mini_cont');
var $loader=$loader_cont.find('.lds-ring div');


// TOGGLING THE NOTIFICATION BELL

$notifbell.click(function() {

	$notifbell.removeClass('success');
	$notifbell.removeClass('error');
	$notifmsg.addClass('animateup');

	setTimeout(function() {
		$notifmsg.fadeOut(100);

		setTimeout(function() {
			$notifmsg.removeClass('animateup');
		},400)
	},400)
})

// CHANGING THE COLOR OF THE LOADER ICON

function changeLoaderColor()
{
	setInterval(function() {

		var $color='#'+(Math.random() * 0xFFFFFF<<0).toString(16);
		$loader.css('borderColor',$color+' transparent transparent transparent')
	},1000)
}

function showLoader()
{
	$loader_cont.show();
	changeLoaderColor();
}

function removeLoader($msg)
{
	$loader_cont.hide();
	$notifbell.addClass('success');

	setTimeout(function(){
		$notifmsg.fadeIn(1000).addClass('success').text($msg);
	},1300)
}