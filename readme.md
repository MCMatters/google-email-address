## Google Mail Address
Helps you to identify is belong email to Google Mail Service or GSuite. 

### Installation

```bash
composer require mcmatters/google-email-address
```

### Usage

```php
<?php

declare(strict_types = 1);

use McMatters\GoogleEmailAddress\GoogleEmailAddress;

require 'vendor/autoload.php';

$manager = new GoogleEmailAddress();

$manager->isGoogleEmailAddress('dima.matters@gmail.com'); // returns "true"
$manager->isGSuiteEmailAddress('d.borzyonok@amgrade.org'); // returns "true"
$manager->normalize('dima.matters@gmail.com'); // returns "dimamatters@gmail.com"
```
