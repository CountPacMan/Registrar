# University Registrar for Epicodus
## by Daniel Toader and Bryan Borgeson
### Date: March 24, 2015
#### Description
University Registrar App allows users to keep track of students and courses, using Postgres SQL for the back-end and Silex/Twig for the front-end.  For example:

1. A registrar can keep track of all students enrolled at the University by entering a students name and date of enrollment.

2. A registrar can keep track of all offered courses by simply providing a course name and number.  From here, the registrar should be able to assign students to a course, so that a teacher can know who's in their course(s).

#### Setup instructions
1. Clone this git repository
2. Set your localhost root folder to ~/Registrar/web/
3. Ensure PHP server is running.
4. Start Postgres and import registrar.sql database into a new database registrar
5. Use Composer to install required dependencies in the composer.json file
6. Start the web app by pointing your browser to the root (http://localhost:8000/)

#### Copyright © 2015, Daniel Toader and Bryan Borgeson

#### License: [MIT](https://github.com/twbs/bootstrap/blob/master/LICENSE")  

#### Technologies used
- HTML5
- CSS3
- Bootstrap ver 3.3.1
- PHP (tested to run on PHP ver 5.6.6)
- Silex ver 1.2.3
- Twig ver 1.18.0
- PHPUnit ver 4.5.0
- PostgreSQL ver 9.4.1
