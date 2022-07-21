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
## Version: {2021}.{12}.{11}                        ##
## Maintainer: {github.com/DanMartins/IoTControl}   ##
## Email: {github.com/DanMartins}                   ##
## Status: {Development}                            ##
/*%################################################################
-->
<?php
   print("<!DOCTYPE html>\n");
   print("<html>\n");
   print("<head>\n");
   print("\t<title>IoTControl</title>\n");
   print("<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n");

   $osrunning = php_uname();
   if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
    // Force ODBCINI to define unixODBC var to the browser user.
	putenv("ODBCINI=/etc/odbc.ini");

   }

   popen("/var/www/html/IoT_2DOF.sh", "r");

   // No warnings but errors ON
   error_reporting(E_ERROR | E_PARSE);

   $ses = Initialize();

   $query = "SELECT ajuste_0 FROM kgainmatrix";
   $qry = odbc_exec($ses, $query);
   $result = odbc_fetch_array($qry);

   $query = "SELECT ajuste_1 FROM kgainmatrix";
   $qry = odbc_exec($ses, $query);
   $result2 = odbc_fetch_array($qry);

   if (!$qry)
    H_Erro("odbc_exec(SELECT)");

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
	.columnA, .columnB, .columnC, .columnD {
	    float: left;
	    width: 23%;
	    height: 98%;
	    padding: 1%;
	    text-align:justify;
    }
    </style>
    <style>
    /* CSS property for content section */
	.colA, .colB, .colC {
	    float: left;
	    width: 33.3%;
	    height: 25vh;
	    padding: 0%;
	    text-align:center;
    }
    </style>
    <style>
    /* CSS property for content section */
	.clA, .clB {
	    float: left;
	    width: 50%;
	    height: 25vh;
	    padding: 0%;
	    text-align:center;
    }
    </style>

        <h1>Heli2DOF LQR</h1>
        <!-- Content section of website layout -->

	<table>
	<form method="post" action="CL_exe.php">
            <div class = "columnA">
                <h2>Control Yaw</h2>
                <input type="number" min="-100" max="100" step="0.1" value="<?php echo $result['ajuste_0'] ?>" name="ajuste"
	                   for="power" oninput="power.value=ajuste.value" style="width: 98%; font-size:1em; text-align:right;" >
                <p></p>
            </div>

	  <input type="range" min="-100" max="100"  step="0.1" value="<?php echo $result['ajuste_0'] ?>" name="power" list="powers"
	         style="height:25%; width:100%; float:center;"
	         for="ajuste" oninput="ajuste.value=power.value" >

     <datalist id="powers">
 		  <option value="-100" label="-100">
 		  <option value="-80">
 		  <option value="-60" label="-60">
 		  <option value="-40">
 		  <option value="-20" label="-20">
 		  <option value="0">
 		  <option value="20" label="20">
 		  <option value="40">
 		  <option value="60" label="60">
 		  <option value="80">
 		  <option value="100" label="100">
 		</datalist>

	  <br>
	  <input type="submit" style="height:16.6%; width:100%; font-size:1em;" value="Update">
	</form>

	<form method="post" action="CL2_exe.php">
            <div class = "columnA">
                <h2>Control Pitch</h2>
                <input type="number" min="-100" max="100" step="0.1" value="<?php echo $result2['ajuste_1'] ?>" name="ajuste"
	                   for="power" oninput="power.value=ajuste.value" style="width: 98%; font-size:1em; text-align:right;" >
                <p></p>
            </div>

	  <input type="range" min="-100" max="100"  step="0.1" value="<?php echo $result2['ajuste_1'] ?>" name="power" list="powers"
	         style="height:25%; width:100%; float:center;"
	         for="ajuste" oninput="ajuste.value=power.value" >

     <datalist id="powers">
 		  <option value="-100" label="-100">
 		  <option value="-80">
 		  <option value="-60" label="-60">
 		  <option value="-40">
 		  <option value="-20" label="-20">
 		  <option value="0">
 		  <option value="20" label="20">
 		  <option value="40">
 		  <option value="60" label="60">
 		  <option value="80">
 		  <option value="100" label="100">
 		</datalist>

	  <br>
	  <input type="submit" style="height:16.6%; width:100%; font-size:1em;" value="Update">
	</form>

<!--
	<div class = "colA">
	   <form action="Aumenta.php" method="post">
	   <input type="submit" style="height:100%; width:100%; font-size:1em;" value="+100">
	  </form>
	</div>

	<div class = "colB">
      <form action="Zero.php" method="post">
      <input type="submit" style="height:100%; width:100%; font-size:1em;" value="Zero">
      </form>
	</div>

	<div class = "colC">
	  <form action="Diminui.php" method="post">
	  <input type="submit" style="height:100%; width:100%; font-size:1em;" value="-100">
	  </form>
	</div>
-->
	<div class = "clA">
      <form action="ggraph_mimo.php" method="post">
      <input type="submit" style="height:100%; width:100%; font-size:1em;" value="Graph">
      </form>
	</div>

	<div class = "clB">
      <form action="index.html" method="post">
      <input type="submit" style="height:100%; width:100%; font-size:1em;" value="Return">
      </form>
    </div>

    </table>
    </head>
    <body>
    </body>
</html>
