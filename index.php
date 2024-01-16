<?php

require 'header.php';

?>

<div class="container-fluid pt-4">
    <h2 class="text-center fw-bold pt-2 pb-4"><?php echo 'Recipe Book' . (strlen($pageTitle) > 0 ? ' - ' . $pageTitle : ''); ?></h2>

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
            $recipeImages = findimages($recipe['id']);
        ?>
            <div class="col-md-3 px-4 py-4">
                <div class="card content d-flex align-items-center">
                    <div class="card-body justify-content-center">
                        <div class="recipe-preview-image text-center pt-2">
                            <?php if (count($recipeImages) > 1) { ?>
                                <div id="carouselExampleControls" class="carousel slide p-1" data-bs-ride="carousel">
                                    <div class="carousel-inner">
                                        <?php foreach ($recipeImages as $image) { ?>
                                            <div class="carousel-item<?php echo (strcmp($image, $recipe['id'] . '.png') === 0 ? ' active' : ''); ?>">
                                                <a href="recipe?id=<?php echo $recipe['id']; ?>"><img class="img-fluid" alt="Alt image" src="assets/images/recipes/<?php echo $image; ?>" style="object-fit: cover; border-radius: 5%; width: 300px; height: 225px;" width="100%" height="100%" /></a>
                                            </div>
                                        <?php } ?> 
                                    </div>
                                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"><i class="bi bi-chevron-left"></i></span>
                                        <span class="visually-hidden">Previous</span>
                                    </button>
                                    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"><i class="bi bi-chevron-right"></i></span>
                                        <span class="visually-hidden">Next</span>
                                    </button>
                                </div>
                            <?php } else { ?>
                                <a href="recipe?id=<?php echo $recipe['id']; ?>"><img class="img-fluid" src="assets/images/recipes/<?php echo $recipe['id']; ?>.png" style="object-fit: cover; border-radius: 5%; width: 300px; height: 225px;" width="100%" height="100%" /></a>
                            <?php } ?>
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