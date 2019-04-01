### Installation

```sh
$ composer require oleksii-nikishkin/validate-emails
```

### Usage
```php
use OleksiiNikishkin\ValidateEmails\EmailValidator;
//...

// Pass a list of emails and, optionally names and domains that are blacklisted.
$emailValidator = new EmailValidator($emails, $blacklistedNames, $blacklistedDomains);

// You can validate blacklisted names and domains.
// These functions return true if validation is passed or an array of non-valid items.
$emailValidator->validateNames();
$emailValidator->validateDomains();

// Validate emails.
// Returns valid and non-valid emails with explanations.
$emailValidator->validateEmails();

//...
```