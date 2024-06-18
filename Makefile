# See https://tech.davis-hansson.com/p/make/
MAKEFLAGS += --warn-undefined-variables
MAKEFLAGS += --no-builtin-rules

OS := $(shell uname)
ERROR_COLOR := "\033[41m"
NO_COLOR := "\033[0m"

PHPUNIT_BIN := vendor/bin/phpunit
PHPUNIT := $(PHPUNIT_BIN)


.DEFAULT_GOAL := help

.PHONY: help
help:
	@printf "\033[33mUsage:\033[0m\n  make TARGET\n\n\033[32m#\n# Commands\n#---------------------------------------------------------------------------\033[0m\n\n"
	@grep -F -h "##" $(MAKEFILE_LIST) | grep -F -v "grep -F" | sed -e 's/\\$$//' | sed -e 's/##//' | awk 'BEGIN {FS = ":"}; {printf "\033[33m%s:\033[0m%s\n", $$1, $$2}'


.PHONY: test
test:	## Runs the tests
test: phpunit

.PHONY: phpunit
phpunit: vendor $(PHPUNIT_BIN)
	$(MAKE) _phpunit

.PHONY: _phpunit
_phpunit:
	$(PHPUNIT)

.PHONY: vendor_install
vendor_install:
	composer install --ansi
	touch -c vendor

vendor: composer.lock
	composer install --ansi
	touch -c "$@"

composer.lock: composer.json
	composer update --lock --ansi
	touch -c "$@"
