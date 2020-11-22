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

   //if(isset($_POST['Atualizar']))
   //{
   // Atualiza();
   //}

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

      if (filter_var($_POST['fuzN3'], FILTER_VALIDATE_FLOAT) === 0.0 || !filter_var($_POST['fuzN3'], FILTER_VALIDATE_FLOAT) === false)
      {
	      $fuzN3=$_POST['fuzN3'];
	  } else
	  {
	      H_Erro("Fator K $_POST[fuzN3]");
      }
      if (filter_var($_POST['fuzN2'], FILTER_VALIDATE_FLOAT) === 0.0 || !filter_var($_POST['fuzN2'], FILTER_VALIDATE_FLOAT) === false)
      {
	      $fuzN2=$_POST['fuzN2'];
	  } else
	  {
	      H_Erro("Fator K $_POST[fuzN2]");
      }
      if (filter_var($_POST['fuzN1'], FILTER_VALIDATE_FLOAT) === 0.0 || !filter_var($_POST['fuzN1'], FILTER_VALIDATE_FLOAT) === false)
      {
	      $fuzN1=$_POST['fuzN1'];
	  } else
	  {
	      H_Erro("Fator K $_POST[fuzN1]");
      }
      if (filter_var($_POST['fuzZ'], FILTER_VALIDATE_FLOAT) === 0.0 || !filter_var($_POST['fuzZ'], FILTER_VALIDATE_FLOAT) === false)
      {
	      $fuzZ=$_POST['fuzZ'];
	  } else
	  {
	      H_Erro("Fator K $_POST[fuzZ]");
      }
      if (filter_var($_POST['fuzP3'], FILTER_VALIDATE_FLOAT) === 0.0 || !filter_var($_POST['fuzP3'], FILTER_VALIDATE_FLOAT) === false)
      {
	      $fuzP3=$_POST['fuzP3'];
	  } else
	  {
	      H_Erro("Fator K $_POST[fuzP3]");
      }
      if (filter_var($_POST['fuzP2'], FILTER_VALIDATE_FLOAT) === 0.0 || !filter_var($_POST['fuzP2'], FILTER_VALIDATE_FLOAT) === false)
      {
	      $fuzP2=$_POST['fuzP2'];
	  } else
	  {
	      H_Erro("Fator K $_POST[fuzP2]");
      }
      if (filter_var($_POST['fuzP1'], FILTER_VALIDATE_FLOAT) === 0.0 || !filter_var($_POST['fuzP1'], FILTER_VALIDATE_FLOAT) === false)
      {
	      $fuzP1=$_POST['fuzP1'];
	  } else
	  {
	      H_Erro("Fator K $_POST[fuzP1]");
      }



      if (filter_var($_POST['defuzN3'], FILTER_VALIDATE_FLOAT) === 0.0 || !filter_var($_POST['defuzN3'], FILTER_VALIDATE_FLOAT) === false)
      {
	      $defuzN3=$_POST['defuzN3'];
	  } else
	  {
	      H_Erro("Fator K $_POST[defuzN3]");
      }
      if (filter_var($_POST['defuzN2'], FILTER_VALIDATE_FLOAT) === 0.0 || !filter_var($_POST['defuzN2'], FILTER_VALIDATE_FLOAT) === false)
      {
	      $defuzN2=$_POST['defuzN2'];
	  } else
	  {
	      H_Erro("Fator K $_POST[defuzN2]");
      }
      if (filter_var($_POST['defuzN1'], FILTER_VALIDATE_FLOAT) === 0.0 || !filter_var($_POST['defuzN1'], FILTER_VALIDATE_FLOAT) === false)
      {
	      $defuzN1=$_POST['defuzN1'];
	  } else
	  {
	      H_Erro("Fator K $_POST[defuzN1]");
      }
      if (filter_var($_POST['defuzZ'], FILTER_VALIDATE_FLOAT) === 0.0 || !filter_var($_POST['defuzZ'], FILTER_VALIDATE_FLOAT) === false)
      {
	      $defuzZ=$_POST['defuzZ'];
	  } else
	  {
	      H_Erro("Fator K $_POST[defuzZ]");
      }
      if (filter_var($_POST['defuzP3'], FILTER_VALIDATE_FLOAT) === 0.0 || !filter_var($_POST['defuzP3'], FILTER_VALIDATE_FLOAT) === false)
      {
	      $defuzP3=$_POST['defuzP3'];
	  } else
	  {
	      H_Erro("Fator K $_POST[defuzP3]");
      }
      if (filter_var($_POST['defuzP2'], FILTER_VALIDATE_FLOAT) === 0.0 || !filter_var($_POST['defuzP2'], FILTER_VALIDATE_FLOAT) === false)
      {
	      $defuzP2=$_POST['defuzP2'];
	  } else
	  {
	      H_Erro("Fator K $_POST[defuzP2]");
      }
      if (filter_var($_POST['defuzP1'], FILTER_VALIDATE_FLOAT) === 0.0 || !filter_var($_POST['defuzP1'], FILTER_VALIDATE_FLOAT) === false)
      {
	      $defuzP1=$_POST['defuzP1'];
	  } else
	  {
	      H_Erro("Fator K $_POST[defuzP1]");
      }


     $ses = Initialize();

     $query = "UPDATE controle
            SET ajuste = '$ajuste'";
     $qry = odbc_exec($ses, $query);

     if (!$qry)
         H_Erro("odbc_exec()");

     $query = "UPDATE fuzzy
            SET \"fuzN3\" = '$fuzN3'";
     $qry = odbc_exec($ses, $query);

     if (!$qry)
         H_Erro("odbc_exec()");



     $query = "UPDATE fuzzy
            SET \"fuzN2\" = '$fuzN2'";
     $qry = odbc_exec($ses, $query);

     if (!$qry)
         H_Erro("odbc_exec()");

     $query = "UPDATE fuzzy
            SET \"fuzN1\" = '$fuzN1'";
     $qry = odbc_exec($ses, $query);

     if (!$qry)
         H_Erro("odbc_exec()");

     $query = "UPDATE fuzzy
            SET \"fuzZ\" = '$fuzZ'";
     $qry = odbc_exec($ses, $query);

     if (!$qry)
         H_Erro("odbc_exec()");


     $query = "UPDATE fuzzy
            SET \"fuzP3\" = '$fuzP3'";
     $qry = odbc_exec($ses, $query);

     if (!$qry)
         H_Erro("odbc_exec()");

     $query = "UPDATE fuzzy
            SET \"fuzP2\" = '$fuzP2'";
     $qry = odbc_exec($ses, $query);

     if (!$qry)
         H_Erro("odbc_exec()");

     $query = "UPDATE fuzzy
            SET \"fuzP1\" = '$fuzP1'";
     $qry = odbc_exec($ses, $query);

     if (!$qry)
         H_Erro("odbc_exec()");





     $query = "UPDATE fuzzy
            SET \"defuzN3\" = '$defuzN3'";
     $qry = odbc_exec($ses, $query);

     if (!$qry)
         H_Erro("odbc_exec()");

     $query = "UPDATE fuzzy
            SET \"defuzN2\" = '$defuzN2'";
     $qry = odbc_exec($ses, $query);

     if (!$qry)
         H_Erro("odbc_exec()");

     $query = "UPDATE fuzzy
            SET \"defuzN1\" = '$defuzN1'";
     $qry = odbc_exec($ses, $query);

     if (!$qry)
         H_Erro("odbc_exec()");

     $query = "UPDATE fuzzy
            SET \"defuzZ\" = '$defuzZ'";
     $qry = odbc_exec($ses, $query);

     if (!$qry)
         H_Erro("odbc_exec()");


     $query = "UPDATE fuzzy
            SET \"defuzP3\" = '$defuzP3'";
     $qry = odbc_exec($ses, $query);

     if (!$qry)
         H_Erro("odbc_exec()");

     $query = "UPDATE fuzzy
            SET \"defuzP2\" = '$defuzP2'";
     $qry = odbc_exec($ses, $query);

     if (!$qry)
         H_Erro("odbc_exec()");

     $query = "UPDATE fuzzy
            SET \"defuzP1\" = '$defuzP1'";
     $qry = odbc_exec($ses, $query);

     if (!$qry)
         H_Erro("odbc_exec()");


     if (!odbc_commit($ses))
         H_Erro("odbc_commit()");

     print("ajuste: [$ajuste] <br>\n");
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