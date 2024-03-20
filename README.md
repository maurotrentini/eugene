## Installation

Follow these steps to run the application:

1. Ensure PHP, Composer, and the SQLite extension are installed on your system.
2. Clone this repository. __Do not fork it!__
3. Run `composer install` to install required dependencies.
4. Run `php artisan migrate` to run the migration that splits the doctors table into doctors and clinics and migrates the data across, preserving all the existing relations.
5. Run `php artisan serve` to start the Laravel development server.
6. Run `yarn dev` to run Vite and compile Tailwind resources (optional: `npm install vite` if it complains that vite is not found).
7. Visit the application in your browser at http://127.0.0.1:8000/doctors or http://127.0.0.1:8000/tests.