# SAML Demo

This repository contains a demonstration of how LightSAML can be used in Laravel.

From the [start page](http://localhost:8000) you can add a test user to the session and begin SP and 
IdP initiated logins.

## Quick start

Clone the repository and install the Composer dependencies.

Generate two certificate / key pairs, one for the SP (iamproperty) and one for the IdP (The Guild).

```shell
$ openssl req -new -x509 -days 365 -nodes -sha256 -out storage/app/saml/credentials/sp.crt -keyout storage/app/saml/credentials/sp.pem
$ openssl req -new -x509 -days 365 -nodes -sha256 -out storage/app/saml/credentials/idp.crt -keyout storage/app/saml/credentials/idp.pem
```

Run the demo using `php artisan serve`.

## Useful links

- [The Beer Drinker’s Guide to SAML](https://duo.com/blog/the-beer-drinkers-guide-to-saml)
