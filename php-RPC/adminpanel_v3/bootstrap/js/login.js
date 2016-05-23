$(function () {
// Please read these set of code after you read next set. So that you can understand simply.
var username = $.cookie("email");   // Get username from cookie on form load.
var pass = $.cookie("password"); 
if(username!=undefined)
{	
     $('#email').val(username);   // Display username on input box if avail on cookie.
	 $('password').val(pass);
     $('#remember_me').prop('checked', true);  // Check check box manually by us. So that duration for current email will reset.
}
// Read this set first.
$('#loginsubmit').click(function()
{
      if($('#remember_me').is(':checked')) // If user checked remember me check box
     {
                var email=$('#email').val(); // Get entered username or email
				var password=$('#password').val(); // Get entered password
                //  Set username on cookie when login form submit.
                $.cookie("email", email, { expires: 365 });  // Remember username for 1 year.
				$.cookie("password", password, { expires: 365 });  // Remember username for 1 year.
     }
	 // Your other codes...
});

});
