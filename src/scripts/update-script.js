function proceed()
{
	document.getElementById('update1').style = "display : none;";
	document.getElementById('update2').style = "display : inline-block;";
	document.getElementById('email').disabled = false;
	document.getElementById('firstname').disabled = false;
	document.getElementById('lastname').disabled = false;
	document.getElementById('login').disabled = false;
	document.getElementById('passwd').value = "";
	document.getElementById('passwd').disabled = false;
}