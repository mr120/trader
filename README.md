# trader
Sample project for test

I used the Silex PHP framework to create a simple api for trade stats/actions
```
Post endpoint: /api/v1/trade/new

GUI endpoint: /
```
Post endpoint uses HMAC authentication, which can be demo'd using the form on the GUI.
In real world environments there would be DB interaction for getting secrets and checking public keys

The endpoint expects the 'message' along with the hmac auth fields.
```
[
    'userId'              => '1',
    'currencyFrom'        => 'EUR,
    'currencyTo'          => 'GBP',
    'amountSell'          => 500,
    'amountBuy'           => 800,
    'rate'                => 1.6,
    'timePlaced'          => 2014-02-15 20:15:10,
    'originatingCountry'  => 'GB'
    ...
    
    'auth_version'    => '1.0',
    'auth_key'        => 'test',
    'auth_timestamp'  => 1427724158,
    'auth_signature'  => 'ec9783df15062176a54afb3e7e10cfc95d8b0d179b46367bdc3e2dbb0f183329'
]
```

The secret key:   ```"e249c439ed7697df2a4b045d97d4b9b7e1854c3ff8dd668c779013653913572e"```   
The public key:   ```"trader_account"```  
The Hmac Authentication files are at: Trader/Auth/Hmac

The authentication requires:  
```
$message = [];
$token = new Token($key, $secret);
$request = new Request('POST', '/api/v1/trade/new', $message);
$signed_request = $request->sign($token);
$params = array_merge($signed_request, $message);

```
Then using perhaps curl
```
...
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
...
```



Use create_db.sql to generate the required db.
