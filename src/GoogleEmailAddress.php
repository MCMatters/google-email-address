<?php

declare(strict_types=1);

namespace McMatters\GoogleEmailAddress;

use function count;
use function explode;
use function filter_var;
use function getmxrr;
use function implode;
use function in_array;
use function preg_match;
use function str_replace;
use function substr;

use const false;
use const FILTER_VALIDATE_EMAIL;
use const true;

/**
 * Class GoogleEmailAddress
 *
 * @package McMatters\GoogleEmailAddress
 */
class GoogleEmailAddress
{
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
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        if (preg_match('/@g(?:oogle)?mail\.com$/i', $email)) {
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
     * @return string
     */
    public function normalize(string $email): string
    {
        $mxRecords = false;

        if (!$this->isGoogleEmailAddress($email)) {
            $mxRecords = $this->checkMXRecords($email);

            if (!$mxRecords) {
                return $email;
            }
        }

        $parts = explode('@', $email);
        $countParts = count($parts);

        if ($countParts < 2) {
            return $email;
        }

        $hostKey = $countParts - 1;
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
