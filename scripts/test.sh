#!/bin/bash
#
# Run all tests and linting.
#
set -e

./scripts/check-deprecated.sh
./scripts/lint-php.sh
./scripts/lint-sh.sh
./scripts/unit-tests.sh
