<?php
class Course {
  private $name;
  private $id;
  private $course_number;

  function __construct($name, $course_number, $id = null) {
    $this->name = $name;
    $this->course_number = $course_number;
    $this->id = $id;
  }

  // setters
  function setName ($name) {
    $this->name = (string) $name;
  }

  function setId($id) {
    $this->id = (int) $id;
  }

  function setCourseNumber($course_number) {
    $this->course_number = (string) $course_number;
  }

  // getters
  function getName() {
    return $this->name;
  }

  function getId() {
    return $this->id;
  }

  function getCourseNumber() {
    return $this->course_number;
  }

  // dB

  function save() {
    $statement = $GLOBALS['DB']->query("INSERT INTO courses (name, course_number) VALUES ('{$this->getName()}', '{$this->getNumber()}') RETURNING id;");
    $result = $statement->fetch(PDO::FETCH_ASSOC);
    $this->setId($result['id']);
  }

  function updateName($new_name) {
    $GLOBALS['DB']->exec("UPDATE courses SET name = '{$new_name}' WHERE id = {$this->getId()}");
    $this->setName($new_name);
  }

  function updateCourseNumber($course_number) {
    $GLOBALS['DB']->exec("UPDATE courses SET course_number = '{$course_number}' WHERE id = {$this->getId()}");
    $this->setCourseNumber($course_number);
  }

  function delete() {
    $GLOBALS['DB']->exec("DELETE FROM courses WHERE id = {$this->getId()};");
    $GLOBALS['DB']->exec("DELETE FROM categories_tasks WHERE category_id = {$this->getId()};");
  }

  function addTask($task) {
    $GLOBALS['DB']->exec("INSERT INTO categories_tasks (category_id, task_id) VALUES ({$this->getId()}, {$task->getId()});");
  }

  function getTasks() {
    $query = $GLOBALS['DB']->query("SELECT task_id FROM categories_tasks WHERE category_id = {$this->getId()};");
    $task_ids = $query->fetchAll(PDO::FETCH_ASSOC);

    $tasks = [];
    foreach ($task_ids as $id) {
      $task_id = $id['task_id'];
      $result = $GLOBALS['DB']->query("SELECT * FROM tasks WHERE id = {$task_id};");
      $returned_task = $result->fetchAll(PDO::FETCH_ASSOC);

      $description = $returned_task[0]['description'];
      $id = $returned_task[0]['id'];
      $due_date = $returned_task[0]['due_date'];
      $due_date = str_replace("-", "/", $due_date);
      $new_task = new Task($description, $id, $due_date);
      array_push($tasks, $new_task);
    }
    return $tasks;
  }

  static function getAll() {
    $returned_categories = $GLOBALS['DB']->query("SELECT * FROM courses;");
    $courses = [];
    foreach ($returned_categories as $category) {
      $name = $category['name'];
      $id = $category['id'];
      $new_category = new Course($name, $id);
      array_push($courses, $new_category);
    }
    return $courses;
  }

  static function deleteAll() {
    $GLOBALS['DB']->exec("DELETE FROM courses *;");
  }

  static function find($search_id) {
    $found_category = null;
    $courses = Course::getAll();
    foreach($courses as $category) {
      $category_id = $category->getId();
      if($category_id == $search_id) {
        $found_category = $category;
      }
    }
    return $found_category;
  }

  static function search($description) {
    $tasks = [];
    $returned_tasks = $GLOBALS['DB']->query("SELECT * FROM tasks WHERE description = '{$description}';");
    foreach ($returned_tasks as $task) {
      $due_date = $task['due_date'];
      $due_date = str_replace("-", "/", $due_date);
      $new_Task = new Task($task['description'], $task['id'], $due_date);
      array_push($tasks, $new_Task);
    }
    return $tasks;
  }
}
