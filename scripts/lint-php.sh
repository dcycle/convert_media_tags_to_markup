#!/bin/bash
#
# Lint php files.
#

docker run --rm -v "$(pwd)"/src:/code dcycle/php-lint:2 \
  --standard=DrupalPractice /code
docker run --rm -v "$(pwd)"/src:/code dcycle/php-lint:2 \
  --standard=Drupal /code
