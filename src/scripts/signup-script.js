function validateEmail(email) 
{
    var re = /\S+@\S+\.\S+/;
    return re.test(email);
}

function validateName(name) 
{
    var re = /^[a-zA-Z ]{2,15}$/;
    return re.test(name);
}

function validateLogin(login)
{
    var re = /^[a-zA-Z0-9]{5,10}$/;
    return re.test(login);
}

function validatePassword(password) 
{
    var re = /^(?=.*[A-Za-z])(?=.*\d)[\S]{8,30}$/;
    return re.test(password);
}

function validate()
{
	var email = document.getElementById('email').value;
	var firstname = document.getElementById('firstname').value;
	var lastname = document.getElementById('lastname').value;
	var login = document.getElementById('login').value;
	var passwd = document.getElementById('passwd').value;
	var passwd2 = document.getElementById('passwd2').value;

	if (validateEmail(email) && validateName(firstname) && validateName(lastname) && validateLogin(login) && validatePassword(passwd) && passwd === passwd2)
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

function error1()
{
	document.getElementById('hidden-error').hidden = false;
	document.getElementById('error-msg').text = "Email Already Exists";
}

function error2()
{
	document.getElementById('hidden-error').hidden = false;
	document.getElementById('error-msg').text = "Login Already Taken";
}

