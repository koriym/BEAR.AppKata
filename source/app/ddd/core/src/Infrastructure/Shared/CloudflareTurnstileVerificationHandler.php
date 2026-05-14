<?php

declare(strict_types=1);

namespace AppCore\Infrastructure\Shared;

use AppCore\Attribute\CloudflareTurnstileSecretKey;
use AppCore\Domain\Captcha\CaptchaTokenMissing;
use AppCore\Domain\Captcha\CaptchaVerifyError;
use AppCore\Domain\Captcha\CloudflareTurnstileVerificationHandlerInterface;

use function curl_exec;
use function curl_getinfo;
use function curl_init;
use function curl_setopt_array;
use function http_build_query;
use function json_decode;

use const CURLINFO_HTTP_CODE;
use const CURLOPT_POST;
use const CURLOPT_POSTFIELDS;
use const CURLOPT_RETURNTRANSFER;
use const CURLOPT_SSL_VERIFYHOST;
use const CURLOPT_SSL_VERIFYPEER;
use const CURLOPT_TIMEOUT;
use const CURLOPT_URL;
use const JSON_THROW_ON_ERROR;

/** @SuppressWarnings("PHPMD.Superglobals") */
final class CloudflareTurnstileVerificationHandler implements CloudflareTurnstileVerificationHandlerInterface
{
    private const VERIFY_URL = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

    public function __construct(
        #[CloudflareTurnstileSecretKey]
        private readonly string $secretKey,
    ) {
    }

    public function __invoke(): void
    {
        if ($this->secretKey === '') {
            return;
        }

        $cfToken = $_POST['cf-turnstile-response'] ?? null;
        if ($cfToken === null) {
            throw new CaptchaTokenMissing();
        }

        $data = [
            'secret' => $this->secretKey,
            'response' => $cfToken,
            'remoteip' => $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? null,
        ];

        $options = [
            CURLOPT_URL => self::VERIFY_URL,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0, // false
        ];

        $curl = curl_init();
        curl_setopt_array($curl, $options);
        $response = (string) curl_exec($curl);
        $code = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if ($code !== 200) {
            throw new CaptchaTokenMissing();
        }

        /** @var array{success: bool, challenge_ts?: string, hostname?: string, error-codes?: array<string>, action?: string, cdata?: string} $result */
        $result = json_decode($response, true, 512, JSON_THROW_ON_ERROR);

        if ($result['success']) {
            return;
        }

        throw new CaptchaVerifyError();
    }
}
