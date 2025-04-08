# URL Shortener Service - Setup Guide

## Prerequisites
- PHP 8.0+
- Composer
- Docker (optional)

## Setup

### Local Installation

1. Clone the repository
   ```bash
   git clone https://github.com/desmondezo1/url-shortener.git
   cd url-shortener
   ```

2. Install dependencies
   ```bash
   composer install
   ```

3. Configure environment
   ```bash
   cp .env.example .env
   ```

4. Set permissions
   ```bash
   chmod -R 755 storage bootstrap/cache
   ```

5. Start the server
   ```bash
   php -S localhost:8000 -t public
   ```

### Docker Installation

Ensure docker is running before you begin:

1. Build and run with Docker
   ```bash
   chmod +x ./run.sh
   ./run.sh
   ```

## API Endpoints

### Encode URL
```
POST /encode
```
Request:
```json
{
  "url": "https://www.example.com/with/some/long/path"
}
```

### Decode URL
```
POST /decode
```
Request:
```json
{
  "url": "http://short.est/AbC123"
}
```

## Testing

To test the project run:

```bash
php vendor/bin/phpunit
```

## API Documentation

To view api documentation, start the php server and visit "/api-docs".
If you are running on port 8000, it will be:
```bash
"https://localhost:8000/api-docs"
```


## Implementation Details
The service uses an in-memory store with caching and implements collision detection for generating unique short codes.