# Biometric Examination Verification System

A university-standard biometric fingerprint-based examination attendance verification system. The system enables secure student verification through biometric scanning and automated attendance tracking.

## Features

✅ **Landing Page** - Professional introduction to the system
✅ **Authentication** - Secure login for administrators and invigilators
✅ **Student Enrollment** - Single and bulk CSV enrollment with fingerprint registration
✅ **Biometric Verification** - Real-time fingerprint scanning and student identification
✅ **Dashboard** - Statistics and analytics for attendance and enrollment
✅ **Attendance Management** - Track present/absent students with detailed records
✅ **Student Management** - View all students organized by department and level

## Tech Stack

- **Frontend**: HTML5, CSS3 (Tailwind CSS), JavaScript (Vanilla)
- **Backend**: PHP 8.0+
- **Database**: MySQL 5.7+
- **Biometric**: Fingerprint matching with similarity scoring

## Quick Start

1. Clone repository
2. Run `composer install`
3. Run `npm install`
4. Import database/schema.sql
5. Start server: `php -S localhost:8000 -t public/`

## Database Schema

Complete schema includes:
- Users (Admin, Invigilators)
- Students (organized by Department & Level)
- Departments & Levels
- Enrollments (fingerprint templates)
- Examinations
- Attendance Records
- CSV Upload Logs

## API Endpoints

- `POST /backend/api/auth.php?action=login` - Login
- `GET /backend/api/students.php` - Get students
- `POST /backend/api/biometric.php?action=verify` - Verify fingerprint
- `POST /backend/api/attendance.php?action=mark` - Mark attendance

## License

MIT
