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
   $osrunning = php_uname();
   if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
    // Force ODBCINI to define unixODBC var to the browser user.
	putenv("ODBCINI=/etc/odbc.ini");

   }

   // No warnings but errors ON
   error_reporting(E_ERROR | E_PARSE);

   $ses = Initialize();

   $query = "SELECT ajuste FROM controle";

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
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- <h3>Applied Control Laboratory (LAC) located in the Department of Telecommunications and Control Engineering (PTC) </h3>
<!-- <h3>at Polytechnic School – University of São Paulo</h3>
<!-- <h3>Doutorando: Danilo Oliveira Martins</h3>
-->
<style>
    /* CSS property for content section */
	.cA {
	    float: left;
	    width: 100%;
	    height: 86%;
	    padding: 0%;
        border-style: solid;
        border-color: #d3d3d3;
	    text-align:justify;
    }
</style>
<style>
    /* CSS property for content section */
	.c_field {
	    float: left;
	    width: 100%;
	    height: 7vh;
	    padding: 0%;
	    text-align:justify;
    }
</style>
<style>
    /* CSS property for content section */
	.columnA, .columnB, .columnC, .columnD, .columnE {
	    float: left;
	    width: 20%;
	    height: 7vh;
	    padding: 0%;
	    text-align:justify;
    }
</style>
<title>IoTControl</title>
  <!--<h1>Graph</h1>-->

<div class = "cA">
<img src="graph.php" alt="Graphic View" style="width:100%;height:100%; padding: 0%; object-fit: fill;"/>
</div>
<br>
<form method="post" action="update_exe.php">
<div class = "c_field">
  <input type="range" min="-400" max="400"  step="10" value="<?php echo $result['ajuste'] ?>" name="ajuste" list="powers"
		 style="height:100%; width:100%; float:center;">
	<datalist id="powers">
	  <option value="-400" label="-400">
	  <option value="-350">
	  <option value="-300">
	  <option value="-250">
	  <option value="-200" label="-200">
	  <option value="-150">
	  <option value="-100">
	  <option value="-50">
	  <option value="0" label="0">
	  <option value="50">
	  <option value="100">
	  <option value="150">
	  <option value="200" label="200">
	  <option value="250">
	  <option value="300">
	  <option value="350">
	  <option value="400" label="400">
	</datalist>
</div>

<div class = "columnA">
	<input type="submit" style="height:100%; width:100%; font-size:1em;" value="Update">
</div>
</form>

<div class = "columnB">
		<form action="Aumenta.php" method="post">
		<input type="submit" style="height:100%; width:100%; font-size:1em;" value="+100">
		</form>
</div>
<div class = "columnC">

		<form action="Zero.php" method="post">
		<input type="submit" style="height:100%; width:100%; font-size:1em;" value="Zero">
		</form>
</div>
<div class = "columnD">
		<form action="Diminui.php" method="post">
		<input type="submit" style="height:100%; width:100%; font-size:1em;" value="-100">
		</form>
</div>
<div class = "columnE">
		<form action="seleciona.html" method="post">
		<input type="submit" style="height:100%; width:100%; font-size:1em;" value="Control">
		</form>
</div>
</head>
</html>