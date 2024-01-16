<?php

require 'header.php';

$id = $_GET['id'];

$recipe = $database->getRecipe($id);

$nutritionalFacts = json_decode($recipe['nutritional_facts'], true);

function calculate_daily_value($nutrient)
{
    global $nutritionalFacts;
    
    $dailyValues = array(
        'fat' => 78,
        'sat-fat' => 20,
        'cholesterol' => 300,
        'sodium' => 2300,
        'carbohydrates' => 275,
        'fiber' => 28,
        'added-sugar' => 50,
        'protein' => 50
    );

    $value = $nutritionalFacts[$nutrient];
    $dailyValue = $dailyValues[$nutrient];

    if (empty($value) || strlen($value) <= 0) {
        $value = 0;
    }

    if (empty($dailyValue) || strlen($dailyValue) <= 0) {
        $dailyValue = 0;
    }

    return number_Format(($value / $dailyValue) * 100, 0);
}

$recipeImages = findimages($id);

?>

<div class="main-container container-fluid pt-4">

    <div class="recipe-container row d-flex justify-content-center">
        <!--<div class="col-sm-10">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/projects/recipe-book/" class="text-mron-green">All Recipes</a></li>
                    <li class="breadcrumb-item"><a href=".?<?php //echo str_replace(array(' & ', ' '), array('-and-', '-'), $recipe['name'][1]); ?>" class="text-mron-green"><?php //echo ucwords($recipe['name'][1]); ?></a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?php //echo $recipe['name'][0]; ?></li>
                </ol>
            </nav>
        </div>-->


        <div class="col-sm-10 recipe-name-desktop">
            <h2 class="fw-bold"><?php echo $recipe['name'][0]; ?></h2> <span class="small">(<a href="<?php echo $recipe['source']; ?>" target="_blank" class="text-mron-green">Original Recipe</a>)</span>
        </div>

        <div class="col-sm-10">
            <div class="card content d-flex">
                <div class="card-body justify-content-center">

                    <div class="row d-flex justify-content-between">
                        <div class="col-sm-4 image-container">
                            <?php if (count($recipeImages) > 1) { ?>
                                <div id="carouselExampleControls" class="carousel slide pt-3" data-bs-ride="carousel">
                                    <div style="border-radius: 10%;" class="carousel-inner">
                                        <?php foreach ($recipeImages as $image) { ?>
                                            <div class="carousel-item<?php echo (strcmp($image, $id . '.png') === 0 ? ' active' : ''); ?>">
                                                <img class="recipe-image img-fluid" alt="Alt image" src="assets/images/recipes/<?php echo $image; ?>" width="100%" height="100%" />
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Previous</span>
                                    </button>
                                    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Next</span>
                                    </button>
                                </div>
                            <?php } else { ?>
                                <img class="recipe-image img-fluid" alt="Alt image" src="assets/images/recipes/<?php echo $id; ?>.png" width="100%" height="100%" />
                            <?php } ?>
                        </div>

                        <div class="col-sm-4 information-container">
                            <div class="row text-center">
                                <div class="col-4"><span class="circular"><?php echo $recipe['servings'] == 0 ? "N/A" : $recipe['servings']; ?></span></div>
                                <div class="col-4"><span class="circular"><?php echo $recipe['prep_time']; ?></span></div>
                                <div class="col-4"><span class="circular"><?php echo $recipe['cook_time']; ?></span></div>
                                <div class="col-4 fw-bold pt-2">Servings</div>
                                <div class="col-4 fw-bold pt-2">Prep Time</div>
                                <div class="col-4 fw-bold pt-2">Cook Time</div>
                                <div class="col-12 pt-5 pb-4">
                                    <span class="fw-bold fs-4 text-mron-blue" style="display: none;">Key Macros</span>
                                </div>
                                <div class="col-3"><span class="circular-blue"><?php echo $nutritionalFacts['protein'] > 0 ? round($nutritionalFacts['protein'], 0) . 'g' : 'ND'; ?></span></div>
                                <div class="col-3"><span class="circular-blue"><?php echo $nutritionalFacts['fat'] > 0 ? round($nutritionalFacts['fat'], 0) . 'g' : 'ND'; ?></span></div>
                                <div class="col-3"><span class="circular-blue"><?php echo $nutritionalFacts['carbohydrates'] > 0 ? round($nutritionalFacts['carbohydrates'], 0) . 'g' : 'ND'; ?></span></div>
                                <div class="col-3"><span class="circular-blue"><?php echo $nutritionalFacts['carbohydrates'] > 0 ? round(($nutritionalFacts['carbohydrates'] - $nutritionalFacts['fiber']), 0) . 'g' : 'ND'; ?></span></div>
                                <div class="col-3 fw-bold pt-2">Protein</div>
                                <div class="col-3 fw-bold pt-2">Fat</div>
                                <div class="col-3 fw-bold pt-2">Carbs</div>
                                <div class="col-3 fw-bold pt-2">Net Carbs</div>
                            </div>
                        </div>

                        <div class="col-sm-4 nutrition-container">
                            <span class="fw-bold text-center" style="font-size: 1.5em;">Nutrition Facts</span>
                            <span class="text-center" style="font-weight: normal; font-size: 0.75em;">
                                <em>(per serving)</em>
                            </span>

                            <div class="row d-flex justify-content-center align-items-center pt-1" style="font-size: 0.6em;">
                                <div class="col-6 border-bottom border-top border-left border-start">
                                    <strong style="font-size: 2em;">Calories</strong>
                                </div>
                                <div class="col-6 border-bottom border-top border-left border-end text-end">
                                    <span style="font-size: 2em;"><?php echo (strlen($nutritionalFacts['calories']) > 0 ? $nutritionalFacts['calories'] : 'No Data'); ?></span>
                                </div>
                                <div class="col-12 text-end border-bottom border-start border-end">
                                    <strong>% daily value</strong>
                                </div>
                                <div class="col-8 border-bottom border-start">
                                    <strong>Total Fat</strong> <?php echo ($nutritionalFacts['fat'] > 0 ? $nutritionalFacts['fat'] . 'g' : '<em class="small">No data</em>'); ?>
                                </div>
                                <div class="col-4 border-bottom border-end text-end">
                                    <?php echo calculate_daily_value('fat'); ?>%
                                </div>
                                <div class="col-8 border-bottom border-start">
                                    &emsp;Saturated Fat <?php echo ($nutritionalFacts['sat-fat'] > 0 ? $nutritionalFacts['sat-fat'] . 'g' : '<em class="small">No data</em>'); ?>
                                </div>
                                <div class="col-4 border-bottom border-end text-end">
                                    <?php echo calculate_daily_value('sat-fat'); ?>%
                                </div>
                                <div class="col-12 border-bottom border-start border-end">
                                    &emsp;<em>Trans</em> Fat <?php echo $nutritionalFacts['trans-fat'] > 0 ? $nutritionalFacts['trans-fat'] . 'g' : '<em class="small">No data</em>'; ?>
                                </div>
                                <div class="col-8 border-bottom border-start">
                                    <strong>Cholesterol</strong> <?php echo $nutritionalFacts['cholesterol'] > 0 ? $nutritionalFacts['cholesterol'] . 'mg' : '<em class="small">No data</em>'; ?>
                                </div>
                                <div class="col-4 border-bottom border-end text-end"><?php echo calculate_daily_value('cholesterol'); ?>%</div>
                                <div class="col-8 border-bottom border-start">
                                    <strong>Sodium</strong> <?php echo $nutritionalFacts['sodium'] > 0 ? $nutritionalFacts['sodium'] . 'mg' : '<em class="small">No data</em>'; ?>
                                </div>
                                <div class="col-4 border-bottom border-end text-end"><?php echo calculate_daily_value('sodium'); ?>%</div>
                                <div class="col-8 border-bottom border-start">
                                    <strong>Total Carbohydrate</strong> <?php echo $nutritionalFacts['carbohydrates'] > 0 ? $nutritionalFacts['carbohydrates'] . 'g' : '<em class="small">No data</em>'; ?>
                                </div>
                                <div class="col-4 border-bottom border-end text-end"><?php echo calculate_daily_value('carbohydrates'); ?>%</div>
                                <div class="col-8 border-bottom border-start">
                                    &emsp;Dietary Fiber <?php echo $nutritionalFacts['fiber'] > 0 ? $nutritionalFacts['fiber'] . 'g' : '<em class="small">No data</em>'; ?>
                                </div>
                                <div class="col-4 border-bottom border-end text-end"><?php echo calculate_daily_value('fiber'); ?>%</div>
                                <div class="col-12 border-bottom border-start border-end">
                                    &emsp;Total Sugars <?php echo $nutritionalFacts['sugar'] > 0 ? $nutritionalFacts['sugar'] . 'g' : '<em class="small">No data</em>'; ?>
                                </div>
                                <div class="col-12 border-bottom border-start border-end">
                                    &emsp;&emsp;Includes <?php echo $nutritionalFacts['added-sugar'] > 0 ? $nutritionalFacts['added-sugar'] . 'g' : '0g'; ?> Added Sugars
                                </div>
                                <div class="col-8 border-bottom border-start">
                                    <strong>Protein</strong> <?php echo $nutritionalFacts['protein'] > 0 ? $nutritionalFacts['protein'] . 'g' : '<em class="small">No data</em>'; ?>
                                </div>
                                <div class="col-4 text-end border-bottom border-end"><?php echo calculate_daily_value('protein'); ?>%</div>
                                <div class="col-12 text-center pt-2">
                                    <em class="small">
                                        * The % Daily Value (DV) tells you how much a nutrient in a serving of food contributes to
                                        a daily diet. 2,000 calories a day is used for general nutrition advice. This nutrition
                                        information has not been verified and may be inaccurate.
                                    </em>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 recipe-name">
                            <h2 class="fw-bold"><?php echo $recipe['name'][0]; ?></h2> <span class="small">(<a href="<?php echo $recipe['source']; ?>" target="_blank" class="text-mron-green">Original Recipe</a>)</span>
                        </div>
                    </div>

                    <div class="row d-flex justify-content-center pt-5">
                        <div class="col-sm-4 border-top pt-3 pb-2">
                            <h5 class="text-mron-blue text-center fw-bold">Ingredients</h5>
                            <ul style="list-style-type: none;">
                                <?php
                                $ingredients = json_decode($recipe['ingredients'], true);

                                foreach ($ingredients as $ingredient) {
                                    $ingredient = str_replace(
                                        array(
                                            "tsp", "tbsp", "%2C", "%2F", "%3B", "%3A", "%26"
                                        ),
                                        array(
                                            "teaspoon", "tablespoon", ",", "/", ";", ":", "&"
                                        ),
                                        $ingredient
                                    );
                                    if (str_starts_with($ingredient['quantity'], '##')) {
                                        echo '<br /><li class="pb-1 fw-bold">' . strtoupper(str_replace('##', '', $ingredient['quantity'])) . ' ' . strtoupper($ingredient['measurement']) . ' ' . strtoupper($ingredient['ingredient']) . '</li>';
                                    } else {
                                        echo '<li class="pb-1"><input class="form-check-input custom-checkbox" type="checkbox" value="" id="flexCheckDefault">&nbsp;&nbsp;' . $ingredient['quantity'] . ' ' . $ingredient['measurement'] . ' ' . $ingredient['ingredient'] . '</li>';
                                    }
                                }
                                ?>
                            </ul>
                        </div>

                        <div class="col-sm-4 text-center border-top pt-3 pb-2">
                            <h5 class="text-mron-blue fw-bold">Instructions</h5>
                            <div class="row d-flex justify-content-center">
                                <?php
                                $instructions = json_decode($recipe['instructions'], true);

                                $step = 1;
                                foreach ($instructions as $instruction) {
                                    $instruction = str_replace("%2C", ",", $instruction);
                                    if (str_starts_with($instruction, '##')) {
                                        echo '<div class="col-sm-12 pb-3 text-start">';
                                        echo '<br /><strong>' . strtoupper(str_replace('##', '', $instruction)) . '</strong>';
                                        echo '</div>';
                                        $step = 0;
                                    } else {
                                        echo '<div class="col-sm-2 pb-3">';
                                        echo '<span class="step-number">' . $step . '</span>';
                                        echo '</div>';
                                        echo '<div class="col-sm-10 pb-3 text-start">';
                                        echo $instruction;
                                        echo '</div>';
                                    }
                                    $step++;
                                }

                                ?>
                            </div>
                        </div>

                        <?php if (strlen($recipe['notes']) > 10) { ?>
                            <div class="col-sm-4 text-center border-top pt-3">
                                <h5 class="text-mron-blue fw-bold">Notes</h5>
                                <div class="row d-flex justify-content-center">
                                    <?php
                                    $notes = json_decode($recipe['notes'], true);

                                    foreach ($notes as $note) {
                                        $note = str_replace("%2C", ",", $note);
                                        echo '<div class="col-sm-10 pb-3 text-start">';
                                        echo $note;
                                        echo '</div>';
                                    }

                                    ?>
                                </div>
                            </div>
                        <?php } ?>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>