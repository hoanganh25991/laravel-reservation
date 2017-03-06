/**
 * language transfer
 */
var curLang = "en_US";

(function(){
	var l = window.$LANG||{};var f = function(k,v){l[k]=v;};
	f("No restaurant found","No restaurant found");
	f("Please select a time!","Please select a time!");
	f("This query has failed! Please try again, or contact support@chope.com.sg if you're unable to proceed. ","This query has failed! Please try again, or contact support@chope.com.sg if you're unable to proceed. ");
	f("We're sorry, but that seems to be an incorrect password! Please try again.","We're sorry, but that seems to be an incorrect password! Please try again.");
	f("The passwords do not match","The passwords do not match");
	f("Your email address has been entered in an invalid format! Please check it and try again.","Your email address has been entered in an invalid format! Please check it and try again.");
	f("This email address has already been registered.","This email address has already been registered.");
	f("We're sorry, but an error has occurred. Please try again, or contact support@chope.com.sg if you're unable to proceed.","We're sorry, but an error has occurred. Please try again, or contact support@chope.com.sg if you're unable to proceed.");
	f("Please enter your email address.","Please enter your email address.");
	f("Oops! It seems that you are not registered with us! Please register for an account with us.","Oops! It seems that you are not registered with us! Please register for an account with us.");
	f("Your password has been reset. Please check your inbox for your new password.","Your password has been reset. Please check your inbox for your new password.");
	f("This confirmation code has expired.","This confirmation code has expired.");
	f("Please enter your confirmation code","Please enter your confirmation code");
	f("Please re-enter your password.","Please re-enter your password.");
	f("You have updated your password successfully.","You have updated your password successfully.");
	f("Instructions on how to reset your password were sent to your email.","Instructions on how to reset your password were sent to your email.");
	f("Logged in, redirecting...","logged in, redirecting...");
	f("An email has been sent.","An email has been sent.");
	f("This email address has already been registered via Facebook. Please try logging in instead!","This email address has already been registered via Facebook. Please try logging in instead!");
	f("We are sorry, but there is no reservation found. Please contact support@chope.com.sg for further assistance.","We are sorry, but there is no reservation found. Please contact support@chope.com.sg for further assistance.");
	f("You have been successfully registered! Welcome to Chope!","You have been successfully registered! Welcome to Chope!");
	f("We encountered an error sending your email! Please enter your email address again.","We encountered an error sending your email! Please enter your email address again.");
	f("Your authentication token expired. Please enter your email again.","Your authentication token expired. Please enter your email again.");
	f("We're sorry, but that seems to be an incorrect username! Please try again.","We're sorry, but that seems to be an incorrect username! Please try again.");
	f("The restaurant is not accepting any more online reservations for the selected date. Please try a different date, or contact the restaurant directly for more information.","The restaurant is not accepting any more online reservations for the selected date. Please try a different date, or contact the restaurant directly for more information.");
	f("Dear user, we have detected that this is your second quick reservation for this restaurant! Please help to prevent abusive behavior by verifying your account via this","Dear user, we have detected that this is your second quick reservation for this restaurant! Please help to prevent abusive behavior by verifying your account via this");
	f("The emails do not match","The emails do not match");
	f("January","January");
	f("February","February");
	f("March","March");
	f("April","April");
	f("May","May");
	f("June","June");
	f("July","July");
	f("August","August");
	f("September","September");
	f("October","October");
	f("November","November");
	f("December","December");
	
	f("Jan","Jan");
	f("Feb","Feb");
	f("Mar","Mar");
	f("Apr","Apr");
	f("Jun","Jun");
	f("Jul","Jul");
	f("Aug","Aug");
	f("Sep","Sep");
	f("Oct","Oct");
	f("Nov","Nov");
	f("Dec","Dec");
	
	f("Sun","Sun");
	f("Mon","Mon");
	f("Tue","Tue");
	f("Wed","Wed");
	f("Thu","Thu");
	f("Fri","Fri");
	f("Sat","Sat");
	f("Today","Today");
	f("Events Today","Events Today");	
	f("We're sorry, but this email address isn't in a valid format.","We're sorry, but this email address isn't in a valid format.");
	f("Request failed: ","Request failed: ");
	f("New password must be more than 6 characters","New password must be more than 6 characters");
	f("Please enter your Surname.","Please enter your Surname.");
	f("Please enter your First Name.","Please enter your First Name.");
	f("We're sorry, but this email address must be in a valid format.","We're sorry, but this email address must be in a valid format.");
	f("Please enter your telephone.","Please enter your telephone.");
	f("Please enter your password again.","Please enter your password again.");
	f("We're sorry, but the passwords don't match","We're sorry, but the passwords don't match");
	f("Please enter your telephone.","Please enter your telephone.");
	f("Password should be at least 6 characters","Password should be at least 6 characters");
	f("Your email address entries do not match. Please try again.","Your email address entries do not match. Please try again.");
	f("We're sorry, but this phone number isn't in a valid format. Please try again.","We're sorry, but this phone number isn't in a valid format. Please try again.");
	f("Please agree to the Terms and Conditions.","Please agree to the Terms and Conditions.");
	f("Dear guest, please acknowledge the information at the bottom of the page by ticking the check box. Thank you for your kind understanding!","Dear guest, please acknowledge the information at the bottom of the page by ticking the check box. Thank you for your kind understanding!");
	f("Not available","Not available");
	f("Please enter your given name.","Please enter your given name.");
	f("Please enter your surname.","Please enter your surname.");
	f("Please enter your email address.","Please enter your email address.");
	f("Are you sure you want to cancel it?","Are you sure you want to cancel it?");
	f("Missing confirmation ID","Missing confirmation ID");
	f("Missing phone number","Missing phone number");
	f("Please select the restaurant","Please select the restaurant");
	f("You must agree to the terms and conditions.","You must agree to the terms and conditions.");
	f("Please enter your comfirmation code.","Please enter your comfirmation code.");
	f('Please select a restaurant.','Please select a restaurant.');
	f('Please enter your Mobile Phone.','Please enter your Mobile Phone.');
	f("We're sorry, but the emails don't match","We're sorry, but the emails don't match");
	f('Please enter your Confirmation code.','Please enter your Confirmation code.');
	f('Your invitaion has been sent failed !','Your invitaion has been sent failed !');
	window.$LANG = l;
})(window);
function _(v){
	if (typeof($LANG[v]) == "undefined" || $LANG[v] == "") {
		return v;
	}
		return $LANG[v];
	}