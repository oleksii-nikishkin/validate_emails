<?php
namespace ValidateEmails;


use OleksiiNikishkin\ValidateEmails\Exceptions\EmailValidateException;

class EmailValidator {
    private $_domainPattern = '[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?';
    private $_namePattern = '[A-Za-z0-9._%+-]+';
    private $_emailPattern;
    private $_errors = [];
    private $_blacklistedNames = [];
    private $_blacklistedDomains = [];
    private $_emails = [];

    public function __construct(array $blacklistedNames, array $blacklistedDomains, array $emails) {
        $this->_blacklistedNames = $blacklistedNames;
        $this->_blacklistedDomains = $blacklistedDomains;
        $this->_emails = $emails;

        $this->_emailPattern = "/^" . $this->_namePattern . "@" . $this->_domainPattern . "$/";

    }

    public function validateDomains() {
        $invalidDomains = [];

        foreach ($this->_blacklistedDomains as $key => $domain) {
            if (!preg_match("/^" . $this->_domainPattern . "$/", $domain))
                $invalidDomains[] = $domain;
        }
        if (count($invalidDomains))
            throw new EmailValidateException('Invalid domain(s) given.', 400, null, [
                'invalid_domains' => $invalidDomains
            ]);

        return true;
    }

    public function validateNames() {
        $invalidNames = [];

        foreach ($this->_blacklistedNames as $key => $name) {
            if (!preg_match("/^" . $this->_namePattern . "$/", $name))
                $invalidNames[] = $name;
        }
        if (count($invalidNames))
            throw new EmailValidateException('Invalid name(s) given.', 400, null, [
                'invalid_names' => $invalidNames
            ]);

        return true;
    }
}