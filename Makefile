.PHONY: tests
container=app

up:
	docker-compose up -d

up-with-db:
	docker-compose --profile=db up -d

build:
	docker-compose rm -vsf
	docker-compose down -v --remove-orphans
	docker-compose build

stop:
	docker-compose stop

down:
	docker-compose down

install:
	docker-compose run --rm --entrypoint "" ${container} composer install

update:
	docker-compose run --rm --entrypoint "" ${container} composer update

require:
	docker-compose run --rm --entrypoint "" ${container} composer require

require-dev:
	docker-compose run --rm --entrypoint "" ${container} composer require --dev

enter:
	docker-compose exec ${container} bash

tests:
	docker-compose run --rm --entrypoint "" ${container} ./artisan test

stan:
	docker-compose run --rm --entrypoint "" ${container} ./vendor/bin/phpstan analyse .

tail-logs:
	docker-compose logs -f ${container}

hook-pre-commit:
	docker-compose run --rm --entrypoint "" ${container} ./git-hooks/pre-commit.sh

hook-pre-push:
	docker-compose run --rm --entrypoint "" ${container} ./git-hooks/pre-push.sh