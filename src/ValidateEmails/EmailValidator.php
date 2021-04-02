<?php declare(strict_types = 1);

namespace OleksiiNikishkin\ValidateEmails;

class EmailValidator
{
    private string $_domainPattern = '[A-Za-z0-9]+([\-\.]{1}[A-Za-z0-9]+)*\.[A-Za-z]{2,5}(:[0-9]{1,5})?(\/.*)?';
    private string $_namePattern = '[A-Za-z0-9._%+-]+';
    private string $_emailPattern;
    private array $_blacklistedNames = [];
    private array $_blacklistedDomains = [];
    private array $_emails = [];

    public function __construct(array $emails, array $blacklistedNames = [], array $blacklistedDomains = [])
    {
        $this->_emails = $emails;
        $this->_blacklistedNames = $blacklistedNames;
        $this->_blacklistedDomains = $blacklistedDomains;

        $this->_emailPattern = "/^" . $this->_namePattern . "@" . $this->_domainPattern . "$/";
    }


    /*
     * Validate domains.
     *
     * Returns true if validation is passed, otherwise returns an array of incorrect domains
     */
    public function validateDomains(): array
    {
        $invalidDomains = [];

        foreach ($this->_blacklistedDomains as $key => $domain) {
            if (!preg_match("/^" . $this->_domainPattern . "$/", $domain))
                $invalidDomains[] = $domain;
        }

        return $invalidDomains;
    }

    /*
     * Validate names.
     *
     * Returns true if validation is passed, otherwise returns an array of incorrect names
     */
    public function validateNames(): array
    {
        $invalidNames = [];

        foreach ($this->_blacklistedNames as $key => $name) {
            if (!preg_match("/^" . $this->_namePattern . "$/", $name))
                $invalidNames[] = $name;
        }

        return $invalidNames;
    }


    /*
     * Returns an array with separated emails to valid and non-valid
     */
    public function validateEmails(): array
    {
        $validEmails = $invalidEmails = [];

        foreach ($this->_emails as $email) {
            $emailErrors = [];

            if (!preg_match($this->_emailPattern, $email)) {
                $invalidEmails[$email] = ["email is invalid"];
                continue;
            }

            list($name, $domain) = explode('@', $email);

            if (!empty($this->_blacklistedNames) && in_array_ci($name, $this->_blacklistedNames))
                $emailErrors[] = "the local part of the email address is blacklisted";

            if (!empty($this->_blacklistedDomains) && in_array_ci($domain, $this->_blacklistedDomains))
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