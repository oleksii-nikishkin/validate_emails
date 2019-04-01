<?php
namespace OleksiiNikishkin\ValidateEmails;


use OleksiiNikishkin\ValidateEmails\Exceptions\EmailValidateException;

class EmailValidator {
    private $_domainPattern = '[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?';
    private $_namePattern = '[A-Za-z0-9._%+-]+';
    private $_emailPattern;
    private $_emailErrors = [];
    private $_blacklistedNames = [];
    private $_blacklistedDomains = [];
    private $_emails = [];

    public function __construct(array $emails, array $blacklistedNames = [], array $blacklistedDomains = []) {
        $this->_emails = $emails;
        $this->_blacklistedNames = $blacklistedNames;
        $this->_blacklistedDomains = $blacklistedDomains;

        $this->_emailPattern = "/^" . $this->_namePattern . "@" . $this->_domainPattern . "$/";
    }

    public function validateDomains() {
        $invalidDomains = [];

        foreach ($this->_blacklistedDomains as $key => $domain) {
            if (!preg_match("/^" . $this->_domainPattern . "$/", $domain))
                $invalidDomains[] = $domain;
        }
        if (count($invalidDomains))
            return $invalidDomains;

        return true;
    }

    public function validateNames() {
        $invalidNames = [];

        foreach ($this->_blacklistedNames as $key => $name) {
            if (!preg_match("/^" . $this->_namePattern . "$/", $name))
                $invalidNames[] = $name;
        }
        if (count($invalidNames))
            return $invalidNames;

        return true;
    }

    public function validateEmails() {
        $validEmails = $invalidEmails = [];

        foreach ($this->_emails as $email) {
            $emailErrors = [];

            if (!preg_match($this->_emailPattern, $email)) {
                $invalidEmails[$email] = ["email is invalid"];
                continue;
            }

            list($name, $domain) = explode('@', $email);

            if (!empty($blacklistedNames) && self::inArrayCI($name, $blacklistedNames))
                $emailErrors[] = "the local part of the email address is blacklisted";

            if (!empty($blacklistedDomains) && self::inArrayCI($domain, $blacklistedDomains))
                $emailErrors[] = "the domain of the email address is blacklisted";

            if (preg_match('/(.)\1{2}/', $email))
                $emailErrors[] = "the email has 3 or more same characters consecutively!";

            if (empty($emailErrors))
                $validEmails[] = $email;
            else
                $invalidEmails[$email] = $emailErrors;
        }

        return [
            'valid_emails' => $validEmails,
            'invalid_emails' => $invalidEmails
        ];
    }
}