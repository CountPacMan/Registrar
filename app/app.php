<?php
  require_once __DIR__."/../vendor/autoload.php";
  require_once __DIR__."/../src/Student.php";
  require_once __DIR__."/../src/Course.php";

  $app = new Silex\Application();

  $app['debug'] = true;

  $DB = new PDO('pgsql:host=localhost;dbname=registrar');

  $app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views'
  ));

  use Symfony\Component\HttpFoundation\Request;
  Request::enableHttpMethodParameterOverride();

  // get

  $app->get("/", function() use ($app) {
    return $app['twig']->render('index.html.twig', array('added' => false, 'courses' => Course::getAll()));
  });

  $app->get("/courses/{id}", function($id) use ($app) {
    $course = Course::find($id);
    return $app['twig']->render('courses.html.twig', array('course' => $course, 'students' => $course->getStudents()));
  });

  $app->get("/courses/{id}/edit", function($id) use ($app) {
    $course = Course::find($id);
    return $app['twig']->render('courses_edit.html.twig', array('course' => $course));
  });

  $app->get("/students", function() use ($app) {
    $results = Student::getAll();
    $results_courses = [];
    foreach ($results as $result) {
      $courses = $result->getCourses();
      array_push($results_courses, $courses);
    }
    return $app['twig']->render('students.html.twig', array('results_courses' => $results_courses, 'results' => $results));
  });

  $app->get("/students/{id}/edit", function($id) use ($app) {
    $student = Student::find($id);
    $courses = $student->getCourses();
    $other_courses = $student->getOtherCourses();
    return $app['twig']->render('students_edit.html.twig', array('student' => $student, 'courses' => $courses, 'other_courses' => $other_courses));
  });

  // post

  $app->post("/courses", function() use ($app) {
    $course = new Course($_POST['name'], $_POST['course_number']);
    $course->save();
    return $app['twig']->render('index.html.twig', array('added' => false, 'courses' => Course::getAll()));
  });

  $app->post("/students", function() use ($app) {
    $student = new Student($_POST['name'], $_POST['enrollment_date']);
    $student->save();
    for ($i = 0; $i < count($_POST['course_id']); $i++) {
      $course = Course::find($_POST['course_id'][$i]);
      $course->addStudent($student);
    }
    return $app['twig']->render('index.html.twig', array('added' => true, 'courses' => Course::getAll()));
  });

  $app->post("/search", function() use ($app) {
    $results = Course::search($_POST['name']);
    $results_courses = [];
    foreach ($results as $result) {
      $courses = $result->getCourses();
      array_push($results_courses, $courses);
    }
    return $app['twig']->render('search_results.html.twig', array('results' => $results, 'results_courses' => $results_courses, 'search_term' => $_POST['name']));
  });

  $app->post("/deleteStudents", function() use ($app) {
    Student::deleteAll();
    return $app['twig']->render('index.html.twig', array('added' => false));
  });

  $app->post("/deleteCourses", function() use ($app) {
    Course::deleteAll();
    return $app['twig']->render('index.html.twig', array('added' => false));
  });

  // patch

  $app->patch("/courses/{id}", function($id) use ($app) {
    $name = $_POST['name'];
    $course = Course::find($id);
    $course->updateName($name);
    return $app['twig']->render('courses.html.twig', array('course' => $course, 'students' => $course->getStudents()));
  });

  $app->patch("/students/{id}", function($id) use ($app) {
    $name = $_POST['name'];
    $student = Student::find($id);
    $student->updateName($name);
    $student = Student::find($id);
    for ($i = 0; $i < count($_POST['course_id']); $i++) {
      $course = Course::find($_POST['course_id'][$i]);
      $course->addStudent($student);
    }
    $courses = $student->getCourses();
    $other_courses = $student->getOtherCourses();
    return $app['twig']->render('students_edit.html.twig', array('student' => $student, 'courses' => $courses, 'other_courses' => $other_courses));
  });

  // delete

  $app->delete("/destroy", function() use ($app) {
    Course::deleteAll();
    Student::deleteAll();
    return $app['twig']->render('index.html.twig', array('added' => false, 'courses' => Course::getAll()));
  });

  $app->delete("/courses/{id}", function($id) use ($app) {
    $course = Course::find($id);
    $course->delete();
    return $app['twig']->render('index.html.twig', array('added' => false, 'courses' => Course::getAll()));
  });

  $app->delete("/student/{id}", function($id) use ($app) {
    $student = Student::find($id);
    $student->delete();
    $course = Course::find($_POST['course_id']);
    return $app['twig']->render('courses.html.twig', array('course' => $course, 'students' => $course->getStudents()));
  });

  $app->delete("/students/{id}", function($id) use ($app) {
    $student = Student::find($id);
    $student->delete();
    return $app['twig']->render('index.html.twig', array('added' => false, 'courses' => Course::getAll()));
  });

  return $app;
?>
