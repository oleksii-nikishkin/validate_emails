<?php

namespace unit\ValidateEmails;

use OleksiiNikishkin\ValidateEmails\EmailValidator;
use OleksiiNikishkin\ValidateEmails\Errors;
use PHPUnit\Framework\TestCase;

class EmailValidatorTest extends TestCase
{
    private const BLACKLISTED_NAMES = ['Trump', 'vova'];
    private const BLACKLISTED_DOMAINS = ['whitehouse.gov', 'hackers.com'];

    public function validNamesProvider(): array
    {
        return [
            ['user'],
            ['user-1'],
            ['user.2'],
            ['user+main'],
        ];
    }

    public function invalidPatternNamesProvider(): array
    {
        return [
            ['$-sign'],
            ['my name'],
            [''],
        ];
    }

    public function blacklistedNamesProvider(): array
    {
        return [
            ['trump'],
            ['VOVA'],
        ];
    }

    /**
     * @dataProvider validNamesProvider
     */
    public function testIsNamePatternPassedReturnsTrue(string $name): void
    {
        $emailValidator = new EmailValidator(self::BLACKLISTED_NAMES, self::BLACKLISTED_DOMAINS);

        $this->assertTrue($emailValidator->isNamePatternPassed($name));
    }

    /**
     * @dataProvider validNamesProvider
     */
    public function testIsNameBlacklistedReturnsFalse(string $name): void
    {
        $emailValidator = new EmailValidator(self::BLACKLISTED_NAMES, self::BLACKLISTED_DOMAINS);

        $this->assertFalse($emailValidator->isNameBlacklisted($name));
    }

    /**
     * @dataProvider invalidPatternNamesProvider
     */
    public function testIsNamePatternPassedReturnsFalse(string $name): void
    {
        $emailValidator = new EmailValidator(self::BLACKLISTED_NAMES, self::BLACKLISTED_DOMAINS);

        $this->assertFalse($emailValidator->isNamePatternPassed($name));
    }

    /**
     * @dataProvider blacklistedNamesProvider
     */
    public function testIsNameBlacklistedReturnsTrue(string $name): void
    {
        $emailValidator = new EmailValidator(self::BLACKLISTED_NAMES, self::BLACKLISTED_DOMAINS);

        $this->assertTrue($emailValidator->isNameBlacklisted($name));
    }

    public function validDomainsProvider(): array
    {
        return [
            ['bing.com'],
            ['Email-Website.de'],
        ];
    }

    public function invalidPatternDomainsProvider(): array
    {
        return [
            ['google .com'],
            ['new+website'],
            ['localhost'],
            [''],
        ];
    }

    public function blacklistedDomainsProvider(): array
    {
        return [
            ['Whitehouse.gov'],
            ['HACKERS.COM'],
        ];
    }

    /**
     * @dataProvider validDomainsProvider
     */
    public function testIsDomainPatternPassedReturnsTrue(string $domain): void
    {
        $emailValidator = new EmailValidator(self::BLACKLISTED_NAMES, self::BLACKLISTED_DOMAINS);

        $this->assertTrue($emailValidator->isDomainPatternPassed($domain));
    }

    /**
     * @dataProvider validDomainsProvider
     */
    public function testIsDomainBlacklistedReturnsFalse(string $domain): void
    {
        $emailValidator = new EmailValidator(self::BLACKLISTED_NAMES, self::BLACKLISTED_DOMAINS);

        $this->assertFalse($emailValidator->isDomainBlacklisted($domain));
    }

    /**
     * @dataProvider invalidPatternDomainsProvider
     */
    public function testIsDomainPatternPassedReturnsFalse(string $domain): void
    {
        $emailValidator = new EmailValidator(self::BLACKLISTED_NAMES, self::BLACKLISTED_DOMAINS);

        $this->assertFalse($emailValidator->isDomainPatternPassed($domain));
    }

    /**
     * @dataProvider blacklistedDomainsProvider
     */
    public function testIsDomainBlacklistedReturnsTrue(string $domain): void
    {
        $emailValidator = new EmailValidator(self::BLACKLISTED_NAMES, self::BLACKLISTED_DOMAINS);

        $this->assertTrue($emailValidator->isDomainBlacklisted($domain));
    }

    public function namesProvider(): array
    {
        return [
            ['user+main', null],
            ['$-sign', Errors::NAME_PATTERN_FAILED],
            ['trump', Errors::NAME_IS_BLACKLISTED],
        ];
    }

    /**
     * @dataProvider namesProvider
     */
    public function testValidateName(string $name, ?string $expectedReturnedValue): void
    {
        $emailValidator = new EmailValidator(self::BLACKLISTED_NAMES, self::BLACKLISTED_DOMAINS);

        $this->assertEquals($expectedReturnedValue, $emailValidator->validateName($name));
    }

    public function domainsProvider(): array
    {
        return [
            ['bing.com', null],
            ['localhost', Errors::DOMAIN_PATTERN_FAILED],
            ['hackers.com', Errors::DOMAIN_IS_BLACKLISTED],
        ];
    }

    /**
     * @dataProvider domainsProvider
     */
    public function testValidateDomain(string $domain, ?string $expectedReturnedValue): void
    {
        $emailValidator = new EmailValidator(self::BLACKLISTED_NAMES, self::BLACKLISTED_DOMAINS);

        $this->assertEquals($expectedReturnedValue, $emailValidator->validateDomain($domain));
    }


    public function emailsProvider(): array
    {
        return [
            'valid_email' => ['good@email.com', null],
            'less_than_2_parts' => ['word', [Errors::EMAIL_PATTERN_FAILED]],
            'more_than_2_parts' => ['email@is@invalid', [Errors::EMAIL_PATTERN_FAILED]],
            'failed_name_only' => ['&-sign@mail.com', [Errors::NAME_PATTERN_FAILED]],
            'failed_domain_only' => ['email@google. com', [Errors::DOMAIN_PATTERN_FAILED]],
            'failed_name_and_domain' => [
                'trump@whitehouse.gov',
                [Errors::NAME_IS_BLACKLISTED, Errors::DOMAIN_IS_BLACKLISTED],
            ],
        ];
    }

    /**
     * @dataProvider emailsProvider
     */
    public function testValidateEmails(string $email, ?array $expectedReturnedValue): void
    {
        $emailValidator = new EmailValidator(self::BLACKLISTED_NAMES, self::BLACKLISTED_DOMAINS);

        $this->assertEquals($expectedReturnedValue, $emailValidator->validateEmail($email));
    }
}