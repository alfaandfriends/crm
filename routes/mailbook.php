<?php

use App\Mail\ContractCreated;
use App\Mail\MailbookMail;
use Xammie\Mailbook\Facades\Mailbook;

Mailbook::add(MailbookMail::class);
// Mailbook::add(function (){
//     return new ContractCreated('sample/sign_url', 'Sample Client');
// });
