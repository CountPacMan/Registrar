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
    $statement = $GLOBALS['DB']->query("INSERT INTO courses (name, course_number) VALUES ('{$this->getName()}', '{$this->getCourseNumber()}') RETURNING id;");
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
    $GLOBALS['DB']->exec("DELETE FROM students_courses WHERE course_id = {$this->getId()};");
  }

  function addStudent($student) {
    $GLOBALS['DB']->exec("INSERT INTO students_courses (course_id, student_id) VALUES ({$this->getId()}, {$student->getId()});");
  }

  function getStudents() {
    $query = $GLOBALS['DB']->query("SELECT student_id FROM students_courses WHERE course_id = {$this->getId()};");
    $student_ids = $query->fetchAll(PDO::FETCH_ASSOC);

    $students = [];
    foreach ($student_ids as $id) {
      $student_id = $id['student_id'];
      $result = $GLOBALS['DB']->query("SELECT * FROM students WHERE id = {$student_id};");
      $returned_student = $result->fetchAll(PDO::FETCH_ASSOC);

      $name = $returned_student[0]['name'];
      $id = $returned_student[0]['id'];
      $enrollment_date = $returned_student[0]['enrollment_date'];
      $enrollment_date = str_replace("-", "/", $enrollment_date);
      $new_student = new Student($name, $enrollment_date, $id);
      array_push($students, $new_student);
    }
    return $students;
  }

  static function getAll() {
    $returned_courses = $GLOBALS['DB']->query("SELECT * FROM courses;");
    $courses = [];
    foreach ($returned_courses as $course) {
      $name = $course['name'];
      $course_number = $course['course_number'];
      $id = $course['id'];
      $new_course = new Course($name, $course_number, $id);
      array_push($courses, $new_course);
    }
    return $courses;
  }

  static function deleteAll() {
    $GLOBALS['DB']->exec("DELETE FROM courses *;");
    $GLOBALS['DB']->exec("DELETE FROM students_courses *;");
  }

  static function find($search_id) {
    $found_course = null;
    $courses = Course::getAll();
    foreach($courses as $course) {
      $course_id = $course->getId();
      if($course_id == $search_id) {
        $found_course = $course;
      }
    }
    return $found_course;
  }

  static function search($name) {
    $students = [];
    $returned_students = $GLOBALS['DB']->query("SELECT * FROM students WHERE name = '{$name}';");
    foreach ($returned_students as $student) {
      $enrollment_date = $student['enrollment_date'];
      $enrollment_date = str_replace("-", "/", $enrollment_date);
      $new_Student = new Student($student['name'], $enrollment_date, $student['id']);
      array_push($students, $new_Student);
    }
    return $students;
  }
}
