<?php
$servername = "127.0.0.1";
$database = "magnakarsa";
$username = "root";
$password = "";

    // Create connection
$conn = new mysqli($servername, $username, $password, $database);

    // Check connection
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}

function get_tree($name = "root") { //40pts
	global $conn;
	$tree = array();
	$q = "SELECT * FROM member where name = '".$name."'";
	$exec = mysqli_query($conn,$q);

	$res = mysqli_fetch_array($exec);
	$children = get_children($name);
	if (count($children) > 0) {
		$tree = array(
			"name" => $name,
			"children" => $children
		);
	}
	return $tree;
}


function get_parents($name) { //25pts
	global $conn;
//mengambil semua parent dari input mulai dari direct-parent sampai root member
//mengembalikan array of string berisi nama parent urut dari yang paling dekat
	$parents = array();
	$q_init = "SELECT * FROM member where name = '".$name."'";
	$exec_init = mysqli_query($conn,$q_init);
	$res_init = mysqli_fetch_array($exec_init);

	$q = "SELECT * FROM member where id = ".$res_init["parent_id"]."&& parent_id != ".$res_init["id"];
	$exec = mysqli_query($conn,$q);

	while($data = mysqli_fetch_assoc($exec)) {
		$children = get_parents($data["name"]);
		$template = array(
			"name" => $data["name"],
			"parent" => $children
		);
		array_push($parents, $template);
	}
	return $parents;
}

function get_children($name) { //25pts
	global $conn;
//mengambil semua parent dari input mulai dari direct-parent sampai root member
//mengembalikan array of string berisi nama parent urut dari yang paling dekat
	$children = array();
	$q_init = "SELECT * FROM member where name = '".$name."'";
	$exec_init = mysqli_query($conn,$q_init);
	$res_init = mysqli_fetch_array($exec_init);

	$q = "SELECT * FROM member where parent_id = ".$res_init["id"]." && id != ".$res_init["id"];
	$exec = mysqli_query($conn,$q);

	while($data = mysqli_fetch_assoc($exec)) {
		$child = get_children($data["name"]);
		$template = array(
			"name" => $data["name"],
			"children" => $child
		);
		array_push($children, $template);
	}
	return $children;
}
// function get_children($name) { //15pts
// 	global $conn;
// //mengambil semua direct-child dari input
// //mengembalikan array of string yang berisi daftar nama child
// 	$children = array();
// 	$q_init = "SELECT * FROM member where name = '".$name."'";
// 	$exec_init = mysqli_query($conn,$q_init);
// 	$res_init = mysqli_fetch_array($exec_init);

// 	$q = "SELECT * FROM member where parent_id = ".$res_init["id"]." && id != ".$res_init["id"];
// 	$exec = mysqli_query($conn,$q);

// 	while($data = mysqli_fetch_assoc($exec)) { 
// 		$children = get_children($data["name"]);
// 		$template = array(
// 			"name" => $data["name"],
// 			"children" => $children
// 		);
// 		array_push($children, $template);
// 	}
// 	return $children;
// }
$tree = get_tree();
echo json_encode($tree);
echo "<br><br><hr><br><br>";

$tree = get_tree('Andre');
echo json_encode($tree);
echo "<br><br><hr><br><br>";
/* akan menulis :
{"name":"Andre","children":[{"name":"Neil","children":[{"name":"Derp","children":[
{"name":"Derpina","children":[]}]}]}]}
*/
$parents = get_parents('Marge');
echo json_encode($parents);
echo "<br><br><hr><br><br>";
/* akan menulis : ["Jessica", "root"] */
$parents = get_parents('Derpina');
echo json_encode($parents);
echo "<br><br><hr><br><br>";
/* akan menulis : ["Derp", "Neil", "Andre", "Andrea", "Maya", "root"] */
$children = get_children('Samantha');
echo json_encode($children);
echo "<br><br><hr><br><br>";
/* akan menulis : ["James", "April", "Charles"] */
$children = get_children('John');
echo json_encode($children);
/* akan menulis : [] */


?>