# See https://tech.davis-hansson.com/p/make/
MAKEFLAGS += --warn-undefined-variables
MAKEFLAGS += --no-builtin-rules

OS := $(shell uname)
ERROR_COLOR := "\033[41m"
NO_COLOR := "\033[0m"

.DEFAULT_GOAL := help

.PHONY: help
help:
	@printf "\033[33mUsage:\033[0m\n  make TARGET\n\n\033[32m#\n# Commands\n#---------------------------------------------------------------------------\033[0m\n\n"
	@grep -F -h "##" $(MAKEFILE_LIST) | grep -F -v "grep -F" | sed -e 's/\\$$//' | sed -e 's/##//' | awk 'BEGIN {FS = ":"}; {printf "\033[33m%s:\033[0m%s\n", $$1, $$2}'



vendor: composer.lock
	eval $(COMPOSER) install --no-scripts
	bash .makefile/touch.sh "$@"

composer.lock: composer.json
	@echo $(ERROR_COLOR)$(@) is not up to date.$(NO_COLOR)
	touch -c "$@"
