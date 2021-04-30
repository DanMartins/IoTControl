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
   print("<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n");

   print("\t<title>IoTControl</title>\n");


   $osrunning = php_uname();
   if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
    // Force ODBCINI to define unixODBC var to the browser user.
	putenv("ODBCINI=/etc/odbc.ini");

   }

   // No warnings but errors ON
   error_reporting(E_ERROR | E_PARSE);

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
    /* CSS property for content section */
	.cl1 {
	    float: left;
	    width: 100%;
	    height: 5%;
	    padding: 1%;
	    text-align:justify;
    }
    </style>
    <style>
    /* CSS property for content section */
	.columnA, .columnB, .columnC, .columnD {
	    float: left;
	    width: 23%;
	    height: 20%;
	    padding: 1%;
	    text-align:justify;
    }
    </style>
    <style>
    /* CSS property for content section */
	.colA, .colB, .colC {
	    float: left;
	    width: 33.3%;
	    height: 20%;
	    padding: 0%;
	    text-align:center;
    }
    </style>
    <style>
    /* CSS property for content section */
	.clA, .clB {
	    float: left;
	    width: 50%;
	    height: 20%;
	    padding: 0%;
	    text-align:center;
    }
    </style>
	<table>
        <!--<h1>Configuration</h1>
        <!-- Content section of website layout -->
	<form method="post" action="configuraexe.php">
	<div class = "clA">
		<h2>Control loop (s)</h2>
		<input type="number" min="0" max="100" step="0.0001" style="width: 98%; font-size:1em;"
			   name="tempociclo" value="<?php echo $result['tempociclo'] ?>">
		<p></p>
	</div>

	<div class = "clB">
		<h2>Storage (s)</h2>
		<input type="number" min="0" max="2000" step="0.01" style="width: 98%; font-size:1em;"
			   name="tempovelcalc" value="<?php echo $result['tempovelcalc'] ?>">
		<p></p>
	</div>

	<div class = "colA">
		<h2>Encoder (ppr)</h2>
		<input type="number" min="0.1" max="2000" step="0.1" style="width: 98%; font-size:1em;"
			   name="pulsosvelcalc" value="<?php echo $result['pulsosvelcalc'] ?>">
		<p></p>
	</div>

	<div class = "colB">
		<h2>Graph (points)</h2>
		<input type="number" min="1" max="2000" step="1" style="width: 98%; font-size:1em;"
			   name="grafpts" value="<?php echo $result['grafpts'] ?>">
		<p></p>
	</div>

	<div class = "colC">
		<h2>Filter speed (n)</h2>
		<input type="number" min="0" max="1" step="0.01" style="width: 98%; font-size:1em;"
			   name="intvelfil" value="<?php echo $result['intvelfil'] ?>">
		<p></p>
	</div>

	<div class = "colA">
		<h2>PWM (Hz)</h2>
		<input type="number" min="10" max="8000" step="1" style="width: 98%; font-size:1em;"
			   name="pwmfreq" value="<?php echo $result['pwmfreq'] ?>">
		<p></p>
	</div>

	<div class = "colB">
		<h2>Deadband (p/s)</h2>
		<input type="number" min="0" max="2000" step="0.01" style="width: 98%; font-size:1em;"
			   name="motorzm" value="<?php echo $result['motorzm'] ?>">
		<p></p>
	</div>

	<div class = "colC">
		<h2>Deadband (%)</h2>
		<input type="number" min="0" max="100" step="0.001" style="width: 98%; font-size:1em;"
			   name="pwmzm" value="<?php echo $result['pwmzm'] ?>">
		<p></p>
	</div>

	<div class = "colA">
		<h2>A (model)</h2>
		<input type="number" min="-10000.0" max="10000.0" step="0.000001" style="width: 98%; font-size:1em;"
			   name="fatora" value="<?php echo $result2['fatora'] ?>">
		<p></p>
	</div>
	<div class = "colB">
		<h2>B (model)</h2>
		<input type="number" min="-10000.0" max="10000.0" step="0.000001" style="width: 98%; font-size:1em;"
			   name="fatorb" value="<?php echo $result2['fatorb'] ?>">
		<p></p>
	</div>
	<div class = "colC">
		<h2>C (model)</h2>
		<input type="number" min="-10000.0" max="10000.0" step="0.000001" style="width: 98%; font-size:1em;"
			   name="fatorc" value="<?php echo $result2['fatorc'] ?>">
		<p></p>
	</div>
	<div class = "clA">
	<input type="submit" style="height:100%; width:100%; font-size:1em; padding: 5%;" value="Update">
	</div>
	</form>

	<form action="index.html" method="post">
    <div class = "clB">
	<input type="submit" style="height:100%; width:100%; font-size:1em; padding: 5%;" value="Return">
    </div>
    </form>
	</head>
    <body>
    </body>
</html>