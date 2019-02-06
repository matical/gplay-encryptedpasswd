# GPEncryptedPasswd
Handles the creation of the "EncryptedPasswd" field, required for *logging in* to the Play Store's APIs.

## Install
`composer require ksmz/gplay-encryptedpasswd`

## Usage
```php
use ksmz\GPEncryptedPasswd\Encrypt;
use ParagonIE\HiddenString\HiddenString;

$gmailAddress = getenv('GOOGLE_LOGIN');
$gmailPassword = getenv('GOOGLE_PASSWORD');

$encrypted = new Encrypt($gmailAddress, new HiddenString($gmailPassword));
// AFcb4KS4dW-knb6SS_VaeZRZVqSEZK9ExPO_zt8mgDISQBuEsLYwKzO9JYzar7m2Zjy5bih4MYkb0W46NMgYy_k0ckdehb_bmxrIjlTHHyPxMHH32P8GOlNjoSkt7QgmGEi5JbmoKFfYTo-9FNOioI-7W2l-e6zrw2OhOfSlYjvp2oHM1Q==
```

## Credits/Other Libraries
https://github.com/dweinstein/node-google-play/blob/e1d66c2f7f65afd8c373e65be59848de8b660be3/lib/login.js
https://github.com/subtletech/google_play_store_password_encrypter
https://github.com/yeriomin/play-store-api/blob/040d1f282fa165f2568e8b64a053a9cbf19aa615/src/main/java/com/github/yeriomin/playstoreapi/PasswordEncrypter.java
