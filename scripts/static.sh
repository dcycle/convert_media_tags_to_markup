#!/bin/bash
#
# Static analysis.
#
set -e

echo 'Performing static analsys'
echo 'If you are getting a false negative, use:'
echo ''
echo '// @phpstan-ignore-next-line'
echo ''
echo 'If you are getting unknown class, add a dummy version of the offending'
echo 'class to:'
echo ''
echo './scripts/lib/phpstan/dummy-classes.php'
echo ''

if [[ "$1" != "nopull" ]]; then
  docker pull dcycle/phpstan-drupal:4
fi

docker run --rm \
  -v "$(pwd)":/var/www/html/modules/custom/convert_media_tags_to_markup \
  -v "$(pwd)"/scripts/lib/phpstan:/phpstan-drupal \
  dcycle/phpstan-drupal:4 /var/www/html/modules/custom/convert_media_tags_to_markup \
  -c /phpstan-drupal/phpstan.neon \
