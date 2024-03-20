## Installation

Follow these steps to run the application:

1. Ensure PHP, Composer, and the SQLite extension are installed on your system.
2. Clone this repository. __Do not fork it!__
3. Run `composer install` to install required dependencies.
4. Run `php artisan migrate` to run the migration that splits the doctors table into doctors and clinics and migrates the data across, preserving all the existing relations.
5. Run `php artisan serve` to start the Laravel development server.
6. Run `yarn dev` to run Vite and compile Tailwind resources (optional: `npm install vite` if it complains that vite is not found).
7. Visit the application in your browser at http://127.0.0.1:8000/doctors, http://127.0.0.1:8000/tests or http://127.0.0.1:8000/clinics.

## Solution

* **Database Restructuring:** Created a new migration script to redesign the database structure, decoupling doctors and clinics and establishing a many-to-many relationship between doctors and clinics using a pivot table. Migrated existing data while preserving relationships between doctors, clinics, and tests.
* **Model and Controller Updates:** Adjusted models, controllers, and views to align with the revised structure. Implemented functionality to retrieve clinics for each doctor and vice versa, as well as to manage many-to-many records (addition and removal). Introduced a dedicated clinics section for clinics data management (http://127.0.0.1:8000/clinics).
* **Search Functionality:** Implemented search functionality for both doctors and clinics, enhancing user experience and facilitating data retrieval.
* **Merge Functionality:** Enabled manual merging of duplicate doctors and clinics. Users can select one or more checkboxes and initiate the merge process. In the modal dialog, they can choose the target record and confirm the merge. Behind the scenes, the code seamlessly migrates all attached records to the target, performs data deduplication, and removes the source records.

Overall, the solution enhances the application's database structure and functionality, providing users with a streamlined experience for managing doctor and clinic data, while ensuring data integrity and usability.


