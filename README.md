# REST Scheduler API


This project was implemented using the Symfony 3.1 framework

The API has two roles 
- employee (read)
- manager (read/write)

Each request has to have a valid token in the header. Each user has their own valid token.

## Data Structures
I changed the data structure to have 3 tables instead of 2.
I am assuming that changing the data structure to a more efficient design is part of the test.
Having the shifts and shift assigments in seperate tables is more efficient when shift times or managers have to be changed as the change
will only have to occur in one row instead of searching for every row that matches to update them.

### User

| field       | type |
| ----------- | ---- |
| id          | id |
| name        | string |
| role        | string |
| email       | string |
| phone       | string |
| wtoken      | string |
| created_at  | date |
| updated_at  | date |

The `role` must be either `employee` or `manager`. 
wtoken is a unique token that has to be in the header of the request to the API in order to authenticate the request.
For example: curl -X GET 'http://127.0.0.1:8000/employee/when' -H "W-Token: iamarya"

### Shift

| field       | type |
| ----------- | ---- |
| id          | id |
| manager_id  | fk |
| break       | float |
| start_time  | date |
| end_time    | date |
| created_at  | date |
| updated_at  | date |

### Shift Assignment

| field       | type |
| ----------- | ---- |
| id          | id |
| employee_id | fk |
| shift_id    | fk |
| created_at  | date |
| updated_at  | date |

These are some test cases from the console for the stories
- [ ] As an employee, I want to know when I am working, by being able to see all of the shifts assigned to me.

curl -X GET 'http://127.0.0.1:8000/employee/when' -H "W-Token: iamarya"

- [ ] As an employee, I want to know who I am working with, by being able to see the employees that are working during the same time period as me. The shift Id is passed by appending it to the end of the URL.

curl -X GET 'http://127.0.0.1:8000/employee/who/1' -H "W-Token: iamarya"


- [ ] As an employee, I want to know how much I worked, by being able to get a summary of hours worked for each week.

curl -X GET 'http://127.0.0.1:8000/employee/worked?start=2016-07-17%2000:00:00&end=2016-07-23%2023:59:59' -H "W-Token: iamarya"

- [ ] As an employee, I want to be able to contact my managers, by seeing manager contact information for my shifts. The shift Id is passed by appending it to the end of the URL.

curl -X GET 'http://127.0.0.1:8000/employee/contact/1' -H "W-Token: iamarya"


- [ ] As a manager, I want to schedule my employees, by creating shifts for any employee.
If manager Id is blank, the manager Id(from the token) that created the shift will be used.
PUT is used as the shift or shift assignemnt may be created or updated.

curl -X PUT -d manager_id=3 -d employee_id=6 -d break_time=0.5 -d start='2016-07-24 02:00:00' -d end='2016-07-24 10:00:00' http://127.0.0.1:8000/manager/schedule -H "W-Token: iamjon"


- [ ] As a manager, I want to see the schedule, by listing shifts within a specific time period.

curl -X GET 'http://127.0.0.1:8000/manager/shift?start=2016-07-17%2000:00:00&end=2016-07-29%2023:59:59' -H "W-Token: iamjon"

- [ ] As a manager, I want to be able to change a shift, by updating the time details. PUT is used as this is used to update.

curl -X PUT -d shift_id=4 -d start='2016-07-17 10:00:00' -d end='2016-07-17 18:00:00' http://127.0.0.1:8000/manager/change -H "W-Token: iamjon"


- [ ] As a manager, I want to be able to assign a shift, by changing the employee that will work a shift.PUT is used as shift assignment may be created or updated.

curl -X PUT -d shift_id=7 -d current_employee_id=1 -d new_employee_id=6 http://127.0.0.1:8000/manager/assign -H "W-Token: iamjon"


- [ ] As a manager, I want to contact an employee, by seeing employee details.

curl -X GET 'http://127.0.0.1:8000/manager/contact/1' -H "W-Token: iamjon"


The entirety of the code for the API is in \src\AppBundle\Controller\ShiftController.php

Oro Doctrine Extensions (https://github.com/orocrm/doctrine-extension) is installed to allow the use of TIMESTAMPDIFF in Doctrine.

The ORM is at \src\AppBundle\Entity

The sql for the database is in worksched.sql
# worksched
