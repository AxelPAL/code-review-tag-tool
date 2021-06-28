# Code Review Tool

![CI](https://github.com/AxelPAL/code-review-tag-tool/actions/workflows/main.yml/badge.svg)

This instrument is aimed to count specific tags in Pull Request's comments at BitBucket and to make a report with this data.

## Initialize the project

In order to make the project operative you should run:
```php
./artisan db:migrate
./artisan db:seed
```

Seeding the database does some things:

* Initialize some specific permissions to get access to some pages
* Set necessary settings

## XDebug
For using XDebug you should add arg to your compose-file (to app service):
```yaml
    build:
      args:
        INSTALL_XDEBUG: "true"
```