<?php

require 'vendor/autoload.php';

date_default_timezone_set('America/Los_Angeles');

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

class Database
{

    private $connection;

    function __construct()
    {
        try {
            $host = $_ENV['DB_HOST'];
            $database   = $_ENV['DB_DATABASE'];
            $username = $_ENV['DB_USERNAME'];
            $password = $_ENV['DB_PASSWORD'];
            $this->connection = new PDO('mysql:host=' . $host . ';dbname=' . $database, $username, $password, array(
                PDO::ATTR_PERSISTENT => true
            ));
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo '<strong>PDO MySQL Error: ' . $e->getMessage() . '</strong><br />';
        }
    }
    
    function addRecipe($name, $source, $ingredients, $instructions, $notes, $nutritionalFacts, $servings, $prepTime, $cookTime, $courseId, $tags = '')
    {
        try {
            $statement = $this->connection->prepare(
                'INSERT INTO `recipe`(`name`, `source`, `ingredients`, `instructions`, `notes`, `nutritional_facts`, `servings`, `prep_time`, `cook_time`, `course_id`, `tags`) 
                 VALUES(:name, :source, :ingredients, :instructions, :notes, :nutritionalFacts, :servings, :prepTime, :cookTime, :courseId, :tags)'
            );
            $statement->execute(array(
                ':name' => $name,
                ':source' => $source,
                ':ingredients' => $ingredients,
                ':instructions' => $instructions,
                ':notes' => $notes,
                ':nutritionalFacts' => $nutritionalFacts,
                ':servings' => $servings,
                ':prepTime' => $prepTime,
                ':cookTime' => $cookTime,
                ':courseId' => $courseId,
                ':tags' => $tags,
            ));
            return true;
        } catch (PDOException $e) {
            echo '<strong>PDO MySQL Error:</strong><br /> ' . $e->getMessage() . '<br />';
            return false;
        }
    }

    function getRecipe($id)
    {
        try {
            $statement = $this->connection->prepare(
                'SELECT * FROM recipe 
                 INNER JOIN course ON (recipe.course_id = course.id) 
                 WHERE recipe.id = :id'
            );
            $statement->execute(array(':id' => $id));
            $result = $statement->fetch(PDO::FETCH_NAMED);
            return $result;
        } catch (PDOException $e) {
            echo '<strong>PDO MySQL Error:</strong><br /> ' . $e->getMessage() . '<br />';
        }
    }

    function getAllRecipes()
    {
        try {
            $statement = $this->connection->prepare(
                'SELECT * FROM `recipe` ORDER BY `name` ASC'
            );
            $statement->execute();
            $result = $statement->fetchAll(PDO::FETCH_NAMED);
            return $result;
        } catch (PDOException $e) {
            echo '<strong>PDO MySQL Error:</strong><br /> ' . $e->getMessage() . '<br />';
        }
    }

    function getRecipeByCourse($courseId)
    {
        try {
            $statement = $this->connection->prepare(
                'SELECT * FROM `recipe` WHERE course_id=:course_id'
            );
            $statement->execute(array(':course_id' => $courseId));
            $result = $statement->fetchAll(PDO::FETCH_NAMED);
            return $result;
        } catch (PDOException $e) {
            echo '<strong>PDO MySQL Error:</strong><br /> ' . $e->getMessage() . '<br />';
        }
    }

    function getRecipeCount() {
        try {
            $statement = $this->connection->prepare(
                'SELECT COUNT(*) FROM `recipe`'
            );
            $statement->execute();
            $result = $statement->fetch();
            return $result[0];
        } catch (PDOException $e) {
            echo '<strong>PDO MySQL Error:</strong><br /> ' . $e->getMessage() . '<br />';
        }
    }

}