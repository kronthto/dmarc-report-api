# DMARC Report API - Manage rua reports

[![Software License][ico-license]](LICENSE.md)
[![Latest Stable Version][ico-githubversion]][link-releases]
[![Build Status][ico-build]][link-build]

This is an HTTP API to [solarissmoke/php-dmarc](https://github.com/solarissmoke/php-dmarc).

## Features

* `/submit` endpoint that passes reports to the parser package
* CORS

## Install

``` bash
$ composer install (--no-dev -o)
$ cp .env.example .env
```
* Adjust *.env* to your environment (database, ...)
* Import the [database schema][link-tables]

## Usage

### `POST /submit`
* Accepts `multipart/form-data` with an array of reports (`reports[]`)

### Auth

If necessary in your environment, you will have to set up authentication yourself (e.G. in your webservers config), as none is implemented in this project.

## Changelog

Please see the [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see the [License File](LICENSE.md) for more information.

[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-githubversion]: https://badge.fury.io/gh/kronthto%2Fdmarc-report-api.svg
[ico-build]: https://travis-ci.org/kronthto/dmarc-report-api.svg?branch=master

[link-releases]: https://github.com/kronthto/dmarc-report-api/releases
[link-contributors]: ../../contributors
[link-build]: https://travis-ci.org/kronthto/dmarc-report-api
[link-tables]: https://github.com/solarissmoke/php-dmarc/blob/v2.0.1/tables.sql
