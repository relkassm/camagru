function logError() 
{
	document.getElementById('hidden-error').hidden = false;
	document.getElementById('error-msg').text = "Wrong Login / Password Combinaison";
}

function logError2() 
{
	document.getElementById('hidden-error').hidden = false;
	document.getElementById('error-msg').text = "Email Confirmation Needed";
}

function logError3()
{
	document.getElementById('hidden-error').hidden = false;
	document.getElementById('error-msg').text = 'Email Does Not Exist';
}

function logNoError() 
{
	document.getElementById('hidden-error').hidden = true;
}

function logNoError2(x) 
{
	document.getElementById('hidden-error').hidden = false;
	document.getElementById('error-msg').text = 'Email sent to '.concat(x);
}

function validateLogin(login)
{
    var re = /[\S]{5,10}$/;
    return re.test(login);
}

function validatePassword(password) 
{
    var re = /[\S]{8,30}$/;
    return re.test(password);
}

function validate()
{
	var login = document.getElementById('login').value;
	var passwd = document.getElementById('passwd').value;

	if (validateLogin(login) && validatePassword(passwd))
	{
		document.getElementById('submit-button').style.backgroundColor = "#000000";
		document.getElementById('submit-button').disabled = false;
	}
	else
	{
		document.getElementById('submit-button').style.backgroundColor = "#555555";
		document.getElementById('submit-button').disabled = true;
	}
}
