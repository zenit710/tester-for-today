FROM php:7.2.28-cli-alpine as RUNNER
FROM composer as composer

WORKDIR /var/app/tester

CMD tail -f /dev/null