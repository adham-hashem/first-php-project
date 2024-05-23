
# SCHOOL Website Project

## Stack
- HTML
- CSS
- JavaScript
- Bootstrap
- PHP
- MySQL

## Running the Project

1. **Clone the repository**
   ```bash
   git clone <repository_url>
   ```

2. **Move the project files**
   Place the cloned project files in the `xampp\htdocs\` directory.

3. **Start XAMPP**
   - Open XAMPP Control Panel.
   - Start the Apache and MySQL services.

4. **Open the project in a web browser**
   - Navigate to `http://127.0.0.1`.
   - Open the project files and start with `index.php`.

## About the Project

### Features for Website Users

#### Admin
- Admin can create an account and upload a profile image.
- Admin can view the latest school news on the main page.
- Admin can view user, teacher, and student accounts.
- Admin can view feedback messages from users or anonymous sources.
- Admin can update their username and password.
- Admin can delete their account.

#### User
- User can create an account and upload a profile image.
- User can view the latest school news on the main page.
- User can access information about students.
- User can input student grades.
- User can update their username and password.
- User can delete their account.

#### Teacher
- Teacher can create an account and upload a profile image.
- Teacher can view the latest school news on the main page.
- Teacher can view their rating out of 10.
- Teacher can access information about students.
- Teacher can update their username and password.
- Teacher can delete their account.

#### Student
- Student can create an account and upload a profile image.
- Student can view the latest school news on the main page.
- Student can view their grades.
- Student can access information about teachers.
- Student can rate a teacher.
- Student can update their username and password.
- Student can delete their account.

## Setup Instructions

### Database Configuration

1. **Create a Database**
   - Open phpMyAdmin via `http://127.0.0.1/phpmyadmin`.
   - Create a new database for the project.

2. **Import the Database Schema**
   - Import the provided SQL file located in the project directory to set up the necessary tables and data.

### Configuration Files

- Ensure that your database credentials in the PHP files match the setup in phpMyAdmin. Update the configuration file, typically `config.php`, with your database host, username, password, and database name.

### File Permissions

- Ensure that the necessary directories have appropriate write permissions for file uploads (e.g., profile images).

## Contribution

If you would like to contribute to the project, please follow these steps:

1. Fork the repository.
2. Create a new branch (`git checkout -b feature-branch`).
3. Make your changes.
4. Commit your changes (`git commit -am 'Add new feature'`).
5. Push to the branch (`git push origin feature-branch`).
6. Create a new Pull Request.

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

---

Thank you for using our SCHOOL website project. If you have any questions or need further assistance, please feel free to contact us. Happy coding!