# Preparing your ZF1 application for Zymfony

Moving to Symfony from ZF1 is not an easy process, and Zymfony will not be able to provide a drop-in solution. The goal of Zymfony is to allow the ease of the migration. Even with that, there are some prerequisites to let Zymfony function. Below we hope to provide some pointers and explaining our own journey.

## Moving to Composer

You will need to start with moving ZF1 over to Composer, instead of in the `library/` folder. This is required for proper autoloading.
We had some changes we made in ZF1 source code, which we had to move out or remove outright. Some changes that are absolutely required, you might have to make a hacky solution for. This is at least somewhat okay, as it's temporary for your migration.

## Moving to PHP 7.1+

You will also need to move to PHP 7.1+. We haven't found any incompatibilities with the latest ZF1 version (1.12), but your code might be incompatible to some degree. Take note of the PHP docs for incompatible changes, and use your IDE to help you out.
ZF 1.12 does have incompatibilities with PHP 7.2. We might eventually fork 1.12 to fix this.

