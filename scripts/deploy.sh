#!/bin/bash
#
# Assuming you have the latest version Docker installed, this script will
# fully create or update your development environment.
#
set -e

if [ "$1" != "9" ] && [ "$1" != "10" ]; then
  >&2 echo "Please specify 9 or 10"
  exit 1;
fi

echo ''
echo 'About to try to get the latest version of'
echo 'https://hub.docker.com/r/dcycle/drupal/ from the Docker hub. This image'
echo 'is updated automatically every Wednesday with the latest version of'
echo 'Drupal and Drush. If the image has changed since the latest deployment,'
echo 'the environment will be completely rebuilt based on this image.'
if [ "$1" == "9" ]; then
  docker pull dcycle/drupal:9php8-fpm-alpine
else
  docker pull dcycle/drupal:10-fpm-alpine
fi

echo ''
echo '-----'
echo 'About to create the convert_media_tags_to_markup_default network if it does not exist,'
echo 'because we need it to have a predictable name when we try to connect'
echo 'other containers to it (for example browser testers).'
echo 'See https://github.com/docker/compose/issues/3736.'
docker network ls | grep convert_media_tags_to_markup_default || docker network create convert_media_tags_to_markup_default

echo ''
echo '-----'
echo 'About to start persistent (-d) containers based on the images defined'
echo 'in ./Dockerfile and ./docker-compose.yml. We are also telling'
echo 'docker-compose to rebuild the images if they are out of date.'
docker-compose -f docker-compose.yml -f docker-compose."$1".yml up -d --build

echo ''
echo '-----'
echo 'Running the deploy script on the running containers. This installs'
echo 'Drupal if it is not yet installed.'
docker-compose exec -T drupal /bin/bash -c '/docker-resources/scripts/deploy-on-container.sh'

echo ''
echo '-----'
echo ''
echo 'If all went well you can now access your site at:'
./scripts/uli.sh
echo '-----'
echo ''
