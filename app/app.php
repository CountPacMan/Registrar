<?php
  require_once __DIR__."/../vendor/autoload.php";
  require_once __DIR__."/../src/Task.php";
  require_once __DIR__."/../src/Category.php";

  $app = new Silex\Application();

  $app['debug'] = true;

  $DB = new PDO('pgsql:host=localhost;dbname=to_do;user=postgres;password=password');

  $app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views'
  ));

  use Symfony\Component\HttpFoundation\Request;
  Request::enableHttpMethodParameterOverride();

  // get

  $app->get("/", function() use ($app) {
    return $app['twig']->render('index.html.twig', array('added' => false, 'categories' => Category::getAll()));
  });

  $app->get("/categories/{id}", function($id) use ($app) {
    $category = Category::find($id);
    return $app['twig']->render('category.html.twig', array('category' => $category, 'tasks' => $category->getTasks()));
  });

  $app->get("/categories/{id}/edit", function($id) use ($app) {
    $category = Category::find($id);
    return $app['twig']->render('category_edit.html.twig', array('category' => $category));
  });

  $app->get("/tasks", function() use ($app) {
    $results = Task::getAll();
    $results_categories = [];
    foreach ($results as $result) {
      $categories = $result->getCategories();
      array_push($results_categories, $categories);
    }
    return $app['twig']->render('tasks.html.twig', array('results_categories' => $results_categories, 'results' => $results));
  });

  // post

  $app->post("/categories", function() use ($app) {
    $category = new Category($_POST['name']);
    $category->save();
    return $app['twig']->render('index.html.twig', array('added' => false, 'categories' => Category::getAll()));
  });

  $app->post("/tasks", function() use ($app) {
    $task = new Task($_POST['description'], null, $_POST['due_date']);
    $task->save();
    for ($i = 0; $i < count($_POST['category_id']); $i++) {
      $category = Category::find($_POST['category_id'][$i]);
      $category->addTask($task);
    }
    return $app['twig']->render('index.html.twig', array('added' => true, 'categories' => Category::getAll()));
  });

  $app->post("/search", function() use ($app) {
    $results = Category::search($_POST['name']);
    $results_categories = [];
    foreach ($results as $result) {
      $categories = $result->getCategories();
      array_push($results_categories, $categories);
    }
    return $app['twig']->render('search_results.html.twig', array('results' => $results, 'results_categories' => $results_categories, 'search_term' => $_POST['name']));
  });

  $app->post("/deleteTasks", function() use ($app) {
    Task::deleteAll();
    return $app['twig']->render('index.html.twig', array('added' => false));
  });

  $app->post("/deleteCategories", function() use ($app) {
    Category::deleteAll();
    return $app['twig']->render('index.html.twig', array('added' => false));
  });

  // patch

  $app->patch("/categories/{id}", function($id) use ($app) {
    $name = $_POST['name'];
    $category = Category::find($id);
    $category->update($name);
    return $app['twig']->render('category.html.twig', array('category' => $category, 'tasks' => $category->getTasks()));
  });

  // delete

  $app->delete("/categories/{id}", function($id) use ($app) {
    $category = Category::find($id);
    $category->delete();
    return $app['twig']->render('index.html.twig', array('added' => false, 'categories' => Category::getAll()));
  });

  return $app;
?>
