/*%################################################################
/*% NetControl - IoTControl
/*%
/*%       Network - Interface.
/*%       DanMartins
/*%       IoTControl reasearch project
/*%       São Paulo, 2017.
/*%
/*%################################################################
*/
<?php
   print("<!DOCTYPE html>\n");
   print("<html>\n");
   print("<head>\n");
   print("\t<title>IoTControl Configuração de Controle</title>\n");
   print("<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n");

   $osrunning = php_uname();
   if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
    // Force ODBCINI to define unixODBC var to the browser user.
	putenv("ODBCINI=/etc/odbc.ini");

   }

   popen("/var/www/html/CAMPO.sh", "r");

   // No warnings but errors ON
   error_reporting(E_ERROR | E_PARSE);

   print("\t<h4>IoTControl - Configurações CAMPO</h4>\n");


   $ses = Initialize();

   $query = "SELECT ajuste, valork, valorki FROM controle";

   $qry = odbc_exec($ses, $query);

   $result = odbc_fetch_array($qry);


   if (!$qry)
    H_Erro("odbc_exec(SELECT TOP)");

   //Done($ses);


   //
   // Initialize()
   //
   // Perform the minimum requirement of logging onto the c-tree Server
   //

   function Initialize() {

      // connect to server
      $test_dsn = "c-treeACE ODBC Driver";
      if(($ses = odbc_pconnect( $test_dsn,"admin","ADMIN")) == FALSE)
      {
        H_Erro("odbc_connect()");
      }else

      return ($ses);
   }


   //
   // Done()
   //
   // This function handles the housekeeping of closing, freeing,
   // disconnecting and logging out of the database
   //

   function Done ($ses) {
      // logout
      odbc_close($ses);
   }

   //
   // H_Erro()
   //
   // General error routine that retrieves and displays specific SQL Error
   // before exiting the tutorial.  If the error returned indicates an object
   // already exists, execution is returned to the calling function.
   //

   function H_Erro($msg) {

      $err = odbc_errormsg();

      print("$msg - SQL ERROR: [$err] <br>\n");
      print("*** Processamento abortado *** <br>\n");
      exit();
   }

   print("</head>\n");
   print("<body>\n");

   print("</body>\n");
   print("</html>\n");

// end of PHP_IoT_ODBC.php
?>
<html>
    <head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <table>
	<form method="post" action="piexe.php">
	  Ganho K:
	  <input type="text" name="valork" value="<?php echo $result['valork'] ?>">
	  <br>
	  Fator Ti:
	  <input type="text" name="valorki" value="<?php echo $result['valorki'] ?>">
	  <br><br>
	  Velocidade Desejada (p/s):
	  <input type="number" min="-1000" max="1000" step="1" value="<?php echo $result['ajuste'] ?>" name="ajuste"
	         for="power" oninput="power.value=ajuste.value" >
	  <br>

	  <input type="range" min="-1000" max="1000"  step="1" value="<?php echo $result['ajuste'] ?>" name="power" list="powers"
	         style="height:15%; width:95%; float:center;"
	         for="ajuste" oninput="ajuste.value=power.value" >

	  <datalist id="powers">
	    <option value="-1000">
	    <option value="-750">
	    <option value="-500">
	    <option value="-250">
	    <option value="0">
	    <option value="250">
	    <option value="500">
	    <option value="750">
	    <option value="1000">
	  </datalist>
	  <br>

	  <input type="submit" style="height:100px; width:250px;" value="Atualizar">
	</form>

	<form action="graph.html" method="post">
	<input type="submit" style="height:100px; width:250px;" value="Gráfico">
	</form>

	<form action="index.html" method="post">
	<input type="submit" style="height:100px; width:250px;" value="Voltar">
    </form>
	<form action="Aumenta.php" method="post">
	<input type="submit" style="height:100px; width:250px;" value="Aumentar (+)">
	</form>
	<form action="Diminui.php" method="post">
	<input type="submit" style="height:100px; width:250px;" value="Diminuir (-)">
	</form>
	</form>
	<form action="Zero.php" method="post">
	<input type="submit" style="height:100px; width:250px;" value="Zero (0)">
	</form>
    </table>
    </head>
    <body>
    </body>
</html>