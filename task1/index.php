<?php
//TODO: set up Mysql connection;
$server = "127.0.0.1:3306";
$database = "mobile";
$user = "root";
$password = "";
$mysqli = new mysqli($server, $user, $password, $database);

//Check to see if we can connect to the server
if(!$mysqli) {
    die("Database server connection failed.");
} else {
    //Attempt to select the database
    $dbconnect = mysqli_select_db($mysqli, $database);
}

//TODO: Fill the array of manufacturer IDs and titles (e.g. "33" => "Alfa Romeo")
$manufacturers = array();
$manufacturer_handle = $mysqli->query("select id, title from manufacturers order by title");
while ($row = $manufacturer_handle->fetch_assoc()) {
    $manufacturers [$row["id"]] = $row["title"];
}

//TODO: Fill the array of color IDs and titles (e.g. "19" => "Tumši pelēka" (dark grey)) 
$colors = array();
$colors_handle = $mysqli->query("select id, title from colors order by title");
while ($row = $colors_handle->fetch_assoc()) {
    $colors [$row["id"]] = $row["title"];
}

//TODO: collect and sanitize the current inputs from GET data
if (isset($_GET['year'])) {
    $year = $_GET['year'];
}
if (isset($_GET['manufacturer'])) {
    $manufacturer = $_GET['manufacturer'];
}
if (isset($_GET['color'])) {
    $color = $_GET['color'];
}
//$manufacturer = "";
//$color = "";

//$year = $_GET["year"];
//$manufacturer = $_GET["manufacturer"];
//$color = $_GET["color"]

$results_handle = $mysqli->prepare("select
	manufacturers.title as manufacturer,
	models.title as model,
	count(*) as count
from
	manufacturers
inner join models on
	manufacturer_id = manufacturers.id
inner join cars on
	cars.model_id = models.id
where
	manufacturer_id = ? #volkswagen
	and color_id = ? #black
	and cars.registration_year = ? #2010
group by
	manufacturers.title,
	models.title
order by
	count desc
");

$results_handle->bind_param("iii", $manufacturer, $color, $year);
$results_handle->execute();

//TODO: connect to database, make a query, collect results, save it to $results array as objects
$results = array();
$result = $results_handle->get_result();
while ($row = $result->fetch_assoc()) {
    $results[] = $row; 
}
  $mysqli -> close();

//while ($row = $results_handle->fetch_assoc()) {
//    $results[] = $row;
//}

//TODO: complete the view file
require("view.php");


require("../task2/logger.php");
$logger = new Logger("/Applications/XAMPP/logs/out.txt");
$logger->log("OK");