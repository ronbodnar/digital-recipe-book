<?php

$date = $_POST['date'];
$name = $_POST['name'];
$source = $_POST['source'];
$ingredients = $_POST['ingredients'];
$instructions = $_POST['instructions'];
$notes = $_POST['notes'];
$pictures = $_POST['pictures'];
$servings = $_POST['servings'];
$prepTime = $_POST['prepTime'];
$cookTime = $_POST['cookTime'];
$courseId = $_POST['course'];

if (isset($name, $ingredients, $instructions, $source, $pictures)) {

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL & ~E_NOTICE);

    require 'Database.class.php';

    $database = new Database();

    $ingredients = str_replace(array('%0D%0A%0D%0A'), array('%0D%0A'), $ingredients);
    $ingredientList = explode('%0D%0A', $ingredients);
    $ingredientArray = array();

    foreach ($ingredientList as $ingredient) {
        $split = explode('%20', $ingredient);

        $quantity = urldecode($split[0]);
        $measurement = urldecode($split[1]);
        $ingredient = implode(" ", array_slice($split, 2));

        foreach (array_slice($split, 2) as $i) {
            $i = urldecode($i);
        }

        $array = array(
            'quantity' => $quantity,
            'measurement' => $measurement,
            'ingredient' => $ingredient
        );

        array_push($ingredientArray, $array);
    }

    $ingredientJson = json_encode($ingredientArray, true);

    $instructions = str_replace(array('%0D%0A%0D%0A'), array('%0D%0A'), $instructions);
    $instructionList = explode('%0D%0A', $instructions);
    $instructionArray = array();

    foreach ($instructionList as $instruction) {
        array_push($instructionArray, urldecode($instruction));
    }

    $instructionJson = json_encode($instructionArray, true);

    $notes = str_replace(array('%0D%0A%0D%0A'), array('%0D%0A'), $notes);
    $noteList = explode('%0D%0A', $notes);
    $noteArray = array();

    foreach ($noteList as $note) {
        array_push($noteArray, urldecode($note));
    }

    $noteJson = json_encode($noteArray, true);

    if (isset($_POST['pictures'])) {
        $pictures = json_decode($_POST['pictures'], true);
        $index = 1;
        foreach ($pictures as $key => $value) {
            list($type, $value) = explode(';', $value);
            list(, $value)      = explode(',', $value);

            $data = base64_decode($value);

            $uploads_dir = realpath(dirname(getcwd())) . '/recipe-book/assets/images/recipes/';
            if (!is_dir($uploads_dir)) {
                mkdir($uploads_dir);
            }

            file_put_contents($uploads_dir . ($database->getRecipeCount() + 1) . '.png', $data);
            $index++;
        }
    }

    $nutritionalFacts = array();

    foreach ($_POST as $key => $value) {
        if (strpos($key, 'nf-') === false)
            continue;

        $key = str_replace('nf-', '', $key);
        $nutritionalFacts[$key] = $value;
    }

    $nutritionalFactsJson = json_encode($nutritionalFacts, true);

    $database->addRecipe($name, $source, $ingredientJson, $instructionJson, $noteJson, $nutritionalFactsJson, $servings, $prepTime, $cookTime, $courseId);

    die('ok');
}


require 'header.php';

?>

<div class="container-fluid pt-4">
    <div class="row d-flex justify-content-center">
        <h2 class="text-center fw-bold pt-2 pb-3">Add Recipe <?PHP echo $_POST['source']; ?></h2>
        <div class="col-md-8">
            <div class="card content d-flex">
                <div class="card-body justify-content-center">
                    <form id="addRecipeForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="row g-3">
                        <div class="col-md-4 pt-2">
                            <div class="row">
                                <div class="col-md-12">
                                    <label for="name" class="form-label fw-bold">Name</label>
                                    <input type="text" class="form-control" name="name" id="name">
                                    <div class="invalid-feedback">
                                        You must enter a recipe name
                                    </div>
                                </div>
                                <div class="col-12 pt-3">
                                    <label for="source" class="form-label fw-bold">Source</label>
                                    <input type="text" class="form-control" name="source" id="source">
                                    <div class="invalid-feedback">
                                        You must enter a valid source url
                                    </div>
                                </div>

                                <div class="col-9 pt-3">
                                    <label for="cameraInput" class="form-label pt-1 fw-bold">Image</label>
                                    <input type="file" class="form-control" id="cameraInput" names="pictures[]" accept="image/*">
                                    <div class="invalid-feedback">
                                        You must select at least 1 picture to upload
                                    </div>
                                </div>
                                <div class="col-3 pt-3">
                                    <div id="selectedFiles"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-4 pt-2">
                            <label for="ingredients" class="form-label fw-bold">Ingredients</label>
                            <textarea class="form-control" id="ingredients" name="ingredients" rows="10"></textarea>
                            <div class="invalid-feedback">
                            </div>
                        </div>
                        <div class="col-4 pt-2">
                            <label for="instructions" class="form-label fw-bold">Instructions</label>
                            <textarea class="form-control" id="instructions" name="instructions" rows="10"></textarea>
                            <div class="invalid-feedback">
                            </div>
                        </div>
                        <div class="col-4">
                            <label for="notes" class="form-label fw-bold">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="9"></textarea>
                            <div class="invalid-feedback">
                            </div>
                        </div>
                        <div class="col-8">
                            <div class="row">
                                <div class="col-2">
                                    <label for="nf-calories" class="form-label fw-bold">Calories</label>
                                    <input type="text" class="form-control" name="nf-calories" id="nf-calories">
                                    <div class="invalid-feedback">
                                    </div>
                                </div>
                                <div class="col-2">
                                    <label for="nf-carbohydrates" class="form-label fw-bold">Carbs</label>
                                    <input type="text" class="form-control" name="nf-carbohydrates" id="nf-carbohydrates">
                                    <div class="invalid-feedback">
                                    </div>
                                </div>
                                <div class="col-2">
                                    <label for="nf-fiber" class="form-label fw-bold">Fiber</label>
                                    <input type="text" class="form-control" name="nf-fiber" id="nf-fiber">
                                    <div class="invalid-feedback">
                                    </div>
                                </div>
                                <div class="col-2">
                                    <label for="nf-protein" class="form-label fw-bold">Protein</label>
                                    <input type="text" class="form-control" name="nf-protein" id="nf-protein">
                                    <div class="invalid-feedback">
                                    </div>
                                </div>
                                <div class="col-2">
                                    <label for="nf-sugar" class="form-label fw-bold">Sugar</label>
                                    <input type="text" class="form-control" name="nf-sugar" id="nf-sugar">
                                    <div class="invalid-feedback">
                                    </div>
                                </div>
                                <div class="col-2">
                                    <label for="nf-added-sugar" class="form-label fw-bold">Add Sugar</label>
                                    <input type="text" class="form-control" name="nf-added-sugar" id="nf-added-sugar">
                                    <div class="invalid-feedback">
                                    </div>
                                </div>
                                <div class="col-2 pt-3">
                                    <label for="nf-fat" class="form-label fw-bold">Fat</label>
                                    <input type="text" class="form-control" name="nf-fat" id="nf-fat">
                                    <div class="invalid-feedback">
                                    </div>
                                </div>
                                <div class="col-2 pt-3">
                                    <label for="nf-sat-fat" class="form-label fw-bold">Sat. Fat</label>
                                    <input type="text" class="form-control" name="nf-sat-fat" id="nf-sat-fat">
                                    <div class="invalid-feedback">
                                    </div>
                                </div>
                                <div class="col-2 pt-3">
                                    <label for="nf-trans-fat" class="form-label fw-bold">Trans Fat</label>
                                    <input type="text" class="form-control" name="nf-trans-fat" id="nf-trans-fat">
                                    <div class="invalid-feedback">
                                    </div>
                                </div>
                                <div class="col-2 pt-3">
                                    <label for="nf-sodium" class="form-label fw-bold">Sodium</label>
                                    <input type="text" class="form-control" name="nf-sodium" id="nf-sodium">
                                    <div class="invalid-feedback">
                                    </div>
                                </div>
                                <div class="col-2 pt-3">
                                    <label for="nf-cholesterol" class="form-label fw-bold">Cholesterol</label>
                                    <input type="text" class="form-control" name="nf-cholesterol" id="nf-cholesterol">
                                    <div class="invalid-feedback">
                                    </div>
                                </div>
                                <div class="col-2 pt-3"></div>
                                <div class="col-2 pt-3">
                                    <label for="servings" class="form-label fw-bold">Servings</label>
                                    <input type="text" class="form-control" name="servings" id="servings">
                                    <div class="invalid-feedback">
                                    </div>
                                </div>
                                <div class="col-2 pt-3">
                                    <label for="prepTime" class="form-label fw-bold">Prep Time</label>
                                    <input type="text" class="form-control" name="prepTime" id="prepTime">
                                    <div class="invalid-feedback">
                                    </div>
                                </div>
                                <div class="col-2 pt-3">
                                    <label for="cookTime" class="form-label fw-bold">Cook Time</label>
                                    <input type="text" class="form-control" name="cookTime" id="cookTime">
                                    <div class="invalid-feedback">
                                    </div>
                                </div>
                                <div class="col-2 pt-3"></div>
                                <div class="col-4 pt-3">
                                    <label for="course" class="form-label fw-bold">Course</label>
                                    <select class="form-select" name="course" id="course" aria-label="Default select example">
                                        <option value="" selected>Select an option</option>
                                        <option value="1">Breakfast</option>
                                        <option value="2">Lunch & Dinner</option>
                                        <option value="3">Dessert</option>
                                        <option value="4">Snack</option>
                                        <option value="5">Side Dish</option>
                                    </select>
                                    <div class="invalid-feedback">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 text-center">
                            <div id="response"></div>
                        </div>

                        <div class="col-12 pb-3 text-center">
                            <button type="submit" class="btn btn-mron">Submit Recipe</button>
                            <button type="button" id="clearFormButton" class="btn btn-secondary">Clear Form</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>