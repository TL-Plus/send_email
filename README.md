# send email or telegram message

Export excel file from server phpmyadmin and send email to recipients

Update:
You can choose to send the excel file via email or telegram

## insert database with excel file

Insert excel file into diginext billing server.

The server being used is diginext billing server with BlackList database.

## report call spam

Perform data query and export spam call numbers to excel file of diginext billing server.

The server being used is diginext billing server with Report database.

## warning with day and status

Perform data queries and export data with dates from 17 to 19 days and statuses 2 and 3 to the excel files.

The server being used is 198 billing server with VoiceReport database.

## warning with liabilities

Perform data queries and export liability contract data to excel files

The server being used is 198 billing server with VoiceReport database.

# Setup install package
```
composer require phpoffice/phpspreadsheet
composer require phpmailer/phpmailer
composer require asimlqt/php-google-spreadsheet-client
composer require google/apiclient
composer require phpseclib/phpseclib
composer require google/auth
composer require hybridauth/hybridauth
composer require telegram-bot/api
```