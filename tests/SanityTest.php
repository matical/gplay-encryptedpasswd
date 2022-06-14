<?php

use ksmz\GPEncryptedPasswd\Encrypt;
use ParagonIE\HiddenString\HiddenString;

it('works', function () {
    $gmailAddress = "NOT-AN-ADDRESS";
    $gmailPassword = "NOT-A-PASSWORD";

    $result = (new Encrypt($gmailAddress, new HiddenString($gmailPassword)))->encrypt();
    expect($result)->not->toBeEmpty();
});
