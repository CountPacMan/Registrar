<?php
  class Student {
    private $name;
    private $enrollment_date;
    private $id;

    function __construct($name, $enrollment_date, $id = null)   {
      $this->name = $name;
      $this->enrollment_date = $enrollment_date;
      $this->id = $id;
    }

    // getters
    function getName()  {
      return $this->name;
    }

    function getId() {
      return $this->id;
    }

    function getEnrollmentDate() {
      return $this->enrollment_date;
    }

    // setters
    function setName($name)  {
      $this->name = (string) $name;
    }

    function setId($id) {
      $this->id = (int) $id;
    }

    function setEnrollmentDate($enrollment_date) {
      $this->enrollment_date = (string) $enrollment_date;
    }

    // DB

    function save() {
      $statement = $GLOBALS['DB']->query("INSERT INTO students (name, enrollment_date) VALUES ('{$this->getName()}', '{$this->getEnrollmentDate()}') RETURNING id;");
      $result = $statement->fetch(PDO::FETCH_ASSOC);
      $this->setId($result['id']);
    }

    function addCourse($course) {
      $GLOBALS['DB']->exec("INSERT INTO students_courses (course_id, student_id) VALUES ({$course->getId()}, {$this->getId()});");
    }

    function getCourses() {
      $query = $GLOBALS['DB']->query("SELECT course_id FROM students_courses WHERE student_id = {$this->getId()};");
      $course_ids = $query->fetchAll(PDO::FETCH_ASSOC);

      $courses = [];
      foreach ($course_ids as $id) {
        $course_id = $id['course_id'];
        $result = $GLOBALS['DB']->query("SELECT * FROM courses WHERE id = {$course_id};");
        $returned_course = $result->fetchAll(PDO::FETCH_ASSOC);

        $name = $returned_course[0]['name'];
        $id = $returned_course[0]['id'];
        $new_course = new Course($name, $id);
        array_push($courses, $new_course);
      }
      return $courses;
    }

    // function getCourseId() {
    //
    // }

    function delete() {
      $GLOBALS['DB']->exec("DELETE FROM students WHERE id = {$this->getId()};");
      $GLOBALS['DB']->exec("DELETE FROM students_courses WHERE student_id = {$this->getId()};");
    }

    static function find($search_id) {
      $found_student = null;
      $students = Student::getAll();
      foreach ($students as $student) {
        $student_id = $student->getId();
        if ($student_id == $search_id) {
          $found_student = $student;
        }
      }
      return $found_student;
    }

    static function getAll() {
      $returned_students = $GLOBALS['DB']->query("SELECT * FROM students;");
      $students = array();
      foreach($returned_students as $student) {
        $name = $student['name'];
        $id = $student['id'];
        $enrollment_date = $student['enrollment_date'];
        $enrollment_date = str_replace("-", "/", $enrollment_date);
        $new_student = new Student($name, $id, $enrollment_date);
        array_push($students, $new_student);
      }
      return $students;
    }

    static function deleteAll() {
      $GLOBALS['DB']->exec("DELETE FROM students *;");
      $GLOBALS['DB']->exec("DELETE FROM students_courses *;");
    }
  }
?>
