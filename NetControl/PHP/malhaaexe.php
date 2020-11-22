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
   print("\t<title>IoTControl Configuração de Controle</title>\n");
   print("</head>\n");
   print("<body>\n");

   $osrunning = php_uname();
   if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
    // Force ODBCINI to define unixODBC var to the browser user.
	putenv("ODBCINI=/etc/odbc.ini");

   }

   // No warnings but errors ON
   error_reporting(E_ERROR | E_PARSE);

   print("\t<h4>IoTControl - Comando em processamento</h4>\n");

   Atualiza();

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
   // Atualiza()
   //
   // Perform the minimum requirement of logging onto the c-tree Server
   //

   function Atualiza() {

      if (filter_var($_POST['ajuste'], FILTER_VALIDATE_INT) === 0 || !filter_var($_POST['ajuste'], FILTER_VALIDATE_INT) === false)
      {
	      $ajuste=$_POST['ajuste'];
	  } else
	  {
	      H_Erro("Velocidade Desejada $_POST[ajuste]");
      }

      if (filter_var($_POST['valork'], FILTER_VALIDATE_FLOAT) === 0.0 || !filter_var($_POST['valork'], FILTER_VALIDATE_FLOAT) === false)
      {
	      $valork=$_POST['valork'];
	  } else
	  {
	      H_Erro("Fator K $_POST[valork]");
      }


     $ses = Initialize();

     $query = "UPDATE controle
            SET ajuste = '$ajuste'";
     $qry = odbc_exec($ses, $query);

     if (!$qry)
         H_Erro("odbc_exec()");

     $query = "UPDATE controle
            SET valork = '$valork'";
     $qry = odbc_exec($ses, $query);

     if (!$qry)
         H_Erro("odbc_exec()");

     if (!odbc_commit($ses))
         H_Erro("odbc_commit()");

     print("ajuste: [$ajuste] <br>\n");
     print("valork: [$valork] <br>\n");
     print("*** Processamento concluído com sucesso *** <br>\n");
     //Done ($ses);

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

      print("$msg - ERRO, parâmetro incorreto ou sintaxe: [$err] <br>\n");
      print("*** Processamento abortado *** <br>\n");
      exit();
   }

   print("</body>\n");
   print("</html>\n");

// end of PHP_IoT_ODBC.php
?>
<html>
    <head>
    </head>
    <body>
    <?php
	ob_start(); // ensures anything dumped out will be caught

	// do stuff here
	$url = 'Location: '. $_SERVER['HTTP_REFERER'];

	// clear out the output buffer
	while (ob_get_status())
	{
		ob_end_clean();
	}

	// no redirect
	header( "$url" );
	?>
    </body>
</html>