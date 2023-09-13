#!/bin/bash
#
# Fix PHP code linting issues.
#
set -e

echo 'Fixing PHP style errors with https://github.com/dcycle/docker-php-lint'
echo ''

docker run \
  --rm -v "$(pwd)"/src:/code \
  --entrypoint=/vendor/bin/phpcbf \
  dcycle/php-lint:3 --standard=DrupalPractice /code \
  || true
docker run \
  --rm -v "$(pwd)"/src:/code \
  --entrypoint=/vendor/bin/phpcbf \
  dcycle/php-lint:3 --standard=Drupal /code \
  || true

echo ' => WE FIXED WHAT WE COULD!'
echo ''
