#  Book Request Management System

A multi-role web application built with PHP and MySQL that allows users to request books, 
admins to monitor activity, and a super admin to manage everything.

##  How to Run This Project

### Requirements
- XAMPP (Apache + MySQL)

### Steps
1. Download and install XAMPP from https://www.apachefriends.org
2. Start **Apache** and **MySQL** from XAMPP Control Panel
3. Open your browser and go to `http://localhost/phpmyadmin/`
4. Create a new database named: `book_request_db`
5. Click the **Import** tab, upload the `book_request_db.sql` file from this repo
6. Copy the project folder into `C:\xampp\htdocs\`
7. Open your browser and visit: `http://localhost/book-request-system/`


## Features
- User registration and login with secure password hashing
- Browse books from Google Books API by category
- Submit, view, and cancel book requests
- Admin dashboard with live statistics
- Super Admin full control: manage users, admins, and all requests
- Role-based access control on every page
- PDO prepared statements to prevent SQL injection
