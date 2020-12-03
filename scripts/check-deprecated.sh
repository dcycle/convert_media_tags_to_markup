#!/bin/bash
#
# Check for deprecated code.
#
set -e

docker run --rm -v "$(pwd)":/var/www/html/modules/convert_media_tags_to_markup dcycle/drupal-check:1 convert_media_tags_to_markup/src
