#!/bin/bash
set -e

echo "Installing Composer dependencies..."
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

echo "Installing Node dependencies..."
npm install

echo "Building frontend assets..."
npm run build

echo "Build completed successfully!"

