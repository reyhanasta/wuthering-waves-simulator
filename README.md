# Wuthering Waves Gacha Simulator

## Description
**Wuthering Waves Gacha Simulator** is a website simulator that allows users to experience the gacha mechanics of the *Wuthering Waves* game. This simulator is designed to give users an idea of the chances of obtaining certain items in the game without spending any in-game resources.

## Installation
This project is built using the **Laravel** framework with **MySQL** as the database, **Redis** for cache management, **Filament** for the admin panel, **Livewire** for dynamic interactions, and **Vite** for frontend asset management.

To install and run this project, follow these steps:

1. **Clone the Repository**:
   ```bash
   git clone https://github.com/reyhanasta/wuthering-waves-simulator.git
2. **Install Frontend Dependencies (NPM)**:
   ```bash
   npm install
3. **Environment Configuration**: Copy the .env.example file to .env and configure your database and Redis settings.
   ```bash
   cp .env.example .env
4. **Generate Laravel Key**:
   ```bash
   php artisan key:generate
6. **Run Database Migration**:
   ```bash
   php artisan migrate
7. **Run Vite**: Compile your frontend assets using vite
   ```bash
   npm run dev
8. **Start the Local Server**:
   ```bash
   php artisan serve

## Usage
After installation and setup, you can access the application through:
1. **Gacha Page**: http://localhost:8000/standard-banner - This is the main page for the gacha simulation.
2. **Admin Page**: http://localhost:8000/admin - Access the admin panel created with Filament, where you can manage the application's data and settings.

## Customizing Gacha Results Icons/Images
Currently, the icons/images displayed in the gacha results are dummy data. To customize the images to match the actual in-game assets or preferred visuals, you need to update the data through the admin panel.

To do this:

Go to the Admin Panel at http://localhost:8000/admin.
Navigate to the Weapons or Charcaters section.
Update the image URLs or upload the desired images by selecting the specific Weapons or Characters.
Save the changes, and the gacha results will display the updated visuals accordingly.

## License
This project is licensed under the MIT License. See the LICENSE file for more information.

