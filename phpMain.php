<?php 
	//Whattodo This variable tell us what function comes next
	$whattodo = $_POST["whattodo"];
	$table = $_POST["table"];
	$fields = $_POST["fields"];

	if ($whattodo == "insert") {
		$values = $_POST["values"];
		insert($table,$fields,$values);
	} elseif ($whattodo == "update") {
		$filter = $_POST["filter"]; //If we're tying to do an update we need a filter 
		$values = $_POST["values"];
		update($table, $fields, $values,$filter);
	} elseif ($whattodo == "delete") {
		$values = $_POST["values"];
		delete($table, $fields, $values);
	} elseif ($whattodo == "select") {
		$filter = $_POST["filter"]; //If we're tying to do a select we need a filter 
		select($table, $fields, $filter);
	}

//FUNCTIONS BEGIN
	function insert($table, $fields, $values)
	{
		global $conn;
		global $PDO;
	    $sql = "INSERT INTO " . $table ." (". $fields .") VALUES (". $values .")";
	    executeSQL($sql);
	}

	function update($table, $fields, $values,$filter)
	{
		#UPDATE t1 SET col1 = col1 + 1, col2 = col1;
		global $conn;
		global $PDO;

    	$valuesArray = explode(",", $values);
    	$fieldsArray = explode(",", $fields);

    	$counter = 0;
		foreach ($fieldsArray as &$val) 
		{
			if ($counter == 0) {
				$newvalues = "SET ". $val . " = ". $valuesArray[$counter];
			} else {
				$newvalues = $newvalues. " , " . $val . " = " . $valuesArray[$counter];
			}
		$counter++;
		}

	    $sql = "UPDATE " . $table ." ". $newvalues ." WHERE ". $filter;
	    executeSQL($sql);
	}

	function delete($table, $fields, $values)
	{
		global $conn;
		global $PDO;

    	$valuesArray = explode(",", $values);
    	$fieldsArray = explode(",", $fields);

    	$counter = 0;
		foreach ($fieldsArray as &$val) 
		{
			if ($counter == 0) {
				$newvalues = "WHERE ". $val . " = ". $valuesArray[$counter];
			} else {
				$newvalues = $newvalues. " AND " . $val . " = " . $valuesArray[$counter];
			}
		$counter++;
		}

	    $sql = "DELETE FROM " . $table ." ". $newvalues;
	    executeSQL($sql);
	}

	function select($table, $fields, $filter)
	{
		global $conn;
		global $PDO;

		//For security reasons we must store our connections data on a protected txt
		$dataForConnection = file_get_contents("../pr/pr.txt");
		$dataForConnection = explode(",", $dataForConnection);

		$conn =  mysql_connect($dataForConnection[0], $dataForConnection[1], $dataForConnection[2],$dataForConnection[3]);
		mysql_select_db('dondeesta');
		$result=mysql_query("SELECT " . $fields ." FROM ". $table . " WHERE " . $filter);

		//$result = mysql_query("SELECT id,email FROM people WHERE id = '42'");

		if (!$result) 
		{
    		echo 'Could not run query: ' . mysql_error();
    		exit;
		}
	    
	    $i = 0;
	    while($datos = mysql_fetch_row($result))
	    {
			foreach ($datos as $elem)
			{
	      		$arrayresult[$i] = $elem;
	      		$i++;
	      	}
	    }

	    $stringresult = "";
	    $cont = 0;
		foreach ($arrayresult as &$valor) 
		{
			if ($cont == 0) {
				$stringresult = $valor;
			} else {
				$stringresult = $stringresult.",".$valor;
			}
			$cont++;
		}

	    echo $stringresult;
	}

	function executeSQL($sql)
	{
		try 
		{
			global $conn;
			//For security reasons we must store our connections data on a protected txt
			$dataForConnection = file_get_contents("../pr/pr.txt");
			$dataForConnection = explode(",", $dataForConnection);
		    $conn = new PDO("mysql:host=".$dataForConnection[0].";dbname=".$dataForConnection[3]."", "".$dataForConnection[1]."","".$dataForConnection[2]."");
		    // set the PDO error mode to exception
		    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		    $conn->exec("SET NAMES utf8");
		    $conn->exec($sql);
		 }
		catch(PDOException $e)
		    {
		    	echo $sql . "<br>" . $e->getMessage();
		    }
		$conn = null;
	}
?>