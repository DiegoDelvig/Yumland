#!/usr/bin/env bash

cd "$(dirname "$0")"
php -S localhost:8000 router.php
