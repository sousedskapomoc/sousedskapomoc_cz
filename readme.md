# SousedskaPomoc.cz

![Composer installation, Code Style check](https://github.com/sousedskapomoc/sousedskapomoc_cz/workflows/Composer%20installation,%20Code%20Style%20check/badge.svg?branch=master)

------

## Description

Web application with volunteer registration forms - dividing them into multiple roles - developed overnight during COVID-19 crisis and used in Czech Republic.

Using this application significantly helps organizing volunteers in cities and towns..

This web application is built for free and open sourced to anybody out there - feel free to start a fork and start help in your country.

All activities connected to this repository are and will be done for free!

## Technologies used

 - PHP 7.4
 - MySQL 8
 - Docker
 - RabbitMQ

## Development

We strictly follow https://danielkummer.github.io/git-flow-cheatsheet/ for releases.

## Deployment

We are using our own pre-built Docker image which consits of nginx, php-fpm, supervisord services - our apps are built on top of that image.

## How to start local development

Docker local development
========================

Docker deployment process
=========================
