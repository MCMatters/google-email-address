<?php

declare(strict_types=1);

namespace McMatters\GoogleEmailAddress\Tests;

use McMatters\GoogleEmailAddress\GoogleEmailAddress;
use PHPUnit\Framework\TestCase;

use const false, null, true;

/**
 * Class GoogleEmailAddressTest
 *
 * @package McMatters\GoogleEmailAddress\Tests
 */
class GoogleEmailAddressTest extends TestCase
{
    /**
     * @var array
     */
    protected $emails = [
        'd.borzyonok@amgrade.com' => [
            'is_google' => false,
            'mx' => false,
            'normalized' => 'd.borzyonok@amgrade.com',
        ],
        'd.borzyonok@amgrade.org' => [
            'is_google' => true,
            'mx' => true,
            'normalized' => 'd.borzyonok@amgrade.org',
        ],
        'd.borzyonok+foobar@amgrade.com' => [
            'is_google' => false,
            'mx' => false,
            'normalized' => 'd.borzyonok+foobar@amgrade.com',
        ],
        'd.borzyonok+foobar@amgrade.org' => [
            'is_google' => true,
            'mx' => true,
            'normalized' => 'd.borzyonok@amgrade.org',
        ],
        'd.borzyonok+1@amgrade.com' => [
            'is_google' => false,
            'mx' => false,
            'normalized' => 'd.borzyonok+1@amgrade.com',
        ],
        'd.borzyonok+1@amgrade.org' => [
            'is_google' => true,
            'mx' => true,
            'normalized' => 'd.borzyonok@amgrade.org',
        ],
        'dima.matters@gmail.com' => [
            'is_google' => true,
            'mx' => false,
            'normalized' => 'dimamatters@gmail.com',
        ],
        'dima.matters+foobar@gmail.com' => [
            'is_google' => true,
            'mx' => false,
            'normalized' => 'dimamatters@gmail.com',
        ],
        'dima.matters+1@gmail.com' => [
            'is_google' => true,
            'mx' => false,
            'normalized' => 'dimamatters@gmail.com',
        ],
        'dima.mat.ters+foobar@gmail.com' => [
            'is_google' => true,
            'mx' => false,
            'normalized' => 'dimamatters@gmail.com',
        ],
        'dima.mat.ters+1@gmail.com' => [
            'is_google' => true,
            'mx' => false,
            'normalized' => 'dimamatters@gmail.com',
        ],
        '"foo@bar"@example.org' => [
            'is_google' => false,
            'mx' => false,
            'normalized' => '"foo@bar"@example.org',
        ],
        '"foo@bar"@gmail.com' => [
            'is_google' => true,
            'mx' => false,
            'normalized' => '"foo@bar"@gmail.com',
        ],
        '"foo@bar"@mcmatters.me' => [
            'is_google' => false,
            'mx' => false,
            'normalized' => '"foo@bar"@mcmatters.me',
        ],
        '"foo@bar+1"@gmail.com' => [
            'is_google' => true,
            'mx' => false,
            'normalized' => '"foo@bar"@gmail.com',
        ],
        '"foo@bar+1"@mcmatters.me' => [
            'is_google' => false,
            'mx' => false,
            'normalized' => '"foo@bar+1"@mcmatters.me',
        ],
    ];

    /**
     * @var \McMatters\GoogleEmailAddress\GoogleEmailAddress
     */
    protected $manager;

    /**
     * GoogleEmailAddressTest constructor.
     *
     * @param string|null $name
     * @param array $data
     * @param string $dataName
     */
    public function __construct(
        ?string $name = null,
        array $data = [],
        $dataName = ''
    ) {
        parent::__construct($name, $data, $dataName);

        $this->manager = new GoogleEmailAddress();
    }

    /**
     * @return void
     */
    public function testIsGoogleEmailAddress()
    {
        foreach ($this->emails as $email => $info) {
            self::assertEquals(
                $this->manager->isGoogleEmailAddress($email, $info['mx'] ?? false),
                $info['is_google'],
                "Email: {$email}"
            );
        }
    }

    /**
     * @return void
     */
    public function testNormalized()
    {
        foreach ($this->emails as $email => $info) {
            self::assertEquals($this->manager->normalize($email), $info['normalized']);
        }
    }
}
