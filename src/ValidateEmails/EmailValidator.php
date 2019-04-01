<?php
namespace OleksiiNikishkin\ValidateEmails;

class EmailValidator {
    private $_domainPattern = '[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?';
    private $_namePattern = '[A-Za-z0-9._%+-]+';
    private $_emailPattern;
    private $_blacklistedNames = [];
    private $_blacklistedDomains = [];
    private $_emails = [];

    /**
     * EmailValidator constructor.
     *
     * @param array $emails
     * @param array $blacklistedNames
     * @param array $blacklistedDomains
     */
    public function __construct(array $emails, array $blacklistedNames = [], array $blacklistedDomains = []) {
        $this->_emails = $emails;
        $this->_blacklistedNames = $blacklistedNames;
        $this->_blacklistedDomains = $blacklistedDomains;

        $this->_emailPattern = "/^" . $this->_namePattern . "@" . $this->_domainPattern . "$/";
    }


    /**
     * Validate domains.
     *
     * Returns true if validation is passed, otherwise returns an array of incorrect domains
     *
     * @return array|bool
     */
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

    /**
     * Validate names.
     *
     * Returns true if validation is passed, otherwise returns an array of incorrect names
     *
     * @return array|bool
     */
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


    /**
     * Returns an array with separated emails to valid and non-valid
     *
     * @return array
     */
    public function validateEmails() {
        $validEmails = $invalidEmails = [];

        foreach ($this->_emails as $email) {
            $emailErrors = [];

            if (!preg_match($this->_emailPattern, $email)) {
                $invalidEmails[$email] = ["email is invalid"];
                continue;
            }

            list($name, $domain) = explode('@', $email);

            if (!empty($blacklistedNames) && in_array_ci($name, $blacklistedNames))
                $emailErrors[] = "the local part of the email address is blacklisted";

            if (!empty($blacklistedDomains) && in_array_ci($domain, $blacklistedDomains))
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