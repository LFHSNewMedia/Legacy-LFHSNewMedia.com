function showMessage(type,header,message,link)
{
	$('#DialogOverlay').remove();
	$('body').append('<div id="DialogOverlay"><div id="DialogContainer" class="'+type+'Dialog"><div id="DialogMessage"><h1>'+header+'</h1>'+message+'</div></div></div>');
	if (typeof link == "undefined" || link != "")
	{
		$('#DialogMessage').append('<button id="DialogButton">Close</button>');
	}
	$('#DialogOverlay').fadeIn(200,function() {
		$('#DialogContainer').fadeIn(300,function() {
			$('#DialogButton').focus();
		});
	});
	if (typeof link == "undefined")
	{
		$('#DialogMessage button').click(function() {
			$('#DialogOverlay').fadeOut(300,function() {
				$('#DialogOverlay').remove();
			});
		});
	} else if (link != "") {
		$('#DialogMessage button').click(function() {
			window.location.href = link;
		});
	}
	Cufon.refresh();
}

function showError(header,message,link)
{
	return showMessage("error",header,message,link);
}

function showSuccess(header,message,link)
{
	return showMessage("success",header,message,link);
}

function showMsg(header,message,link)
{
	return showMessage("",header,message,link);
}

function checkFocus() {
	if ($(document.activeElement).is("input") || $(document.activeElement).is("textarea")) {
		//Something's selected
		return true;
	}
	return false;
}

var openingEditMenu = 0;
function openVideoOptions(identifier)
{
	if (openingEditMenu == 1) return;
	if ($('#OptionsMenu').length <= 0) {
		openingEditMenu = 1;
		$.ajax({
			url:'/admin/ajax_editvideo/'+identifier,
			success: function(data) 
			{
				$('body').append(data);
				openingEditMenu = 0;
			},
			error:function (xhr, ajaxOptions, thrownError) {
				showError("Ajax Processing Error","<p>An error occured while processing your request.</p><p>"+xhr.status+" : "+thrownError+"</p>");
			}
		});
	}
}

function showLogin()
{
	showMessage("","Log In Required",'<form id="authform" method="post" action="#" style="text-align:center;"><div id="error" style="color:#fff;"></div><input type="text" name="email" placeholder="E-Mail Address"><br><input type="password" name="password" placeholder="Password"><br><input type="submit" style="width:128px;font-size:14px;padding:4px;" value="Log In"><br><a id="registerButton" href="javascript:;" style="color:#fff;">Register</a><br><a id="forgotButton" href="javascript:;" style="color:#fff;">Forgot Password</a><br><img id="loader" style="display:none;" src="/img/ajax-loader.gif" alt="Loading ..."></form>');
	
	setTimeout("$('#authform input:text').focus();",600);

	$('#registerButton').click(function() {
		$('#DialogOverlay').fadeOut(300,function() {
			setTimeout(showRegister,100);
			$('#DialogOverlay').remove();
		});
	});
	
	$('#forgotButton').click(function() {
		$('#DialogOverlay').fadeOut(300,function() {
			setTimeout(showForgotPass,100);
			$('#DialogOverlay').remove();
		});
	});
	
	$("#authform").submit(function(event) {
		//Get form data before disabling form
		formdata = $('#authform').serialize();
		//$("#authform #error").stop().fadeOut(function() {
			$("#authform #error").html("");
		//});
		
		$('#authform input').attr('disabled', true);
		$('#authform input:text,#authform input:password').css('border-width','1px');
		$('#authform input:text,#authform input:password').animate({ borderTopColor: "#000",borderRightColor: "#000",borderBottomColor: "#000",borderLeftColor: "#000" }, 'slow');
		$('#authform #loader').show();
		
		event.preventDefault(); 

		
		$.post( "/authentication/verify", formdata,
			function( data ) {
				$('#authform #loader').hide();
				if (data.error.length > 0)
				{
					$("#authform #error").html(data.error);
					$("#authform #error").stop().fadeIn();
					$('#authform input:text,#authform input:password').animate({ borderTopColor: "#c30000",borderRightColor: "#c30000",borderBottomColor: "#c30000",borderLeftColor: "#c30000",borderWidth: "2px" }, 'fast');
					$('#authform input').attr('disabled', false);
				} else {
					window.location.href = "/profile/me";
				}
			}, 'json'
		);
	});
}

function showRegister()
{
	showMessage("","Registration",'<div style="float:left;width:400px;color:#fff;">Registration will allow you to upload videos to LFHS New Media as well as view additional videos created by other students.<br><br>Please complete the form to register for LFHS New Media.<br><br>A confirmation e-mail will be dispatched to the address you provide to confirm your identity.</div><form id="registrationForm" method="post" action="#" style="text-align:center;width:400px;float:left;"><div id="error" style="color:#fff;"></div><input type="text" name="email" placeholder="E-Mail Address"><br><input type="text" name="displayName" placeholder="Your Name"><br><input type="password" name="password" placeholder="Password" style="width:95px;margin-right:5px;"><input type="password" name="confirmPassword" placeholder="Confirm" style="width:95px;margin-left:5px;"><br><input type="submit" style="width:128px;font-size:14px;padding:4px;" value="Register"><img id="loader" style="display:none;" src="/img/ajax-loader.gif" alt="Loading ..."></form>');
	
	setTimeout("$('#registrationForm input:text').first().focus();",600);
	
	$("#registrationForm").submit(function(event) {
		//Get form data before disabling form
		formdata = $('#registrationForm').serialize();
		
		//$("#authform #error").stop().fadeOut(function() {
			$("#registrationForm #error").html("");
		//});
		
		$('#registrationForm input').attr('disabled', true);
		$('#registrationForm input:text,#registrationForm input:password').css('border-width','1px');
		$('#registrationForm input:text,#registrationForm input:password').animate({ borderTopColor: "#000",borderRightColor: "#000",borderBottomColor: "#000",borderLeftColor: "#000" }, 'slow');
		$('#registrationForm #loader').show();
		
		event.preventDefault(); 

		
		$.post( "/authentication/register", formdata,
			function( data ) {
				$('#registrationForm #loader').hide();
				if (data.error.length > 0)
				{
					$("#registrationForm #error").html(data.error);
					$("#registrationForm #error").stop().fadeIn();
					$('#registrationForm input:text,#registrationForm input:password').animate({ borderTopColor: "#c30000",borderRightColor: "#c30000",borderBottomColor: "#c30000",borderLeftColor: "#c30000",borderWidth: "2px" }, 'fast');
					$('#registrationForm input').attr('disabled', false);
				} else {
					window.location.href = "/profile/me";
				}
			}, 'json'
		);
	});
}

function showForgotPass()
{
	showMessage("","New Password",'<form id="newpassform" method="post" action="#" style="text-align:center;"><div id="error" style="color:#fff;"></div><input type="text" name="email" placeholder="E-Mail Address"><br><br><input type="submit" style="width:128px;font-size:14px;padding:4px;" value="New Password"><br><img id="loader" style="display:none;" src="/img/ajax-loader.gif" alt="Loading ..."></form>');
	
	$("#newpassform").submit(function(event) {
		//Get form data before disabling form
		formdata = $('#newpassform').serialize();
		
		//$("#authform #error").stop().fadeOut(function() {
			$("#newpassform #error").html("");
		//});
		
		$('#newpassform input').attr('disabled', true);
		$('#newpassform #loader').show();
		
		event.preventDefault(); 

		
		$.post( "/authentication/newpass", formdata,
			function( data ) {
				$('#newpassform #loader').hide();
				if (data.error.length > 0)
				{
					$("#newpassform #error").html(data.error);
					$("#newpassform #error").stop().fadeIn();
					$('#newpassform input:text').animate({ borderTopColor: "#c30000",borderRightColor: "#c30000",borderBottomColor: "#c30000",borderLeftColor: "#c30000",borderWidth: "2px" }, 'fast');
					$('#newpassform input').attr('disabled', false);
				} else {
					$('#newpassform').html("<span style=\"color:#fff;\">A new password has been dispatched to your e-mail address.</span>");
				}
			}, 'json'
		);
	});

}

/* Administration Thumbnail Stuff */
$(function() {
	$('div.videoThumbSmall.selectable').click(function() {
		if ($(this).hasClass('checked'))
		{
			$(this).removeClass('checked');
			if ($('div.videoThumbSmall.selectable.checked').length <= 0)
			{
				closeSelectionPane();
			} else {
				$("#SelectionMenu .title").html($('div.videoThumbSmall.selectable.checked').length + " items selected.");
				Cufon.refresh();
			}
			$(this).find(".selectionIndicator").remove();
		} else {
			$(this).addClass('checked');
			$(this).append('<div class="selectionIndicator"></div>');
			if ($(".bottomFlyOutMenu").length <= 0)
			{
				showSelectionPane();
			}
			$("#SelectionMenu .title").html($('div.videoThumbSmall.selectable.checked').length + " items selected.");
			Cufon.refresh();
		}
	});
});

function showSelectionPane()
{
	var menu = $('<div id="SelectionMenu" class="bottomFlyOutMenu"><div class="title"></div></div>');
	$('body').append(menu);
	menu.animate({bottom:0},400,'easeOutQuart');
	Cufon.replace("#SelectionMenu .title");
}

function closeSelectionPane()
{
	$('.bottomFlyOutMenu').animate({bottom:-64},400,'easeOutQuart',function() {$('.bottomFlyOutMenu').remove()});
}
