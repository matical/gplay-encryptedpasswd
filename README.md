# GPEncryptedPasswd
Handles the creation of the "EncryptedPasswd" field, required by Play Store's internal APIs. This library is does not perform any additional tasks besides generating this field.

## Install
`composer require ksmz/gplay-encryptedpasswd`

## Usage
```php
use ksmz\GPEncryptedPasswd\Encrypt;
use ParagonIE\HiddenString\HiddenString;

// Fetch your credentials from somewhere else.
$gmailAddress = getenv('GOOGLE_EMAIL');
$gmailPassword = getenv('GOOGLE_PASSWORD');

// HiddenString hides sensitive information from backtraces.
// Alternatively, this can be eventually be replaced by https://wiki.php.net/rfc/redact_parameters_in_back_traces in PHP 8.2
$encryptor = new Encrypt($gmailAddress, new HiddenString($gmailPassword));
echo $encryptor->encrypt();
// AFcb4KS4dW-knb6SS_VaeZRZVqSEZK9ExPO_zt8mgDISQBuEsLYwKzO9JYzar7m2Zjy5bih4MYkb0W46NMgYy_k0ckdehb_bmxrIjlTHHyPxMHH32P8GOlNjoSkt7QgmGEi5JbmoKFfYTo-9FNOioI-7W2l-e6zrw2OhOfSlYjvp2oHM1Q==
```

## Credits/Other Libraries
https://web.archive.org/web/20150814054004/http://codedigging.com/blog/2014-06-09-about-encryptedpasswd/

https://github.com/dweinstein/node-google-play/blob/e1d66c2f7f65afd8c373e65be59848de8b660be3/lib/login.js

https://github.com/subtletech/google_play_store_password_encrypter

https://github.com/yeriomin/play-store-api/blob/040d1f282fa165f2568e8b64a053a9cbf19aa615/src/main/java/com/github/yeriomin/playstoreapi/PasswordEncrypter.java
