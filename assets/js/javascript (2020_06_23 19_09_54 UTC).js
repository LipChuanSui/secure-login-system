/***************************************************************************************
*    Title: Show/Hide password field
*    Author: Sohail Aj
*    Date: 15/5/2020
*    Availability: https://codepen.io/Sohail05/pen/yOpeBm
*
***************************************************************************************/

$(".toggle-password").click(function() { // toggle password visibility
  $(this).toggleClass("fa-eye fa-eye-slash");
  var input = $($(this).attr("toggle"));
  if (input.attr("type") == "password") {
    input.attr("type", "text");
  } else {
    input.attr("type", "password");
  }
});

/***************************************************************************************
*    Title: Check password strength using javascript / jquery
*    Author: Dave
*    Date: 4/4/2020
*    Availability: https://stackoverflow.com/questions/38779755/check-password-strength-using-javascript-jquery
*
***************************************************************************************/

function passStrength() {// check pssword strrength

		var password = $('#pass1').val();
		CheckPasswordStrength(password)

}

function passNotOk(){//to alert user if he really want to proceed with weak password

	return confirm("Do you really want to register with weak/medium password?");
}

function passOk(){//aloow user to proceed

	return true;
}

function CheckPasswordStrength(password) {//check password strength

 var password_strength = document.getElementById("password_strength");//dipslay result of checking

	 //Regular Expressions
   //strong password require uppercase, lowercase, symbol and password
	 var regex = new Array();
	 regex.push("[A-Z]"); //For Uppercase Alphabet
	 regex.push("[a-z]"); //For Lowercase Alphabet
	 regex.push("[0-9]"); //For Numeric Digits
	 regex.push("[$@$!%*#?&]"); //For Special Characters

	 var passed = 0;//marks for password strength

	 //Validation for each Regular Expression
	 for (var i = 0; i < regex.length; i++) {
			 if((new RegExp (regex[i])).test(password)){
					 passed++;
			 }
	 }

	 //Validation for Length of Password
   //password longer than 8 Characters is strong
	 if(passed > 2 && password.length > 8){
			 passed++;
	 }


	 var color = "";
	 var passwordStrength = "";

	 switch(passed){//display results for each marks
			 case 0://no input

			 		color = "Red";
					 break;

			 case 1: case 2://password only fullfil 2 condition

					 passwordStrength = "Password is Weak.";//weak password
					 color = "Red";
					 document.getElementById("submitBtn").onclick = function() {return passNotOk()};//prompt alert to ask user
					 break;

			 case 3://password only fullfil 3 condition

					 passwordStrength = "Password is Medium.";
					 color = "Orange";
					 document.getElementById("submitBtn").onclick = function() {return passNotOk()};//prompt alert to ask user
					 break;

			 case 4: case 5://password only fullfil all condition

					 passwordStrength = "Password is Strong.";
					 color = "Green";
					 document.getElementById("submitBtn").onclick = function() {return passOk()};
					 break;

	 }
	 password_strength.innerHTML = passwordStrength;//display result
	 password_strength.style.color = color;
}


/***************************************************************************************
*    Title: Check password strength using javascript / jquery
*    Author: Dave
*    Date: 4/4/2020
*    Availability:
https://forums.asp.net/t/1899214.aspx?If+no+activity+for+15+minutes+display+an+alert+on+web+page+and+then+either+continue+or+logout
*
***************************************************************************************/
//auto logout after 3 minutes of inactive
// Set timeout variables.
var timoutWarning = 120000; // Display warning in 2 Mins.
var timoutNow = 180000; // Timeout in 3 mins.
var getUrl = window.location;
var baseUrl = getUrl .protocol + "//" + getUrl.host + "/" + getUrl.pathname.split('/')[1];//get baseUrl
var logoutUrl = baseUrl  + "/users/logout"; // URL to logout page.

var warningTimer;
var timeoutTimer;

function StartTimers() {// Start timers.

    warningTimer = setTimeout("IdleWarning()", timoutWarning);//display idle warning after 2 minutes of inactive
    timeoutTimer = setTimeout("IdleTimeout()", timoutNow);//logout user after 3 minutes of inactive
}


function ResetTimers() {// Reset timers when user move mouse
  //see application/views/templates.header.php -> body tag

    clearTimeout(warningTimer);
    clearTimeout(timeoutTimer);
    StartTimers();
		$("#timeout").modal('hide');
}

function IdleWarning() {// Show idle timeout warning dialog.

	$('#timeout').modal('show');//display modal to alert user about 2 mins of inactive
  //see application/views/templates.header.php -> modal
}

function IdleTimeout() {// Logout the user.

    window.location = logoutUrl;
}
