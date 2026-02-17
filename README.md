# Spotify Alike App (PHP & MySQL)

This project is a simple Spotify-like web application built with PHP and MySQL.  
It allows users to log in, browse artists and albums, create playlists, and play audio files.

## Features
- User authentication (login/logout, session management)
- Artist and album pages
- Playlist creation and management
- Dynamic content loaded from MySQL database
- Basic media playback support

## Tech Stack
- Backend: PHP  
- Database: MySQL  
- Frontend: HTML, CSS  
- Media: Local audio and image files

## How to Run
1. Import the database:
   - Create a MySQL database
   - Import the `main.sql` file
2. Configure database connection:
   - Edit `config.php` / `db.php` with your local database credentials
3. Run the project:
   - Use a local server such as XAMPP, WAMP, or Laragon
   - Place the project in the server root folder 
   - Open `http://localhost/` in your browser

## Project Structure
- `index.php` – main entry point  
- `login.php` / `logout.php` – user authentication  
- `album.php`, `artist.php`, `playlist.php` – core application pages  
- `config.php`, `db.php` – database configuration  
- `main.sql` – database schema and sample data  

## Notes
- Audio and image files are not included in this repository.
- Add your own media files locally for testing purposes.


