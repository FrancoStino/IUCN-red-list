# IUCN Red List Explorer

A Laravel 12 web application that explores the IUCN Red List API v4, displaying species assessments by ecological system
and country. Built with Livewire 4 and Tailwind CSS 4.

## Features

- **Dashboard**: Browse species assessments by ecological system (Terrestrial, Freshwater, Marine) or by country
- **Assessments List**: View paginated assessments with list/card toggle and pagination/infinite scroll switch
- **Species Detail**: View taxonomic information, common names, and assessment history
- **Assessment Detail**: View conservation status, population trend, conservation actions, and documentation
- **Favorites**: Add species to a global favorites list (persisted in database)
- **Cached Footer**: API version, Red List version, and species count (cached for 1 day)
- **Country Flags**: ISO alpha-2 country codes displayed with Unicode flag emojis
- **Filters**: Filter assessments by year and extinction status

## Tech Stack

- **Backend**: Laravel 12, PHP 8.4
- **Frontend**: Livewire 4, Tailwind CSS 4, Vite
- **Database**: MariaDB 11.4
- **Development**: Lando (Docker-based)
- **Package Manager**: Yarn

## Requirements

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) (or Docker Engine + Docker Compose)
- [Lando](https://lando.dev/) (v3.x or later)
- An IUCN Red List API v4 token ([request one here](https://api.iucnredlist.org/))

## Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd IUCN-Red-List
   ```

2. **Copy the environment file**
   ```bash
   cp .env.example .env
   ```

3. **Set your IUCN API token**

   Open `.env` and add your token:
   ```
   IUCN_API_TOKEN=your_token_here
   ```

4. **Start Lando**
   ```bash
   lando start
   ```
   This will:
    - Build the Docker containers (PHP 8.4, Nginx, MariaDB 11.4)
    - Install Node.js and Yarn
    - Install frontend dependencies via `yarn`

5. **Install PHP dependencies**
   ```bash
   lando composer install
   ```

6. **Generate application key**
   ```bash
   lando artisan key:generate
   ```

7. **Run database migrations**
   ```bash
   lando artisan migrate
   ```

8. **Build frontend assets**
   ```bash
   lando yarn build
   ```

9. **Access the application**

   Open your browser and navigate to the URL shown by Lando `https://iucn-red-list.lndo.site`.

## Development

To run the Vite development server with hot-reload:

```bash
lando dev
```

## Available Lando Commands

| Command          | Description                   |
|------------------|-------------------------------|
| `lando artisan`  | Run Laravel Artisan commands  |
| `lando composer` | Run Composer commands         |
| `lando yarn`     | Run Yarn commands             |
| `lando dev`      | Start Vite dev server         |
| `lando tinker`   | Open Laravel Tinker REPL      |
| `lando test`     | Run PHPUnit tests             |
| `lando pint`     | Run Laravel Pint (code style) |

## Caching Strategy

| Data              | Cache Duration | Description                                  |
|-------------------|----------------|----------------------------------------------|
| Footer statistics | 1 day          | API version, Red List version, species count |
| Dashboard lists   | 1 hour         | Systems and countries lists                  |
| Detail pages      | 5 minutes      | Taxon and assessment details                 |

## API Reference

This application consumes the [IUCN Red List API v4](https://api.iucnredlist.org/api/v4/).

## License

This project is for evaluation purposes.
