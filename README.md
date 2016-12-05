"Pouce d'Or" RESTful service (Symfony)
======================================

Repository for the symfony version of restful service for Pouce d'Or Website : www.poucedor.fr

# Major bundles used

* Symfony 3.1
* Doctrine
* phpUnit
* FOSUserBundle
* FOSOauthServerBundle
* FOSRestBundle
* JMSSerializerBundle
* NelmioApiDocBundle

# To Do

## For Version 1.0

* Add security layer with user roles
* Capifony (Deployement Tool)
* Deal with email service not to be marked as spam

## For Version 2.0

* Add Facebook and Google OAuth2


# How to use it

## Setup

Get vendors via composer ("composer update"). https://getcomposer.org/download/

Run Apache and mySQL with the test database. You can use MAMP (OSX) or WAMP (Windows).

## Access to API documentation

Access with the following address: localhost:8888/web/app_dev.php/api/doc

## Run tests

Install phpunit on your machine: https://phpunit.de/getting-started.html  

Run tests with the command: phpunit

# What you have to know on the project

## Security

We use OAuth2 authentification: password grant

## The project

It's a personnal project done after work hours for the Pouce d'Or contest.


# License

This website have been created and is owned by Olivier NGUYEN QUOC.

**No rights permission given including rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software without an authorisation of the owner.**

All technologies used are under MIT license or BSD license.

A Symfony project created on September 19, 2016, 2:46 pm.
