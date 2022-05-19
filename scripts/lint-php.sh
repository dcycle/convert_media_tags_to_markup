#!/bin/bash
#
# Lint php files.
#
set -e

docker run --rm -v "$(pwd)"/src:/code dcycle/php-lint:3 \
  --standard=DrupalPractice /code
docker run --rm -v "$(pwd)"/src:/code dcycle/php-lint:3 \
  --standard=Drupal /code
