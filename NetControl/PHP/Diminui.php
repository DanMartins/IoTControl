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

   error_reporting(E_ERROR | E_PARSE);

   $session = Initialize();
   $value = 0;
   Defines($session, $value);
   PrintSpeed($session, $value);
   //Done($session);


   //
   // Initialize()
   //

   function Initialize() {
      $test_dsn = "c-treeACE ODBC Driver";
      if(($ses = odbc_pconnect( $test_dsn,"admin","ADMIN")) == FALSE)
      {
        Handle_Error("odbc_connect()");
      }else{
           echo "Connection succeeded!\n";
      }

      return ($ses);
   }


   //
   // Define()
   //

   function Defines($ses, $val) {
      $qry = odbc_exec($ses, "SELECT ajuste FROM controle");

      odbc_fetch_row($qry);
      $array = odbc_result($qry, "ajuste");
      $array= strval (intval($array) - 10);


      $query = "UPDATE controle
            SET ajuste = $array";
      $qry = odbc_exec($ses, $query);

      if (!$qry)
         Handle_Error("odbc_exec()");

      if (!odbc_commit($ses))
         Handle_Error("odbc_commit()");
   }

   //
   // PrintSpeed()
   //

   function PrintSpeed($ses, $val) {
      $qry = odbc_exec($ses, "SELECT TOP 1 velocidade, tempo FROM dados ORDER BY tempo DESC");
      odbc_result_all($qry);

      if (!$qry)
         Handle_Error("odbc_exec()");

   }

   //
   // Done()
   //

   function Done ($ses) {
      odbc_close($ses);
   }


   //
   // Handle_Error()
   //

   function Handle_Error($msg) {

      $err = odbc_errormsg();

      print("$msg - SQL ERROR: [$err] <br>\n");
      print("*** Execution aborted *** <br>\n");
      exit();
   }


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