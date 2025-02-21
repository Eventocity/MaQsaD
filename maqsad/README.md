# MaQsaD - Service Provider Platform

MaQsaD is a web platform that connects service providers with beneficiaries, facilitating easy service discovery and management.

## Features

- User Authentication (Login/Signup)
- Role-based Access Control (Provider/Beneficiary)
- Service Management
- Provider Directory
- Service Request System

## Tech Stack

- PHP 8.2.12
- MySQL 5.7+
- Bootstrap 5.1.3
- HTML5/CSS3/JavaScript

## Prerequisites

- XAMPP (or similar PHP development environment)
- MySQL Server
- Web Browser

## Installation

1. Clone the repository:
```bash
git clone https://github.com/yourusername/maqsad.git
```

2. Import the database:
```bash
mysql -u root < database/maqsad_db.sql
```

3. Configure database connection:
   - Open `auth/connect.php`
   - Update database credentials if needed

4. Start your web server and navigate to:
```
http://localhost/maqsad
```

## Project Structure

```
maqsad/
├── api/              # API endpoints
├── auth/             # Authentication files
│   ├── connect.php   # Database connection
│   ├── login.php     # Login handler
│   ├── logout.php    # Logout handler
│   └── signup.php    # Signup handler
├── database/         # Database files
│   ├── maqsad_db.sql # Full database dump
│   ├── reset_db.sql  # Database reset script
│   └── schema.sql    # Database schema
├── uploads/          # File uploads directory
├── dashboard.php     # User dashboard
├── index.html        # Landing page
└── README.md         # Project documentation
```

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Contact

Your Name - your.email@example.com
Project Link: https://github.com/yourusername/maqsad
