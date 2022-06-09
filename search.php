<?php session_start(); ?>

<?php include 'login.php';

$id = $_POST['id'] ?? NULL;
$cat = $_POST['cat'] ?? NULL;
$subcat = $_POST['subcat'] ?? NULL;
$name = $_POST['name'] ?? NULL;
$q = $_POST['q'] ?? NULL;

if ($subcat != NULL && $cat != NULL) {
    $query = "SELECT * FROM catalog WHERE category = '$cat' AND subcategory = '$subcat'";
} else if ($id != NULL) {
    $query = "SELECT * FROM catalog WHERE id = '$id'";
} else if ($cat != NULL) {
    $query = "SELECT * FROM catalog WHERE category = '$cat'";
} else if ($subcat != NULL) {
    $query = "SELECT * FROM catalog WHERE subcategory = '$subcat'";
} else if ($name != NULL) {
    $query = "SELECT * FROM catalog WHERE name = '$name'";
} else {
    $query = "SELECT * FROM catalog WHERE
    category LIKE '%$q%' OR 
    subcategory LIKE '%$q%' OR 
    name LIKE '%$q%' OR 
    id LIKE '$q%'";
}

if ($stmt = $mysqli->prepare($query)) {
    $stmt->execute();
    $stmt->store_result();
	$stmt->bind_result(
		$id,
        $direction,
        $category,
        $subcategory,
        $name,
        $name_add,
        $dia,
        $lenth,
        $add,
        $img,
        $handle);
}

// Nothing is found
if ($stmt->num_rows == 0) {
	?>
	<div class='nothing-found'>
        <span class="icon">search_off</span>
        Ничего не найдено, измените условия поиска
    </div> <?php

// Single item
} else if ($stmt->num_rows == 1) {
	$stmt->fetch(); ?>

    <div class="item">
        <div class='item-image'>
            <div><img src="<?php echo $img ?>" alt="Pic"></div> <?php
            if ($handle != NULL) { ?>
                <div><img src="<?php echo $handle ?>" alt="Pic"></div> <?php
            } ?>
        </div>
        <div class="item-name">
            <div class='item-name-top'>
                <div class="item-n">
                    <?php printf ($name); ?><?php
                    if ($name_add != NULL) { ?>
                        <span><?php printf ($name_add); ?></span><?php
                    } ?>
                </div> <?php
                if ($add != NULL) { ?>
                    <div class="item-n-add"><?php printf ($add); ?></div><?php
                }
                if ($dia != NULL || $lenth != NULL) { ?> 
                    <div class="item-n-dia-lenth"><?php
                        if ($dia != NULL) { ?>
                            <span>∅</span> <?php printf ($dia); ?><?php
                        }
                        if ($dia != NULL && $lenth != NULL) { ?>, <?php }
                        if ($lenth != NULL) { ?>
                            <span>длинна</span> <?php printf ($lenth); ?><?php
                        } ?> 
                    </div><?php
                }?>
            </div>
            <div class="item-name-bottom">
                <?php printf ($category); ?>
                &emsp;>&emsp;
                <?php printf ($subcategory); ?>
                &emsp;>&emsp;
                <span>арт. </span><?php printf ($id); ?>
            </div>
        </div>
        <div class='s-bttn icon' onClick="shareLink('<?php printf ($name); ?>', '<?php printf ($id); ?>')">ios_share</div>	
    </div>

    <?php

// Multiple items
} else {
	$subcategory_array = array();
	$category_array = array();
	if ($searchresult = $mysqli->query($query)) {
		while ($row = $searchresult->fetch_assoc()) {
			if (!in_array(array($row["category"], $row["subcategory"]), $subcategory_array)) {
				$subcategory_array[] = array($row["category"], $row["subcategory"]);
                if (!in_array(array($row["category"]), $category_array)) {
                    $category_array[] = array($row["category"]);
                }
			} 	
		}
		$searchresult->free();
	} 

    foreach ($category_array as $carr) { ?>
    <div class="category">
        <div class="category-title no-select"><?php printf ($carr[0]); ?></div> <?php

        foreach ($subcategory_array as $sarr) { 
            if ($sarr[0] == $carr[0]) { ?>
            <div class="subcategory">
                <div class="subcategory-title no-select"><?php printf ($sarr[1]); ?></div> <?php

                $stmt->data_seek(0);
                while ($stmt->fetch()) {
                    if ($subcategory == $sarr[1] && $category == $sarr[0]) { ?>
                        <div class="card" onclick="itemPage('<?php printf ($category); ?>','<?php printf ($subcategory); ?>','<?php printf ($id); ?>')">
                            <div class='card-image'>
                                    <img src="<?php echo $img ?>" alt="Pic">
                            </div>
                            <div class="card-name">
                                <div class="card-n">
                                    <div><?php printf ($name); ?></div>
                                    <div></div>
                                </div><?php
                                if ($name_add != NULL) { ?>
                                    <div class="card-n-nadd"><?php printf ($name_add); ?></div><?php
                                }
                                if ($add != NULL) { ?>
                                    <div class="card-n-add"><?php printf ($add); ?></div><?php
                                }
                                if ($dia != NULL || $lenth != NULL) { ?> <div class="card-n-dia-lenth"><?php
                                    if ($dia != NULL) { ?>
                                        <span>∅</span> <?php printf ($dia); ?><?php
                                    }
                                    if ($dia != NULL && $lenth != NULL) { ?>, <?php }
                                    if ($lenth != NULL) { ?>
                                        <span>длинна</span> <?php printf ($lenth); ?><?php
                                    } ?> </div><?php
                                }?> 
                            </div>	
                            <div class="card-id">Арт. <?php printf ($id); ?></div>			
                        </div> <?php
                    }
                } ?>

            </div><?php
            }
        } ?>

    </div><?php
    }
}

$stmt->close();
$mysqli->close();
?>

<script>
    function itemPage(c, s, i) {
        $('main').html("<div class='lds-ellipsis'><div></div><div></div><div></div><div></div></div>");
        $('main').load( 'search.php', { id: i });
        $('.active').removeClass('active');
        history.pushState({ id: i }, "", "/#" + i);
    }

    // Share
    function shareLink(n, i) {      
        navigator.share({
            title: n + ' - арт. ' + i,
            text: n + ' - арт. ' + i,
            url: decodeURI(window.location),
        });
    };
</script>