#!/bin/bash
#
# Run tests on Circle CI.
#
set -e

./scripts/test.sh
./scripts/deploy.sh 9
docker-compose down -v
./scripts/deploy.sh 10
docker-compose down -v
