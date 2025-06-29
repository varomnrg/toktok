#!/bin/bash

# Ensure .env has Unix-style line endings (removes \r)
if [ -f .env ]; then
    echo "Fixing line endings in .env with dos2unix"
    dos2unix .env >/dev/null 2>&1

    echo "Loading environment variables from .env"
    set -a
    source .env
    set +a
else
    echo ".env file not found!"
fi

# Start local PHP server
php -S localhost:8000
