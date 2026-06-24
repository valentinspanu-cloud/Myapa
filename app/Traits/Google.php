<?php

namespace App\Traits;

use TimeHunter\LaravelGoogleReCaptchaV3\Facades\GoogleReCaptchaV3;

trait Google
{
    /**
     * Verify Google ReCaptcha V3
     *
     * @param array $data
     * @return array
     */
    protected function recaptchaAuth(array $data): array
    {
        if (empty($data['g-recaptcha-response'])) {
            return ['score' => 1]; // skip dacă nu e token
        }

        $response = GoogleReCaptchaV3::verifyResponse(
            $data['g-recaptcha-response'],
            request()->ip()
        );

        return [
            'score'   => $response->getScore() ?? 1,
            'success' => $response->isSuccess() ?? true,
        ];
    }
}
