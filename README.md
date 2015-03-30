# trader
Sample project for test

I used the Silex PHP framework to create a simple api for trade stats/actions
```
Post endpoint: /api/v1/trade/new

GUI endpoint: /
```
Post endpoint uses HMAC authentication, which can be demo'd using the form on the GUI.
In real world environments there would be DB interaction for getting secrets and checking public keys

The endpoint expects a 'message' key along with the hmac auth fields.
```
[
  'message' => [
    'userId'              => '1',
    'currencyFrom'        => 'EUR,
    'currencyTo'          => 'GBP',
    'amountSell'          => 500,
    'amountBuy'           => 800,
    'rate'                => 1.6,
    'timePlaced'          => 2014-02-15 20:15:10,
    'originatingCountry'  => 'GB'
  ]
]
```
HMAC fields would look similar to.
```
[
  'auth_version'    => '1.0',
  'auth_key'        => 'test',
  'auth_timestamp'  => 1427724158,
  'auth_signature'  => 'ec9783df15062176a54afb3e7e10cfc95d8b0d179b46367bdc3e2dbb0f183329'
]
```
