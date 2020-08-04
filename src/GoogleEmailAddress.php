<?php

declare(strict_types=1);

namespace McMatters\GoogleEmailAddress;

use function array_map, count, explode, filter_var, getmxrr, implode, in_array,
    preg_match, preg_quote, str_replace, substr;

use const false, null, true, FILTER_VALIDATE_EMAIL;

/**
 * Class GoogleEmailAddress
 *
 * @package McMatters\GoogleEmailAddress
 */
class GoogleEmailAddress
{
    /**
     * @var array
     */
    protected $googleAddresses = [
        'gmail.com',
        'googlemail.com',
    ];

    /**
     * @param string $email
     * @param bool $checkMXRecords
     *
     * @return bool
     */
    public function isGoogleEmailAddress(
        string $email,
        bool $checkMXRecords = false
    ): bool {
        static $regex;

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        if (null === $regex) {
            $regex = implode('|', array_map(static function (string $host) {
                return preg_quote($host, '.');
            }, $this->googleAddresses));

            $regex = "/@({$regex})$/i";
        }

        if (preg_match($regex, $email)) {
            return true;
        }

        if (!$checkMXRecords) {
            return false;
        }

        return $this->checkMXRecords($email);
    }

    /**
     * @param string $email
     *
     * @return bool
     */
    public function isGSuiteEmailAddress(string $email): bool
    {
        return $this->isGoogleEmailAddress($email, true);
    }

    /**
     * @param string $email
     *
     * @return string|null
     */
    public function normalize(string $email): ?string
    {
        $mxRecords = false;

        if (!$this->isGoogleEmailAddress($email)) {
            $mxRecords = $this->checkMXRecords($email);

            if (!$mxRecords) {
                return $email;
            }
        }

        $parts = explode('@', $email);

        if (count($parts) < 2) {
            return null;
        }

        $hostKey = count($parts) - 1;
        $host = $parts[$hostKey];
        unset($parts[$hostKey]);

        $name = implode('@', $parts);
        $name = preg_replace_callback('/\+.*/', static function () use ($name) {
            $quotes = ['"', "'"];
            $firstSymbol = substr($name, 0, 1);

            if (in_array($firstSymbol, $quotes, true)) {
                return $firstSymbol;
            }

            return '';
        }, $name);

        if (!$mxRecords) {
            $name = str_replace('.', '', $name);
        }

        return "{$name}@{$host}";
    }

    /**
     * @param string $email
     *
     * @return bool
     */
    protected function checkMXRecords(string $email): bool
    {
        [, $domain] = explode('@', $email);

        getmxrr($domain, $hosts);

        foreach ($hosts as $host) {
            if (preg_match('/\.google\.com$/i', $host)) {
                return true;
            }
        }

        return false;
    }
}
