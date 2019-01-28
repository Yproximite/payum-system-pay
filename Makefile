.SILENT:
.PHONY: build test

## Colors
COLOR_RESET   = \033[0m
COLOR_INFO    = \033[32m
COLOR_COMMENT = \033[33m

## Help
help:
	printf "${COLOR_COMMENT}Usage:${COLOR_RESET}\n"
	printf " make [target]\n\n"
	printf "${COLOR_COMMENT}Available targets:${COLOR_RESET}\n"
	awk '/^[a-zA-Z\-\_0-9\.@]+:/ { \
		helpMessage = match(lastLine, /^## (.*)/); \
		if (helpMessage) { \
			helpCommand = substr($$1, 0, index($$1, ":")); \
			helpMessage = substr(lastLine, RSTART + 3, RLENGTH); \
			printf " ${COLOR_INFO}%-16s${COLOR_RESET} %s\n", helpCommand, helpMessage; \
		} \
	} \
	{ lastLine = $$0 }' $(MAKEFILE_LIST)

###############
# Environment #
###############

## Setup environment
setup:
	vagrant up --no-provision
	vagrant provision
	vagrant ssh -- "cd /srv/app && make install-app"

## Update environment
update: export ANSIBLE_TAGS = manala.update
update:
	vagrant provision

## Update ansible
update-ansible: export ANSIBLE_TAGS = manala.update
update-ansible:
	vagrant provision --provision-with ansible

## Provision environment
provision: export ANSIBLE_EXTRA_VARS = {"manala":{"update":false}}
provision:
	vagrant provision --provision-with app

## Provision nginx
provision-nginx: export ANSIBLE_TAGS = manala_nginx
provision-nginx: provision

## Provision php
provision-php: export ANSIBLE_TAGS = manala_php
provision-php: provision

############
# Commands #
############

install-app: install-git-hooks
	composer install

install-git-hooks:
	wget --output-document=.git/hooks/pre-commit https://gist.githubusercontent.com/tristanbes/6f968b45d40fbf9da144bf86f8846d32/raw/0dfc2c44a10678dbeaad29e538b8a6d2d00fa496/pre-commit
	chmod +x .git/hooks/pre-commit

############
# Commands #
############

run-phpstan:
	php bin/phpstan analyse Action Api.php SkeletonGatewayFactory.php --level max --autoload-file=vendor/autoload.php

run-phpunit:
	php bin/phpunit

run-php-cs-fixer:
	php bin/php-cs-fixer fix

run-php-cs-fixer@travis:
	php bin/php-cs-fixer fix -v --diff --dry-run
