<?php

require 'header.php';

$validCourses = array(
    'breakfast',
    'lunch-and-dinner',
    'dessert',
    'snacks',
    'side-dishes'
);

?>

<div class="container-fluid pt-4">
    <h2 class="text-center fw-bold pt-2 pb-4"></h2>

    <div id="recipeContainer row d-flex justify-content-center"></div>

    <div class="row d-flex justify-content-center">
        <?php
        $recipes = $database->getAllRecipes();

        if ($breakfast)
            $recipes = $database->getRecipeByCourse(1);

        if ($lunchDinner)
            $recipes = $database->getRecipeByCourse(2);

        if ($dessert)
            $recipes = $database->getRecipeByCourse(3);

        if ($snack)
            $recipes = $database->getRecipeByCourse(4);

        if ($sideDish)
            $recipes = $database->getRecipeByCourse(5);

        foreach ($recipes as $recipe) {
        ?>
            <div class="col-md-2 px-2">
                <div class="card content d-flex align-items-center">
                    <div class="card-body justify-content-center" style="height: 300px;">
                        <div class="recipe-preview-image text-center pt-2">
                            <a href="recipe?id=<?php echo $recipe['id']; ?>"><img class="img-fluid" src="uploads/<?php echo $recipe['id']; ?>.png" style="object-fit: cover; border-radius: 5%; width: 100%; height: 200px;" width="100%" height="100%" /></a>
                        </div>

                        <div class="recipe-preview-title pt-4 text-center">
                            <a href="recipe?id=<?php echo $recipe['id']; ?>" class="text-mron-blue fw-bold" style="font-size: 1rem;"><?php echo (strlen($recipe['name']) > 47) ? substr($recipe['name'], 0, 44) . '...' : $recipe['name']; ?></a>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<?php include 'footer.php'; ?>