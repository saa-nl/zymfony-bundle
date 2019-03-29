# Request bridge component

The request bridge provies an easy way to go between Symfony requests and Zend requests, which may be useful in your migration.

## Usage

Simply call the request bridge statically:

- From ZF1 to Symfony request: `SAA\ZymfonyBundle\Request\RequestBridge::toSymfonyRequest`
- From Symfony to ZF1 request: `SAA\ZymfonyBundle\Request\RequestBridge::fromSymfonyRequest`