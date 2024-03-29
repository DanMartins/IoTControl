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
## Version: {2021}.{11}.{08}                        ##
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

   //arrays
   $datarray = array();
   $ajustevel = array();
   $pwmout = array();
   $kfeedback = array();
   $table = array();
   //vars
   $tcount = 0;
   $offset = 0;
   $pts = 0;
   $samples = 10000;

   $ses = Initialize();
   if (!$ses)
     goto end;

   // First SQL query
   $query = "SELECT ajuste FROM controle";
   $qry = odbc_exec($ses, $query);

   $result = odbc_fetch_array($qry);

   if (!$qry)
    H_Erro("odbc_exec(SELECT TOP)");

   // Second SQL query
   $qry = odbc_exec($ses, "SELECT grafpts FROM configura");

   $info = odbc_fetch_array($qry);
   $pontos = $info["grafpts"];

   if (!$qry)
    Handle_Error("odbc_exec(SELECT)");

   // Third SQL query
   $query = "WITH BottomX (velocidade, tempo, ajuste, erro, kmotor) AS (SELECT TOP " . strval($samples);
   $query = $query . "  velocidade, tempo, ajuste, erro, kmotor FROM dados ORDER BY tempo DESC) SELECT * FROM BottomX ORDER BY tempo ASC";

   // ODBC exec
   $qry = odbc_exec($ses, $query);

   // ODBC fetch
   $info = odbc_fetch_array($qry);
   $veloc = $info["velocidade"];
   $tem = floatval(substr($info["tempo"], -6, -1)) + 60*floatval(substr($info["tempo"], -9, -8)) + 3600*floatval(substr($info["tempo"], -12, -11));//substr($info["tempo"], 16, -1);
   $datarray[$tem]= $veloc;
   $ajustevel[$tem] = $info["ajuste"];
   $pwmout[$tem] = $info["erro"];
   $kfeedback[$tem] = $info["kmotor"];

   $table[$tcount] = array("T","IN","VM","VT","VP");
   $tcount=$tcount+1;
   $table[$tcount] = array($tem, floatval($info["ajuste"]), floatval($info["erro"]), floatval($info["velocidade"]), floatval($info["kmotor"]));

   while ($info = odbc_fetch_array($qry)){
       $veloc = $info["velocidade"];
       $tem = floatval(substr($info["tempo"], -6, -1)) + 60*floatval(substr($info["tempo"], -9, -8)) + 3600*floatval(substr($info["tempo"], -12, -11));//substr($info["tempo"], 16, -1);
       $datarray[$tem]= $veloc;
       $ajustevel[$tem] = $info["ajuste"];
       $pwmout[$tem] = $info["erro"];
       $kfeedback[$tem] = $info["kmotor"];

       $tcount=$tcount+1;
       $table[$tcount] = array($tem, floatval($info["ajuste"]), floatval($info["erro"]), floatval($info["velocidade"]), floatval($info["kmotor"]));
   }

   // interface vars - server/client: PHP/JS
   $pts = intval($pontos);
   $offset = (count($table)-$pts);

   if (!$qry)
    Handle_Error("odbc_exec(SELECT TOP)");

   end:
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
        return(0);
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
      //exit();
   }

   print("</head>\n");
   print("<body>\n");

   print("</body>\n");
   print("</html>\n");

// end of PHP_IoT_ODBC.php
?>
<!DOCTYPE html>
<html>
<link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- <h3>Applied Control Laboratory (LCA) located in the Department of Telecommunications and Control Engineering (PTC) </h3>
<!-- <h3>at Polytechnic School � University of S�o Paulo</h3>
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
  <div id="dom-target" style="display: none;">
    <?php
      $jsonTable = json_encode($table);
      $output = $tcount; // Again, do some operation, get the output.
      echo htmlspecialchars($jsonTable); /* You have to escape because the result
                                           will not be valid HTML otherwise. */
    ?>
  </div>

  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
  <div id="chart_div" style="width:100%; height:100%"></div>
</div>
<br>

<form method="post" action="upd_graph_exe.php">
<div class = "c_field">
  <input type="range" min="0" max="400"  step="10" value="<?php echo $pontos ?>" name="grafpts" list="powers"
		 style="height:100%; width:100%; float:center;">
	<datalist id="powers">
	  <option value="0" label="0">
	  <option value="25">
	  <option value="50">
	  <option value="75">
	  <option value="100" label="100">
	  <option value="125">
	  <option value="150">
	  <option value="1750">
	  <option value="200" label="200">
	  <option value="225">
	  <option value="250">
	  <option value="275">
	  <option value="300" label="300">
	  <option value="325">
	  <option value="350">
	  <option value="375">
	  <option value="400" label="400">
	</datalist>
</div>

<div class = "columnA">
	<input type="submit" style="height:100%; width:100%; font-size:1em;" value="Update">
</div>
</form>

<div class = "columnB">
		<input type="button" onclick="drawChartLine(-1)" style="height:100%; width:100%; font-size:1em;" value="<<<"/>
</div>

<div class = "columnC">
		<input type="button" onclick="drawChartLine(0)" style="height:100%; width:100%; font-size:1em;" value="---"/>

</div>

<div class = "columnD">
		<input type="button" onclick="drawChartLine(1)" style="height:100%; width:100%; font-size:1em;" value=">>>"/>

</div>

<div class = "columnE">
		<!--
		<form action="index.html" method="post">
		<input type="submit" style="height:100%; width:100%; font-size:1em;" value="Control">
		</form>

		<input type="button" onclick="saveData2File()" style="height:100%; width:100%; font-size:1em;" value="Log"/>
    -->
    <form>
    <select name="formcontrol" onchange="oPtions(this.value);"
     style="height:100%; width:100%; font-size:1em; text-align-last: center;">
     <option value="">Options</option>
        <option value="Log">Log</option>
        <option value="Load">Load</option>
    </select>
    </form>
</div>

<script type="text/javascript">
// Load google charts
google.charts.load('current', {packages: ['corechart', 'line']});
google.charts.setOnLoadCallback(drawCurveTypes);

// global vars
var options = {
    'title':'IoTControl',
    //'width':900,//'width':1350,
    //'width_units':'%',
    //'height':900,//'height':550,
    //'height_units':'%',
    hAxis: {
      title: 'Time',
      gridlines : {count : 12}
    },
    vAxis: {
      title: 'Volts',
      //viewWindow:{
      //  max:8,
      //  min:-8
      //},
      gridlines : {count : 12}
    },
    colors: ['black', 'green','red','blue'],//"IN","VM","VT","VP"
    series: {
      1: {
        curveType: 'none'
      }
    }
};
var offset_data = <?=$offset?>;

var offset = <?=$offset?>;
var pontos = <?=$pts?>;
let table = <?=$jsonTable?>;
let tableview = new Array(pontos);

// JS functions
function drawCurveTypes() {
  tableview = table.slice(offset, (offset + pontos));//$tableview = array_slice($table, $offset, intval($pontos));
  tableview[0] = table[0];

  var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
  var data = new google.visualization.arrayToDataTable(tableview, false);
  chart.draw(data, options);

}

function drawChartLine(x) {
  if (x == 0){
    offset = offset_data;
  }
  else if (x > 0){
    offset = offset + (pontos/4);
    if (offset >= (offset_data+(pontos*3/4))){offset = (offset_data+(pontos*3/4));}
  }
  else {
    offset = offset - (pontos/4);
    if (offset <= 0){offset = 0;}
  }

  //Update Graph
  drawCurveTypes();
}

//convert data to file
function convertToCSV(objArray) {
    var array = typeof objArray != 'object' ? JSON.parse(objArray) : objArray;
    var str = '';

    for (var i = 0; i < array.length; i++) {
        var line = '';
        for (var index in array[i]) {
            if (line != '') line += ','

            line += array[i][index];
        }

        str += line + '\r\n';
    }

    return str;
}

function csvToArray(str, option, delimiter = ",") {
  // slice from start of text to the first \n index
  // use split to create an array from string by delimiter
  const headers = str.slice(0, str.indexOf("\r\n")).split(delimiter);

  // slice from \n index + 1 to the end of the text
  // use split to create an array of each csv value row
  const rows = str.slice(str.indexOf("\r\n") + 2).split("\r\n");

  let arr_var = new Array();
  var i = 0;

  if (option == 1){//$table[$tcount] = array("T","IN","VM","VT","VP");
    arr_var[i] = ["T","IN","VM","VT","VP"];
    i++;
  }


  // Map the rows
  // split values from each row into an array
  // use headers.reduce to create an object
  // object properties derived from headers:values
  // the object passed as an element of the array
  const arr = rows.map(function (row) {
    const values = row.split(delimiter);
    const el = headers.reduce(function (object, header, index) {
      if (option == 1)
      {
        if (header.includes("2")){}
	else{
		object[header] = parseFloat(values[index]);
	}
      }
      else
      {
	  object[header] = parseFloat(values[index]);
      }
      return object;
    }, {});
    if (option == 1){//"T","IN","VM","VT","VP")
       arr_var[i] = [el.T.valueOf(), el.IN.valueOf(), el.VM.valueOf(), el.VT.valueOf(), el.VP.valueOf()];
    }
    i++;
    return el;
  });

  // return the array
  return arr_var;

}

function saveData2File() {
  var str = JSON.stringify(<?=$jsonTable?>, null, 2);
  var csv = this.convertToCSV(str);
  let csvContent = "data:text/csv;charset=utf-8,"
    + csv;
  var encodedUri = encodeURI(csvContent);

  window.open(encodedUri);
}

function saveFile2Data() {

   var input = document.createElement('input');
   input.type = 'file';

   input.onchange = e => {

     // getting a hold of the file reference
     var file = e.target.files[0];

     // setting up the reader
     var reader = new FileReader();
     reader.readAsText(file,'UTF-8');

     // here we tell the reader what to do when it's done reading...
     reader.onload = readerEvent => {
        var content = readerEvent.target.result; // this is the content!
        //console.log( content );

        let data_array = new Array();
        data_array = csvToArray(content,1);
        table = data_array; //$table[$tcount] = array("T","IN","VM","VT","VP"));

        offset_data = (table.length - pontos);
        offset = offset_data;
        //Update Graph
        drawCurveTypes();
     }

  }
  input.click();
}



// to adapt chart to screen
function resizeChart () {
   var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
   var data = new google.visualization.arrayToDataTable(tableview, false);
   chart.draw(data, options);
}
function oPtions(value){
    if (value=="Log"){
      saveData2File()
    }
    else if(value=="Load"){
      saveFile2Data()
    }
}
if (document.addEventListener) {window.addEventListener('resize', resizeChart);}
else if (document.attachEvent) {window.attachEvent('onresize', resizeChart);}
else {window.resize = resizeChart;}

</script>
</head>
</html>
