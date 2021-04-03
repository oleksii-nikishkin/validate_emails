<?php declare(strict_types = 1);

namespace OleksiiNikishkin\ValidateEmails;

class EmailValidator
{
    private const NAME_PATTERN = "[A-Za-z0-9._%+-]+";
    private const DOMAIN_PATTERN = "[A-Za-z0-9]+([\-\.]{1}[A-Za-z0-9]+)*\.[A-Za-z]{2,5}(:[0-9]{1,5})?(\/.*)?";

    private array $blacklistedNames;
    private array $blacklistedDomains;
    private string $namePattern;
    private string $domainPattern;

    public function __construct(
        array $blacklistedNames = [],
        array $blacklistedDomains = [],
        string $namePattern = self::NAME_PATTERN,
        string $domainPattern = self::DOMAIN_PATTERN
    ) {
        $this->blacklistedNames = $blacklistedNames;
        $this->blacklistedDomains = $blacklistedDomains;
        $this->namePattern = $namePattern;
        $this->domainPattern = $domainPattern;
    }

    public function isNamePatternPassed(string $name): bool
    {
        return (bool)preg_match("/^" . $this->namePattern . "$/", $name);
    }

    public function isDomainPatternPassed(string $domain): bool
    {
        return (bool)preg_match("/^" . $this->domainPattern . "$/", $domain);
    }

    public function isNameBlacklisted(string $name): bool
    {
        return in_array_ci($name, $this->blacklistedNames);
    }

    public function isDomainBlacklisted(string $domain): bool
    {
        return in_array_ci($domain, $this->blacklistedDomains);
    }

    /*
     * Returns a string from OleksiiNikishkin\Errors or null if the given name is valid.
     */
    public function validateName(string $name): ?string
    {
        if (!$this->isNamePatternPassed($name)) {
            return Errors::NAME_PATTERN_FAILED;
        }

        if ($this->isNameBlacklisted($name)) {
            return Errors::NAME_IS_BLACKLISTED;
        }

        return null;
    }

    /*
     * Returns a string from OleksiiNikishkin\Errors or null if the given domain is valid.
     */
    public function validateDomain(string $domain): ?string
    {
        if (!$this->isDomainPatternPassed($domain)) {
            return Errors::DOMAIN_PATTERN_FAILED;
        }

        if ($this->isDomainBlacklisted($domain)) {
            return Errors::DOMAIN_IS_BLACKLISTED;
        }

        return null;
    }

    /*
     * Returns an array of validation errors defined in OleksiiNikishkin\Errors or null if the given email is valid
     */
    public function validateEmail(string $email): ?array
    {
        $parts = explode("@", $email);
        if (!$parts || count($parts) !== 2) {
            return [Errors::EMAIL_PATTERN_FAILED];
        }

        $errors = [];
        $name = $parts[0];
        $domain = $parts[1];

        if ($nameError = $this->validateName($name)) {
            $errors[] = $nameError;
        }

        if ($domainError = $this->validateDomain($domain)) {
            $errors[] = $domainError;
        }

        return !empty($errors) ? $errors : null;
    }
}