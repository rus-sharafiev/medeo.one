<?php session_start();

include 'login.php';

$query = "SELECT direction, category, subcategory FROM catalog";

$subcat_array = array();
$cat_array = array();
$direction_array = array();

if ($searchresult = $mysqli->query($query)) {
	while ($row = $searchresult->fetch_assoc()) {

		if (!in_array(array($row["direction"], $row["category"], $row["subcategory"]), $subcat_array)) {
			$subcat_array[] = array($row["direction"], $row["category"], $row["subcategory"]);

            if (!in_array(array($row["direction"], $row["category"]), $cat_array)) {
                $cat_array[] = array($row["direction"], $row["category"]); 

                if (!in_array(array($row["direction"]), $direction_array)) {
                    $direction_array[] = array($row["direction"]);
                }
            }
		} 	
	}
	$searchresult->free();
}

foreach ($direction_array as list($direction)) { ?>
    <div>
        <div id='<?php printf ($direction); ?>'><?php printf ($direction); ?></div> 
        <div> <?php
        foreach ($cat_array as list($d, $category)) {
            if ($d == $direction) { ?>
                <div>
                    <div><?php printf ($category); ?></div>
                    <div> <?php
                        foreach ($subcat_array as list($d, $c, $subcategory)) {
                            if ($c == $category && $d == $direction) { ?>
                                <div><?php printf ($subcategory); ?></div>
                            <?php
                            }
                        } ?>
                    </div>
                </div> <?php
            }
        }?>
        </div>
    </div> <?php
}

$mysqli->close();
?>