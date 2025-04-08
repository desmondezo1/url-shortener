#!/bin/bash

# Build the Docker image
docker build -t url-shortener .

# Run the container
docker run -p 8000:8000 -d --name url-shortener-app url-shortener

./vendor/bin/phpunit

echo "URL Shortener is running at http://localhost:8000"
echo "Test with:"
echo "See documentation at http://localhost:8000/api-docs"