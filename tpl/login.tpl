                <script language="javascript" src="/ola/lib/md5.js"></script>

                <script language="javascript">

<!--
  function LoginResponse () {
    document.loginform.login_user.value = document.loginfake.login_user.value;
    document.loginform.login_password.value = MD5(document.loginfake.login_password.value);
    document.loginform.submit ();
  }
// -->

                </script>

                <form name="loginfake">
                  <table class="type2" align=center>
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

                <form name="loginform" method="post" action="/ola/index.php">
                <table align=center>
                 <tr>
                  <td>
                   <input type="hidden" name="login_user">
                   <input type="hidden" name="login_password">
                   <input onClick="LoginResponse(); return false;" type="submit" value="Login">
                  </td>
                 </tr>
                </table>
                </form>
