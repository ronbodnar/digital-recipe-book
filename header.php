<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL & ~E_NOTICE);

require 'functions.php';
require 'Database.class.php';

$database = new Database();

$id = $_GET['id'];

$recipe = $database->getRecipe($id);

$breakfast = strpos($_SERVER['REQUEST_URI'], "?breakfast") !== false;
$lunchDinner = strpos($_SERVER['REQUEST_URI'], "?lunch-and-dinner") !== false;
$snack = strpos($_SERVER['REQUEST_URI'], "?snack") !== false;
$sideDish = strpos($_SERVER['REQUEST_URI'], "?side-dish") !== false;
$dessert = strpos($_SERVER['REQUEST_URI'], "?dessert") !== false;

function getPrefix()
{
    global $breakfast, $lunchDinner, $snack, $sideDish, $dessert;
    $prefix = '';
    if ($breakfast)
        $prefix = 'Breakfast';

    if ($lunchDinner)
        $prefix = 'Lunch & Dinner';

    if ($snack)
        $prefix = 'Snacks';

    if ($sideDish)
        $prefix = 'Side Dishes';

    if ($dessert)
        $prefix = 'Dessert';

    return $prefix;
}

$pageTitle = getPrefix();

?>

<!doctype html>
<html lang="en" class="h-100">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="This application is used to store recipes and order them in an easy-to-find and easy-to-read arrangement.">
    <meta name="author" content="Ron Bodnar">

    <meta property=’og:title’ content='Recipe Book' />
    <meta property=’og:image’ content=<?php echo $recipe == null ? 'https://ronbodnar.com/projects/recipe-book/uploads/' . $recipe['id'] . '.png' : '/assets/images/favicon.png'; ?> />
    <meta property=’og:description’ content=' ' />
    <meta property=’og:url’ content='https://ronbodnar.com/projects/recipe-book/' />
    <meta property='og:image:width' content='1200' />
    <meta property='og:image:height' content='627' />

    <title><?php echo ($recipe == null ? (strlen(getPrefix()) > 0 ? getPrefix() . ' | ' : '') : $recipe['name'][0] . ' | '); ?>Recipe Book</title>

    <link rel="canonical" href="https://ronbodnar.com/projects/recipe-book/">

    <script>
        // Render blocking
        if (localStorage.theme) document.documentElement.setAttribute("data-theme", localStorage.theme);
    </script>

    <link href="/assets/css/bootstrap.min.css?v=<?php echo filemtime('/assets/css/bootstrap.min.css'); ?>" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="<?php echo getRelativePath(); ?>assets/css/style.css?v=<?php echo filemtime(getRelativePath() . 'assets/css/style.css'); ?>" rel="stylesheet">

    <link rel="icon" href="/assets/images/favicon.png?v=<?php echo filemtime('/assets/images/favicon.png'); ?>">

    <meta name="theme-color" content="#7952b3">
</head>

<body class="body">
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="/"><img src="/assets/images/header-lg.png" width="209" height="58" alt="Logo" /></a>

            <button style="background: transparent; border: none;" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSearch" aria-controls="navbarSearch" aria-expanded="false" aria-label="Toggle search">
                <i class="navbar-toggler bi bi-search" style="color: var(--light-text-color); font-size: 1.5rem; padding: 4px; margin: 0; margin-left: -4vw;"></i>
            </button>

            <button style="padding: 4px; margin: 0;" class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <i class="bi bi-list" style="color: var(--light-text-color); font-size: 2rem;"></i>
            </button>

            <div class="collapse navbar-collapse justify-content-end px-1" id="navbarNav">
                <ul class="navbar-nav">

                    <li class="nav-item nav-search-field">
                        <div class="navbarSearchDesktop justify-content-end px-1" style="display: none;">
                            <form action="/projects/recipe-book/search" method="GET">
                                <input id="navSearchFieldDesktop" class="form-control" type="search" name="query" placeholder="Search" aria-label="Search">
                            </form>
                        </div>
                    </li>

                    <li class="nav-item navSearchToggle align-self-center">
                        <button id="navSearchButton" style="background: transparent; border: none;" type="button">
                            <i class="bi bi-search" style="color: var(--light-text-color); font-size: 1.2rem; padding: 10px;"></i>
                        </button>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link<?php echo (strlen(getPrefix()) > 0 ? '' : ' active'); ?>" aria-current="page" href="/projects/recipe-book/">All Recipes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?php echo $breakfast ? ' active' : ''; ?>" href=".?breakfast">Breakfast</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?php echo $lunchDinner ? ' active' : ''; ?>" href=".?lunch-and-dinner">Lunch &amp; Dinner</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?php echo $sideDish ? ' active' : ''; ?>" href=".?side-dish">Side Dish</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?php echo $dessert ? ' active' : ''; ?>" href=".?dessert">Dessert</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?php echo $snack ? ' active' : ''; ?>" href=".?snack">Snack</a>
                    </li>
                    <li class="nav-item py-1">
                        <i class="bi bi-moon-stars text-mron-green px-2" id="darkModeSwitch" style="cursor: pointer; font-size: 1.3rem;" onclick="toggleDarkMode()"></i>
                    </li>
                </ul>
            </div>

            <div class="collapse navbar-collapse" id="navbarSearch">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <form class="d-flex" action="/projects/recipe-book/search" method="GET">
                            <input class="form-control me-2" type="search" name="query" placeholder="Search" aria-label="Search">
                            <button class="btn btn-outline-success" type="submit">Search</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>