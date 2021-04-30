<!-- %################################################################
######################################################
## {IoTControl - NetControl - Interface}            ##
######################################################
## { DanMartins/IoTControl is licensed under the    ##
##   GNU General Public License v3.0}               ##
######################################################
## Author: {DanMartins}                             ##
## Copyright: Copyright {2021}, {IoTControl}        ##
## Credits: [{https://domartins.wixsite.com/data}]  ##
## License: {GNU General Public License v3.0}       ##
## Version: {2021}.{04}.{22}                        ##
## Maintainer: {github.com/DanMartins/IoTControl}   ##
## Email: {github.com/DanMartins}                   ##
## Status: {Development}                            ##
/*%################################################################
-->
<?php

   print("<html>\n");
   print("<head>\n");
   print("\t<title>IoTControl</title>\n");
   print("</head>\n");
   print("<body>\n");

   $osrunning = php_uname();
   if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
    // Force ODBCINI to define unixODBC var to the browser user.
	putenv("ODBCINI=/etc/odbc.ini");

   }

   // No warnings but errors ON
   error_reporting(E_ERROR | E_PARSE);

   Atualiza();
   // if(isset($_REQUEST['Atualizar']))
   if(isset($_POST['Atualizar']))
   {
	 Atualiza();
   }

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

      if (!filter_var($_POST['pulsosvelcalc'], FILTER_VALIDATE_INT) === false)
      {
	      $pulsosvelcalc=$_POST['pulsosvelcalc'];
	  } else
	  {
	      H_Erro("$_POST[pulsosvelcalc]");
      }

      if (filter_var($_POST['tempovelcalc'], FILTER_VALIDATE_FLOAT) === 0.0 || !filter_var($_POST['tempovelcalc'], FILTER_VALIDATE_FLOAT) === false)
      {
	      $tempovelcalc=$_POST['tempovelcalc'];
	  } else
	  {
	      H_Erro("$_POST[tempovelcalc]");
      }

      if (filter_var($_POST['tempociclo'], FILTER_VALIDATE_FLOAT) === 0.0 || !filter_var($_POST['tempociclo'], FILTER_VALIDATE_FLOAT) === false)
      {
	      $tempociclo=$_POST['tempociclo'];
	  } else
	  {
	      H_Erro("$_POST[tempociclo]");
      }

      if (!filter_var($_POST['grafpts'], FILTER_VALIDATE_INT) === false)
      {
	      $grafpts=$_POST['grafpts'];
	  } else
	  {
	      H_Erro("$_POST[grafpts]");
      }

      if (filter_var($_POST['intvelfil'], FILTER_VALIDATE_FLOAT) === 0.0 || !filter_var($_POST['intvelfil'], FILTER_VALIDATE_FLOAT) === false)
      {
	      $intvelfil=$_POST['intvelfil'];
	  } else
	  {
	      H_Erro("$_POST[intvelfil]");
      }

      if (filter_var($_POST['pwmfreq'], FILTER_VALIDATE_FLOAT) === 0.0 || !filter_var($_POST['pwmfreq'], FILTER_VALIDATE_FLOAT) === false)
      {
	      $pwmfreq=$_POST['pwmfreq'];
	  } else
	  {
	      H_Erro("$_POST[pwmfreq]");
      }

      if (filter_var($_POST['motorzm'], FILTER_VALIDATE_FLOAT) === 0.0 || !filter_var($_POST['motorzm'], FILTER_VALIDATE_FLOAT) === false)
      {
	      $motorzm=$_POST['motorzm'];
	  } else
	  {
	      H_Erro("$_POST[motorzm]");
      }

      if (filter_var($_POST['pwmzm'], FILTER_VALIDATE_FLOAT) === 0.0 || !filter_var($_POST['pwmzm'], FILTER_VALIDATE_FLOAT) === false)
      {
	      $pwmzm=$_POST['pwmzm'];
	  } else
	  {
	      H_Erro("$_POST[pwmzm]");
      }

      if (filter_var($_POST['fatora'], FILTER_VALIDATE_FLOAT) === 0.0 || !filter_var($_POST['fatora'], FILTER_VALIDATE_FLOAT) === false)
      {
	      $fatora=$_POST['fatora'];
	  } else
	  {
	      H_Erro("$_POST[fatora]");
      }
      if (filter_var($_POST['fatorb'], FILTER_VALIDATE_FLOAT) === 0.0 || !filter_var($_POST['fatorb'], FILTER_VALIDATE_FLOAT) === false)
      {
	      $fatorb=$_POST['fatorb'];
	  } else
	  {
	      H_Erro("$_POST[fatorb]");
      }
      if (filter_var($_POST['fatorc'], FILTER_VALIDATE_FLOAT) === 0.0 || !filter_var($_POST['fatorc'], FILTER_VALIDATE_FLOAT) === false)
      {
	      $fatorc=$_POST['fatorc'];
	  } else
	  {
	      H_Erro("$_POST[fatorc]");
      }

     $ses = Initialize();

     $query = "UPDATE configura
            SET pulsosvelcalc = '$pulsosvelcalc'";
     $qry = odbc_exec($ses, $query);

     if (!$qry)
         H_Erro("odbc_exec()");


     $query = "UPDATE configura
            SET tempovelcalc = '$tempovelcalc'";
     $qry = odbc_exec($ses, $query);

     if (!$qry)
         H_Erro("odbc_exec()");

     $query = "UPDATE configura
            SET tempociclo = '$tempociclo'";
     $qry = odbc_exec($ses, $query);

     if (!$qry)
         H_Erro("odbc_exec()");

     $query = "UPDATE configura
            SET intvelfil = '$intvelfil'";
     $qry = odbc_exec($ses, $query);

     if (!$qry)
         H_Erro("odbc_exec()");

     $query = "UPDATE configura
            SET grafpts = '$grafpts'";
     $qry = odbc_exec($ses, $query);

     if (!$qry)
         H_Erro("odbc_exec()");

     $query = "UPDATE configura
            SET pwmfreq = '$pwmfreq'";
     $qry = odbc_exec($ses, $query);

     if (!$qry)
         H_Erro("odbc_exec() pwmfreq");

     $query = "UPDATE configura
            SET motorzm = '$motorzm'";
     $qry = odbc_exec($ses, $query);

     if (!$qry)
         H_Erro("odbc_exec() motorzm");

     $query = "UPDATE configura
            SET pwmzm = '$pwmzm'";
     $qry = odbc_exec($ses, $query);

     if (!$qry)
         H_Erro("odbc_exec() pwmzm");

     $query = "UPDATE controle
            SET fatora = '$fatora'";
     $qry = odbc_exec($ses, $query);

     if (!$qry)
         H_Erro("odbc_exec() fatorb");
     $query = "UPDATE controle
            SET fatorb = '$fatorb'";
     $qry = odbc_exec($ses, $query);

     if (!$qry)
         H_Erro("odbc_exec() fatorc");
     $query = "UPDATE controle
            SET fatorc = '$fatorc'";
     $qry = odbc_exec($ses, $query);

     if (!$qry)
         H_Erro("odbc_exec() fatorc");


     if (!odbc_commit($ses))
         H_Erro("odbc_commit()");

     //print("pulsosvelcalc: [$pulsosvelcalc] <br>\n");
     //print("tempovelcalc: [$tempovelcalc] <br>\n");
     //print("tempociclo: [$tempociclo] <br>\n");
     //print("grafpts: [$grafpts] <br>\n");
     //print("intvelfil: [$intvelfil] <br>\n");
     //print("pwmfreq: [$pwmfreq] <br>\n");
     //print("motorzm: [$motorzm] <br>\n");
     //print("pwmzm: [$pwmzm] <br>\n");
     //print("fatora: [$fatora] <br>\n");
     //print("fatorb: [$fatorb] <br>\n");
     //print("fatorc: [$fatorc] <br>\n");


     //print("*** Processamento executado com sucesso *** <br>\n");
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

      print("$msg - ERRO, valor incorreto ou sintaxe: [$err] <br>\n");
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