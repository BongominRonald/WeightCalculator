# Charm: S6 Weight Calculator Application

## Overview
Charm is a web-based application designed to calculate weights for Advanced and Ordinary Level subjects in the Ugandan education system. It helps students determine eligibility and weights for university admissions based on their exam results.

## Features
- User authentication (login/signup)
- Subject selection for O-Level and A-Level
- Score entry and weight calculation
- Results viewing and management
- Responsive design with dark/light theme toggle
- Mobile-friendly sidebar navigation

## Technology Stack
- **Backend**: PHP (MVC pattern)
- **Frontend**: HTML, CSS, JavaScript
- **Database**: SQL (schema provided)
- **Server**: XAMPP/Apache

## Installation
1. Clone or download the project to your XAMPP htdocs folder (e.g., `C:\XAMMP\htdocs\Charm`).
2. Import `config/database.sql` into your MySQL database.
3. Update database connection in `config/db_connect.php`.
4. Start XAMPP and navigate to `http://localhost/Charm`.

## Usage
1. **Landing Page**: Register or log in.
2. **Dashboard**: Follow the guided steps to add subjects, enter scores, and calculate weights.
3. **Navigation**: Use the sidebar to access different sections.
4. **Theme**: Toggle between dark and light modes.

## File Structure
```
Charm/
├── index.html              # Landing page with login/signup
├── app/
│   ├── controllers/        # PHP controllers (Auth, Dashboard)
│   ├── models/             # PHP models (User, Result, etc.)
│   ├── views/              # PHP views and partials
│   └── config/             # Database connection
├── assets/
│   ├── style.css           # Main stylesheet
│   ├── script.js           # Main JavaScript
│   └── (icons)             # Favicon and icons
└── config/
    └── database.sql        # Database schema
```

## API Endpoints
- `AuthController.php?action=login` - User login
- `AuthController.php?action=signup` - User registration
- `AuthController.php?action=logout` - User logout
- `DashboardController.php?action=<page>` - Various app pages

## Contributing
- Follow PHP MVC patterns.
- Use meaningful variable names.
- Add comments for complex logic.
- Test on multiple devices for responsiveness.

## License
This project is for educational purposes.