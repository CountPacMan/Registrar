<?php

  /**
    * @backupGlobals disabled
    * @backupStaticAttributes disabled
    */

  require_once "src/Student.php";

  $DB = new PDO('pgsql:host=localhost;dbname=registrar');

  class StudentTest extends PHPUnit_Framework_TestCase {

    protected function tearDown() {
      Student::deleteAll();
      Course::deleteAll();
    }

    function test_save() {
      // Arrange
      $name = "Biscuitdoughhandsman";
      $test_student = new Student($name, '1999/01/01');

      // Act
      $test_student->save();

      // Assert
      $result = Student::getAll();
      $this->assertEquals($test_student, $result[0]);
    }

    function test_getAll() {
      // Arrange
      $name = "Biscuitdoughhandsman";
      $name2 = "Bob";
      $test_Student = new Student($name, '1999/01/01');
      $test_Student->save();
      $test_Student2 = new Student($name2, '1999/01/01');
      $test_Student2->save();

      // Act
      $result = Student::getAll();

      // Assert
      $this->assertEquals([$test_Student, $test_Student2], $result);
    }

    function test_deleteAll() {
      // Arrange
      $name = "Biscuitdoughhandsman";
      $name2 = "Bob";
      $test_Student = new Student($name, '1999/01/01');
      $test_Student->save();
      $test_Student2 = new Student($name2, '1999/01/01');
      $test_Student2->save();

      // Act
      Student::deleteAll();

      // Assert
      $result = Student::getAll();
      $this->assertEquals([], $result);
    }

    function testDelete() {
      //Arrange
      $name = "Biscuitdoughhandsman";
      $id = 1;
      $test_course = new Course($name, 'M101', $id);
      $test_course->save();

      $name = "Bob";
      $id2 = 2;
      $test_student = new Student($name, '1999/01/01', $id2);
      $test_student->save();

      //Act
      $test_student->addCourse($test_course);
      $test_student->delete();

      //Assert
      $this->assertEquals([], $test_course->getStudents());
    }

    function test_getId() {
      // Arrange
      $name = "Biscuitdoughhandsman";
      $id = 1;
      $test_Student = new Student($name, '1999/01/01', $id);

      // Act
      $result = $test_Student->getId();

      // Assert
      $this->assertEquals(1, $result);
    }

    function test_setId() {
      // Arrange
      $name = "Biscuitdoughhandsman";
      $test_Student = new Student($name, '1999/01/01');

      // Act
      $test_Student->setId(2);

      // Assert
      $result = $test_Student->getId();
      $this->assertEquals(2, $result);
    }

    function test_find() {
      // Arrange
      $name = "Biscuitdoughhandsman";
      $name2 = "Bob";
      $test_Student = new Student($name, '1999/01/01', 1);
      $test_Student->save();
      $test_Student2 = new Student($name2, '1999/01/01', 1);
      $test_Student2->save();

      // Act
      $result = Student::find($test_Student->getId());

      // Assert
      $this->assertEquals($test_Student, $result);
    }

    function test_enrollmentDate() {
      // Arrange
      $name = "Biscuitdoughhandsman";
      $enrollment_date = '1/18/1999';
      $test_Student = new Student($name, $enrollment_date);

      // Act
      $result = $test_Student->getEnrollmentDate();

      // Assert

      $this->assertEquals($enrollment_date, $result);
    }

    function testAddCourse() {
      //Arrange
      $name = "Biscuitdoughhandsman";
      $id = 1;
      $test_course = new Course($name, 'M101', $id);
      $test_course->save();

      $name = "Bob";
      $id2 = 2;
      $test_student = new Student($name, '1999/01/01', $id2);
      $test_student->save();

      //Act
      $test_student->addCourse($test_course);

      //Assert
      $this->assertEquals($test_student->getCourses()[0], $test_course);
    }
  }
?>
