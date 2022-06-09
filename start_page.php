<?php session_start(); ?>

<link rel="preload" href="CSS/start.css" as="style">
<link href="CSS/start.css" rel="stylesheet">

<?php

include 'login.php';

$query = "SELECT category, subcategory, img FROM catalog";

$subcat_array = array();
$cat_array = array();
$img_array = array();

if ($searchresult = $mysqli->query($query)) {
	while ($row = $searchresult->fetch_assoc()) {
		if (!in_array(array($row["category"], $row["subcategory"]), $subcat_array)) {
			$subcat_array[] = array($row["category"], $row["subcategory"]);
            if (!in_array(array($row["category"]), $cat_array)) {
                $cat_array[] = array($row["category"]);
            }
            if (!in_array(array($row["category"], $row["subcategory"], $row["img"]), $img_array)) {
			    $img_array[] = array($row["category"], $row["subcategory"], $row["img"]);
            }
		} 	
	}
	$searchresult->free();
} ?>

<div> <?php

foreach ($cat_array as list($category)) {
    foreach ($subcat_array as list($c, $s)) {
        if ($c == $category) { ?>
            <div class='<?php echo $category ?>'> <?php
                foreach ($img_array as list($ic, $is, $i)) {
                    if ($ic == $category && $is == $s) { ?>
                        <div><img src="<?php echo $i ?>" alt="Pic"></div> <?php 
                    }
                } ?>
                <div><?php printf ($s); ?></div>
            </div> <?php
        }
    }
} ?>

</div> <?php

$mysqli->close();
?>

<script>
    $('main > div > div').click( function() {
        $('.active').removeClass('active');
        $('main').html("<div class='lds-ellipsis'><div></div><div></div><div></div><div></div></div>");
        var subcat_data = $(this).children().last().text();
        var cat_data = $(this).attr('class');
        $('main').load( 'search.php', { subcat: subcat_data, cat: cat_data } );
        history.pushState({ subcat: subcat_data, cat: cat_data }, "", "/" + cat_data + "_" + subcat_data);
    });
</script>