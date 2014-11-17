<!doctype html>
<html lang="en">

<head>
	<link rel="stylesheet" href="TreeStyle.css" />
	<title>Tree View</title>
</head>

<body>

<div>
	<h3>Tree View</h3>
	<div id="simpleTreeContainer"></div>
</div>

<script type="text/javascript"
		src="http://code.jquery.com/jquery-1.10.1.js"></script>
<script type="text/javascript" src="jsonTree.js"></script>

<?php
$json_data = file('data.json');
$data = json_decode($json_data[0], true);

$id = 0;
$result = array();

function attributes($attributes, &$id) {
	$result['ItemId'] = ++$id;
	$result['Title'] = "attributes";
	$result['Items'] = array();
	foreach($attributes as $key => $attr) {
		$k['ItemId'] = ++$id;
		$k['Title'] = $key;
		$k['Items'] = array();
		$v['ItemId'] = ++$id;
		$v['Title'] = $attr;
		array_push($k['Items'], $v);
		array_push($result['Items'], $k);
	}
	return $result;
}

function childs($childs, &$id) {
	$result['ItemId'] = ++$id;
	$result['Title'] = "childs";
	$result['Items'] = array();
	foreach($childs as $key => $child) {
		$k['ItemId'] = ++$id;
		$k['Title'] = $key;
		$k['Items'] = array();
		array_push($k['Items'], element($child, $id));
		array_push($result['Items'], $k);
	}
	return $result;
}

function value($string, &$id) {
	$k['ItemId'] = ++$id;
	$k['Title'] = "value";
	$k['Items'] = array();
	$value['ItemId'] = ++$id;
	$value['Title'] = $string;
	array_push($k['Items'], $value);
	return $k;
}

function element($elements, &$id) {
	$result = array();
	foreach($elements as $key => $element) {
		switch ($key) {
		case "attributes":
			array_push($result, attributes($element, $id));
			break;
		case "value":
			array_push($result, value($element, $id));
			break;
		case "child_nodes":
			array_push($result, childs($element, $id));
			break;
		default:
			$result['ItemId'] = ++$id;
			$result['Title'] = $key;
			$result['Items'] = array();
			array_push($result['Items'], element($element, $id));
			break;
		}
	}
	return $result;
}

$result = element($data, $id);

?>

<script>
var jsonD = <?php echo json_encode($result); ?>;

$(document).ready(function () {

	$('#simpleTreeContainer').jsonTree(jsonD, {
		mandatorySelect: true,
		selectedIdElementName: 'simpleTreeContainer',
		selectedItemId: 'simpleTreeContainer'
	});
});
</script>

</body>
</html>
