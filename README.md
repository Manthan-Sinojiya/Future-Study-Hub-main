# Future Study Hub

Future Study Hub is an advanced online educational platform designed to support individuals in planning their future academic and career pathways. The platform provides comprehensive preparation for the IELTS exam and offers a wide range of courses to enhance various skills. Users can purchase courses, access video lectures, and take exams directly on the platform.

## Features

- **IELTS Preparation**: Comprehensive resources to help users prepare for the IELTS exam.
- **Wide Range of Courses**: Diverse selection of courses to enhance different skill sets.
- **Video Lectures**: High-quality video lectures available for enrolled courses.
- **Online Exams**: Users can take exams to test their knowledge and track their progress.
- **Course Purchases**: Integrated system for purchasing courses securely.

## Installation

To set up the Future Study Hub on your local system, follow these steps:

1. **Clone the Repository:**
   ```bash
   git clone https://github.com/yourusername/future-study-hub.git
   cd future-study-hub
   ```

2. **Setup the Environment:**
   - Make sure you have PHP installed on your machine.
   - Install a web server like Apache or Nginx with MySQL.

3. **Database Configuration:**
   - Create a new MySQL database for the application.
   - Import the provided SQL file into your database:
     ```bash
     mysql -u username -p database_name < database.sql
     ```
   - Update the database credentials in the `config.php` file:
     ```php
     <?php
     define('DB_HOST', 'localhost');
     define('DB_USER', 'your_db_username');
     define('DB_PASS', 'your_db_password');
     define('DB_NAME', 'your_db_name');
     ?>
     ```

4. **Run the Application:**
   - Place the project folder in your web server's root directory (e.g., `/var/www/html`).
   - Start your web server and navigate to the application in your browser (e.g., `http://localhost/future-study-hub`).

## Usage

- **Sign Up:** Register for an account to access the platform.
- **Browse Courses:** Explore available courses, including IELTS preparation and other skill-enhancing modules.
- **Purchase Courses:** Securely purchase the courses of your choice.
- **Access Content:** Start learning through video lectures and take practice exams to assess your progress.

## Folder Structure

```
Directory structure:
└── manthan-sinojiya-future-study-hub-main/
    ├── Registration.php
    ├── SMTPDebug
    ├── access_granted.php
    ├── adm
    ├── composer
    ├── composer.json
    ├── composer.lock
    ├── composer.phar
    ├── courses.php
    ├── download.php
    ├── exam.php
    ├── fetch_messages.php
    ├── fetch_suggestions.php
    ├── forgetotp.php
    ├── forgetpass.php
    ├── forgetvr.php
    ├── get_payment_data.php
    ├── index.php
    ├── li.php
    ├── li_fetch_questions.php
    ├── li_submit_answers.php
    ├── li_view_mark.php
    ├── login.php
    ├── logout.php
    ├── material.php
    ├── otp.php
    ├── profile.php
    ├── re.php
    ├── re_fetch_questions.php
    ├── re_submit_answers.php
    ├── send_message.php
    ├── track_progress.php
    ├── update_profile.php
    ├── verify_payment.php
    ├── video-lectures.php
    ├── view_result.php
    ├── watch-video.php
    ├── wr.php
    ├── wr_fetch_question.php
    ├── wr_submit_answers.php
    ├── Admin/
    ├── Teacher/
    ├── assets/
    ├── payment/
    ├── uploads/
    └── vendor/
```

## Technologies Used

- **Backend:** PHP
- **Frontend:** HTML, CSS, JavaScript
- **Database:** MySQL
- **Server:** Apache

## Contribution

We welcome contributions to improve Future Study Hub. To contribute:

1. Fork the repository.
2. Create a new branch for your feature or bug fix.
3. Commit your changes and push the branch to your fork.
4. Open a pull request describing the changes.

## License

This project is licensed under the MIT License. See the `LICENSE` file for details.

## Contact

For any questions or feedback, please contact us at [your-email@example.com].
