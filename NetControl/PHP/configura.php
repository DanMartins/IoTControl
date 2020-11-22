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

   print("<html>\n");
   print("<head>\n");
   print("<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n");

   print("\t<title>IoTControl Configuração de Sistema</title>\n");


   $osrunning = php_uname();
   if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
    // Force ODBCINI to define unixODBC var to the browser user.
	putenv("ODBCINI=/etc/odbc.ini");

   }

   // No warnings but errors ON
   error_reporting(E_ERROR | E_PARSE);

   print("\t<h4>IoTControl - Configurações</h4>\n");


   $ses = Initialize();

   $query = "SELECT pulsosvelcalc, tempovelcalc, tempociclo, grafpts, intvelfil, pwmfreq, motorzm, pwmzm FROM configura";

   $qry = odbc_exec($ses, $query);

   $result = odbc_fetch_array($qry);

   $query2 = "SELECT fatora, fatorb, fatorc FROM controle";

   $qry2 = odbc_exec($ses, $query2);

   $result2 = odbc_fetch_array($qry2);

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
    </head>
    <body>
    <?php ?>
	<form method="post" action="configuraexe.php">
	  Fator A (modelamento):
	  <input type="text" name="fatora" value="<?php echo $result2['fatora'] ?>">
      <br>
	  Fator B (modelamento):
	  <input type="text" name="fatorb" value="<?php echo $result2['fatorb'] ?>">
      <br>
	  Fator C (modelamento):
	  <input type="text" name="fatorc" value="<?php echo $result2['fatorc'] ?>">
      <br>
	  Quantidade de pulsos para calcular velocidade:
	  <input type="text" name="pulsosvelcalc" value="<?php echo $result['pulsosvelcalc'] ?>">
      <br>
	  Tempo máximo para calcular velocidade (s):
	  <input type="text" name="tempovelcalc" value="<?php echo $result['tempovelcalc'] ?>">
      <br>
	  Intervalo entre armazenamento de dados (s):
	  <input type="text" name="tempociclo" value="<?php echo $result['tempociclo'] ?>">
      <br>
	  Quantidade de amostras para velocidade:
	  <input type="text" name="intvelfil" value="<?php echo $result['intvelfil'] ?>">
      <br>
	  Pontos para exibir gráfico:
	  <input type="text" name="grafpts" value="<?php echo $result['grafpts'] ?>">
      <br>
	  Frequência PWM:
	  <input type="text" name="pwmfreq" value="<?php echo $result['pwmfreq'] ?>">
      <br>
	  Zona Morta Motor (p/s):
	  <input type="text" name="motorzm" value="<?php echo $result['motorzm'] ?>">
      <br>
	  Zona Morta PWM (%):
	  <input type="text" name="pwmzm" value="<?php echo $result['pwmzm'] ?>">
	  <br>
	  <input type="submit" style="height:100px; width:250px;" value="Atualizar">
	</form>

	<form action="index.html" method="post">
	<input type="submit" style="height:100px; width:250px;" value="Voltar">
    </form>

    </body>
</html>