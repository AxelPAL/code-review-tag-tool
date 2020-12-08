.PHONY: tests

phpstan:
	php8.0 ./vendor/bin/phpstan analyze .

tests:
	php8.0 ./vendor/bin/phpunit