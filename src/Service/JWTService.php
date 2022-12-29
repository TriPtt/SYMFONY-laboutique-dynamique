<?php

namespace App\Service;

use DateTimeImmutable;

class JWTService
{
    /**
     * Génère un token JWT
     *
     * @param array $header
     * @param array $payload
     * @param string $secret
     * @param int $validity
     * @return string
     */
    public function generate(array $header, array $payload, string $secret, int $validity = 10800): string
    {
        if ($validity > 0) {
            $now = new DateTimeImmutable();
            $exp = $now->getTimestamp() + $validity;

            $payload['iat'] = $now->getTimestamp();
            $payload['exp'] = $exp;
        }

        // On encode en base64
        $base64Header = base64_encode(json_encode($header));
        $base64Payload = base64_encode(json_encode($payload));

        // On "nettoie" les valeurs encodées (retrait des +, / et =)
        $base64Header = str_replace(['+', '/', '='], ['-', '_', ''], $base64Header);
        $base64Payload = str_replace(['+', '/', '='], ['-', '_', ''], $base64Payload);

        // On génère la signature
        $secret = base64_encode($secret);

        $signature = hash_hmac('sha256', $base64Header . '.' . $base64Payload, $secret, true);

        $base64Signature = base64_encode($signature);

        $base64Signature = str_replace(['+', '/', '='], ['-', '_', ''], $base64Signature);

        // On crée le token
        $jwt = $base64Header . '.' . $base64Payload . '.' . $base64Signature;

        return $jwt;
    }

    /**
     * Vérifie si le token est valide
     *
     * @param string $token
     * @return bool
     */
    public function isValid(string $token): bool
    {
        return preg_match(
            '/^[a-zA-Z0-9\-\_\=]+\.[a-zA-Z0-9\-\_\=]+\.[a-zA-Z0-9\-\_\=]+$/',
            $token
        ) === 1;
    }

    /**
     * Récupère le payload du token
     *
     * @param string $token
     * @return array
     */
    public function getPayload(string $token): array
    {
        $array = explode('.', $token);

        $payload = json_decode(base64_decode($array[1]), true);

        return $payload;
    }

    /**
     * Récupère le header du token
     *
     * @param string $token
     * @return array
     */
    public function getHeader(string $token): array
    {
        $array = explode('.', $token);

        $header = json_decode(base64_decode($array[0]), true);

        return $header;
    }

    /**
     * Vérifie si le token est expiré
     *
     * @param string $token
     * @return bool
     */
    public function isExpired(string $token): bool
    {
        $payload = $this->getPayload($token);

        $now = new DateTimeImmutable();

        return $payload['exp'] < $now->getTimestamp();
    }

    /**
     * Vérifie si le token est vérifié
     *
     * @param string $token
     * @param string $secret
     */
    public function check(string $token, string $secret)
    {
        // On récupère le header et le payload
        $header = $this->getHeader($token);
        $payload = $this->getPayload($token);

        // On régénère un token
        $verifToken = $this->generate($header, $payload, $secret, 0);

        return $token === $verifToken;
    }
}
