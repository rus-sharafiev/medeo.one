<?php session_start(); ?>

<?php

$q = $_POST['q'] ?? NULL;

// Category
    include 'login.php';
    $query = "SELECT category FROM catalog WHERE category LIKE '$q%'";
    $cat_array = array();

    if ($searchresult = $mysqli->query($query)) {
        while ($row = $searchresult->fetch_assoc()) {
            if (!in_array(array($row["category"]), $cat_array)) {
                $cat_array[] = array($row["category"]);
            }
        }
        $searchresult->free();
    }
    if ($cat_array != NULL) {
        ?> <div class='ac'> <div class='montserrat no-select'>Категории</div>  <?php
        foreach ($cat_array as list($category)) { ?>    
            <div class='ac-cat'><?php printf ($category); ?></div><?php
        }
        ?> </div> <?php
    }
    $mysqli->close();
    ?>

    <script>
        $('.ac-cat').click( function() {
            var data = $(this).text();
            $('main').load( 'search.php', { cat: data });
            $('.active').removeClass('active');
            history.pushState({ cat: data }, "", "/" + data);
        });
    </script>

    <?php

// Subcategory
    include 'login.php';
    $query = "SELECT subcategory FROM catalog WHERE subcategory LIKE '$q%'";
    $subcat_array = array();

    if ($searchresult = $mysqli->query($query)) {
        while ($row = $searchresult->fetch_assoc()) {
            if (!in_array(array($row["subcategory"]), $subcat_array)) {
                $subcat_array[] = array($row["subcategory"]);
            }
        }
        $searchresult->free();
    }

    if ($subcat_array != NULL) {
        ?> <div class='ac'> <div class='montserrat no-select'>Подкатегории</div> <?php
        foreach ($subcat_array as list($subcategory)) { ?>    
            <div class='ac-subcat'><?php printf ($subcategory); ?></div><?php
        }
        ?> </div> <?php
    }
    $mysqli->close();
    ?>

    <script>
        $('.ac-subcat').click( function() {
            var data = $(this).text();
            $('main').load( 'search.php', { subcat: data });
            $('.active').removeClass('active');
            history.pushState({ subcat: data }, "", "/" + data);
        });
    </script>

    <?php

// ID
    include 'login.php';
    $query = "SELECT id, name, img FROM catalog WHERE id LIKE '$q%' LIMIT 10";

    if ($stmt = $mysqli->prepare($query)) {
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result(
            $id,
            $name,
            $img);
    }

    if ($stmt->num_rows != NULL) {
        ?> <div class='ac'> <div class='montserrat no-select'>Артикулы</div> <?php
        $stmt->data_seek(0);
        while ($stmt->fetch()) { ?>
            <div class="ac-id">
                <div><?php printf ($id); ?></div>
                <img src="<?php echo $img ?>" alt="Pic">
                <div><?php printf ($name); ?></div>
            </div> <?php
        }
    ?> </div> <?php
    }
    $stmt->close();
    $mysqli->close();
    ?>

    <script>
        $('.ac-id').click( function() {
            var data = $(this).children().first().text();
            $('main').load( 'search.php', { id: data });
            $('.active').removeClass('active');
            history.pushState({ id: data }, "", "/#" + data);
        });
    </script>