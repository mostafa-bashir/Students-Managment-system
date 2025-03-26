# Student Management System


A comprehensive Student Management System built with Laravel that helps educational institutions manage student data, attendance, and sessions efficiently.

## Features

- **Student Management**: Add, edit, and manage student profiles with complete information
- **Attendance Tracking**: Record and monitor student attendance for sessions
- **Session Management**: Create and organize class sessions
- **User Authentication**: Secure admin panel with role-based access
- **Responsive Design**: Works on desktop and mobile devices
- **Data Export**: Export attendance and student data
- **Search Functionality**: Quickly find students with Arabic/English name search

## Technologies Used

- **Backend**: Laravel 9.x
- **Frontend**: Bootstrap 5, jQuery
- **Database**: MySQL
- **Authentication**: Laravel Sanctum
- **Deployment**: Ready for shared hosting or VPS

## Installation

### Prerequisites

- PHP 8.0 or higher
- Composer
- MySQL 5.7+ or MariaDB
- Node.js (for asset compilation)
- Web server (Apache/Nginx)

### Step-by-Step Installation

1. **Clone the repository**:
   ```bash
   git clone https://github.com/mostafa-bashir/Students-Managment-system.git
   cd Students-Managment-system
   ```

2. **Install PHP dependencies**:
   ```bash
   composer install
   ```

3. **Install JavaScript dependencies**:
   ```bash
   npm install
   npm run build
   ```

4. **Create and configure .env file**:
   ```bash
   cp .env.example .env
   ```
   Edit the `.env` file with your database credentials and app settings.

5. **Generate application key**:
   ```bash
   php artisan key:generate
   ```

6. **Run database migrations**:
   ```bash
   php artisan migrate --seed
   ```

7. **Create storage link**:
   ```bash
   php artisan storage:link
   ```

8. **Set up cron job for task scheduling** (optional):
   Add this to your server's crontab:
   ```bash
   * * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
   ```

## Usage

1. Access the admin panel at `yourdomain.com/admin`
2. Default admin credentials:
   - Email: admin@example.com
   - Password: password
3. Navigate through the dashboard to manage:
   - Students
   - Sessions
   - Attendance
   - System settings

## Contributing

This project is open source and welcomes contributions! Here's how you can help:

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Support

For support, questions, or feature requests, please:
- Open an issue on GitHub
- Email the maintainer at mostafa93.bashir93@gmail.com


## Roadmap

- [ ] Add parent portal
- [ ] Implement gradebook functionality
- [ ] Develop mobile app companion
- [ ] Add multi-language support

## Acknowledgments

- Laravel community
- Bootstrap team
- All contributors to open source packages used in this project

---

**Share this project**: If you find this project useful, please consider starring it on GitHub and sharing it with your network!