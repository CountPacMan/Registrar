<?php

  /**
    * @backupGlobals disabled
    * @backupStaticAttributes disabled
    */

  require_once "src/Course.php";
  require_once "src/Student.php";

  $DB = new PDO('pgsql:host=localhost;dbname=registrar');

  class CourseTest extends PHPUnit_Framework_TestCase {

    protected function tearDown() {
      Student::deleteAll();
      Course::deleteAll();
    }

    function test_getName() {
      // Arrange
      $name = "Maths";
      $test_course = new Course($name, 'M101');

      // Act
      $result = $test_course->getName();

      // Assert
      $this->assertEquals($name, $result);
    }

    function test_getId() {
      // Arrange
      $name = "Maths";
      $id = 1;
      $test_course = new Course($name, 'M101', $id);

      // Act
      $result = $test_course->getId();

      // Assert
      $this->assertEquals(1, $result);
    }

    function test_setId() {
      // Assert
      $name = "Maths";
      $test_course = new Course($name, 'M101');

      // Act
      $test_course->setId(2);
      $result = $test_course->getId();

      // Assert
      $this->assertEquals(2, $result);
    }

    function test_save() {
      // Arrange
      $name = "Maths";
      $test_course = new Course($name, 'M101');
      $test_course->save();

      // Act
      $result = Course::getAll();

      // Assert
      $this->assertEquals($test_course, $result[0]);
    }

    function test_getAll() {
      // Arrange
      $name = "Maths";
      $name2 = "Sciences";
      $test_course = new Course($name, 'M101');
      $test_course->save();
      $test_course2 = new Course($name2, 'M101');
      $test_course2->save();

      // Act
      $result = Course::getAll();

      // Assert
      $this->assertEquals([$test_course, $test_course2], $result);
    }

    function test_deleteAll() {
      // Arrange
      $name = "Maths";
      $name2 = "Sciences";
      $test_course = new Course($name, 'M101');
      $test_course->save();
      $test_course2 = new Course($name, 'M101');
      $test_course2->save();

      // Act
      Student::deleteAll();
      Course::deleteAll();
      $result = Course::getAll();

      // Assert
      $this->assertEquals([], $result);
    }

    function testDelete() {
      //Arrange
      $name = "Maths";
      $id = 1;
      $test_course = new Course($name, 'M101', $id);
      $test_course->save();

      $student_name = "Dennis Lumberg";
      $id2 = 2;
      $test_student = new Student($student_name, '1999/01/01', $id2);
      $test_student->save();

      //Act
      $test_course->addStudent($test_student);
      $test_course->delete();

      //Assert
      $this->assertEquals([], $test_student->getCourses());
    }

    function test_find() {
      // Arrange
      $name = "Maths";
      $name2 = "Sciences";
      $test_course = new Course($name, 'M101');
      $test_course->save();
      $test_course2 = new Course($name2, 'M101');
      $test_course2->save();

      // Act
      $result = Course::find($test_course->getId());

      // Assert
      $this->assertEquals($test_course, $result);
    }

    function testAddStudent() {
        //Arrange
        $name = "Maths";
        $id = 1;
        $test_course = new Course($name, 'M101', $id);
        $test_course->save();

        $student_name = "Dennis Lumberg";
        $id2 = 2;
        $test_student = new Student($student_name, '1999/01/01', $id2);
        $test_student->save();

        //Act
        $test_course->addStudent($test_student);

        //Assert
        $this->assertEquals($test_course->getStudents()[0], $test_student);
    }

    function test_getStudents() {
      // Arrange
      $name = "Maths";
      $id = 1;
      $test_course = new Course($name, 'M101', $id);
      $test_course->save();

      $student_name = "Biscuitdoughhandsman";
      $id2 = 2;
      $test_student = new Student($student_name, '1999/01/01', $id2);
      $test_student->save();

      $student_name2 = "Bob";
      $id3 = 3;
      $test_student2 = new Student($student_name2, '1999/01/01', $id3);
      $test_student2->save();

      // Act
      $test_course->addStudent($test_student);
      $test_course->addStudent($test_student2);

      // Assert
      $this->assertEquals($test_course->getStudents(), [$test_student, $test_student2]);
    }

    function test_search() {
      // Arrange
      $name = "Maths";
      $test_course = new Course($name, 'M101');
      $test_course->save();

      $test_course_id = $test_course->getId();
      $student_name = "Biscuitdoughhandsman";
      $test_student = new Student($student_name, '1999/01/01');
      $test_student->save();

      // Act
      $result = $test_course->search($student_name);

      // Assert
      $this->assertEquals($test_student, $result[0]);
    }

    function test_updateName() {
      // Assert
      $name = "Maths";
      $id = null;
      $test_course = new Course($name, 'M101');
      $test_course->save();

      $new_name = "Sciences";

      // Act
      $test_course->updateName($new_name);

      // Assert
      $this->assertEquals("Sciences", $test_course->getName());
    }

    function test_updateCourseNumber() {
      // Assert
      $name = "Maths";
      $id = null;
      $test_course = new Course($name, 'M101');
      $test_course->save();

      $new_course_number = "M102";

      // Act
      $test_course->updateCourseNumber($new_course_number);

      // Assert
      $this->assertEquals("Maths", $test_course->getName());
    }

  }
?>
