# Send Email or Telegram Message

Export excel file from server phpmyadmin and send email to recipients

## Update
You can choose to send the excel file via email or telegram

### Function Send Email and Telegram Message
#### Call the following function to send email
`function sendEmailForDays($sql, $header, $attachment, $subject, $recipients)
`

#### Call the following function to send a telegram message
`function sendTelegramMessage($sql, $header, $filename, $textMessage, $chatId)
`

## insert database with excel file

Insert excel file into diginext billing server.

The server being used is diginext billing server with BlackList database.

## report call spam

Perform data query and export spam call numbers to excel file of diginext billing server.

The server being used is diginext billing server with Report database.

## warning with day and status

Perform data queries and export data with dates from 17 to 19 days and statuses 2 and 3 to the excel files.

The server being used is digitel billing server with VoiceReport database.

## warning with liabilities

Perform data queries and export liability contract data to excel files

The server being used is digitel billing server with VoiceReport database.

# Setup install package file root
```
composer require phpoffice/phpspreadsheet
composer require phpmailer/phpmailer
composer require asimlqt/php-google-spreadsheet-client
composer require google/apiclient
composer require phpseclib/phpseclib
composer require google/auth
composer require hybridauth/hybridauth
composer require telegram-bot/api
composer require vlucas/phpdotenv
```

## Copy the .env.example file locally
`cp .env.example .env`

### if you want to change the database connection you need to change the $result variable in import_excel.php file with the corresponding database connection
#### Database connection server billing default
```
$result = connectAndQueryDatabase($sql, $_ENV['DB_HOSTNAME'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $_ENV['DB_DATABASE']);
DB_HOSTNAME=
DB_USERNAME=
DB_PASSWORD=
DB_DATABASE=
```

#### Database connection server billing DIGINEXT
```
$result = connectAndQueryDatabase($sql, $_ENV['DB_HOSTNAME_DIGINEXT'], $_ENV['DB_USERNAME_DIGINEXT'], $_ENV['DB_PASSWORD_DIGINEXT'], $_ENV['DB_DATABASE_DIGINEXT']);
DB_HOSTNAME_DIGINEXT=
DB_USERNAME_DIGINEXT=
DB_PASSWORD_DIGINEXT=
DB_DATABASE_DIGINEXT=
```

#### Database connection server billing DIGITEL
```
$result = connectAndQueryDatabase($sql, $_ENV['DB_HOSTNAME_DIGITEL'], $_ENV['DB_USERNAME_DIGITEL'], $_ENV['DB_PASSWORD_DIGITEL'], $_ENV['DB_DATABASE_DIGITEL']);
DB_HOSTNAME_DIGITEL=
DB_USERNAME_DIGITEL=
DB_PASSWORD_DIGITEL=
DB_DATABASE_DIGITEL=
```