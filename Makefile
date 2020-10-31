.PHONY: tests

phpstan:
	php7.4 ./vendor/bin/phpstan analyze .

tests:
	php7.4 ./vendor/bin/phpunit