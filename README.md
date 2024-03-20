# Senior Laravel API Developer Code Challenge

## Introduction

This code challenge aims to evaluate your skills in API development, database design, and the Laravel framework. The provided Laravel application comprises two main entities: Doctors and Tests. The current data structure has redundant information about doctors and/or clinics across multiple rows. Your task is to normalize the data by splitting it into two tables: a doctors table containing information about individual doctors, and a clinics table containing information about individual clinics. Note that multiple doctors can work at the same clinic and a clinic can contain multiple doctors.

Additionally, you need to modify the user interface to match the new database structure.

Lastly, the doctor and clinic information is inconsistent due to multiple users' input over time. Develop tools to merge duplicate doctors and clinics.

## Existing Application

The current application includes:

1. A Laravel project named "code-challenge."
2. A SQLite database with preseeded Doctors and Tasks.
3. Controllers, views, form requests, and routes for Doctors and Tests.

## Installation

Follow these steps to run the application:

1. Ensure PHP, Composer, and the SQLite extension are installed on your system.
2. Clone this repository. __Do not fork it!__
3. Run `composer install` to install required dependencies.
4. Run `php artisan serve` to start the Laravel development server.
5. Run `yarn dev` to run Vite and compile Tailwind resources.
6. Visit the application in your browser at http://127.0.0.1:8000/doctors or http://127.0.0.1:8000/tests.

## Code Challenge

Make an update to the application by completing the following tasks:

1. Normalize the `doctors` table by splitting it into two tables:
   - A table containing information about doctors.
   - A table containing information about clinics.
2. Maintain the relationship between the new tables and tests, ensuring the tests index displays the same information after your changes.
3. Create a user interface to perform the following actions:
   - Merge duplicate doctors.
   - Merge duplicate clinics.

### Assumptions and Deviations

1. The .env file is committed to Git. Under normal circumstances, this would be avoided.
2. The authentication system can be ignored.

### Guidelines

1. Limit your work to 4 hours.
2. Document any assumptions you make.
3. You may use any libraries you prefer, but be prepared to justify your choices.
4. Focus on ease of use rather than aesthetics, as this is not a design challenge.

### Bonus Points (Complete only if time permits within the 4-hour period)

1. Implement any additional improvements you deem necessary.
2. Develop a page that recommends likely duplicates and allows users to merge them.
3. Break down the clinic_address into multiple columns and geocode the location.

Submit your solution, along with any additional documentation or explanations, to danny@eugenelabs.com.

Good luck and enjoy the challenge!
