<?php

/***************************************************************************
* ONLINE LIBRARY APPLICATION (OLA)               http://ola.sourceforge.net/
* (c) 2002 S. Rawlinson and N. Flear    Licenced under GPL (see licence.txt)
****************************************************************************
* set_password.php - version 2.0
* - reveals the encrypted password in order to set
*   up a new login and password
***************************************************************************/

?>

<html>
<head>
<title>Login Test</title>
</head>
<body>

<script language="javascript" src="md5.js"></script>

<script language="javascript">

<!--
  function LoginResponse () {
    document.loginform.login_user.value = document.loginfake.login_user.value;
    document.loginform.login_password.value = MD5(document.loginfake.login_password.value);
	 
    document.loginform.submit ();
  }
// -->


</script>

<h2>Login Test</h2>

<p>This page helps the administrator determine the encrypted password in 
order to set it for the login script.</p>

<form name="loginfake">
  <table class="type2">
    <tr>
      <td class="type3">User:</td>
      <td><input type="text" name="login_user"></td>
    </tr>
    <tr>
      <td class="type3">Password:</td>
      <td><input type="password" name="login_password"></td>
    </tr>
  </table>
</form>

<form name="loginform" method="post" action="set_password.php">
  <input type="hidden" name="login_user">
  <input type="hidden" name="login_password">
  <input onClick="LoginResponse(); return false;" type="submit" value="Login">
</form>

<?php

if (isset ($_POST) && !empty ($_POST)) {
  echo ("The encrypted user and password are: ");
  $login = $_POST["login_user"];
  echo ($login);
  echo (" ");
  $pwd = $_POST['login_password'];
  echo ($pwd);
}

?>

</body>
</html>
