<?php

declare(strict_types=1);

final class ApiClient
{
    private Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function assertReachable(): void
    {
        $response = $this->request('GET', '', null, null);
        if ($response['status'] < 100 || $response['status'] >= 500) {
            throw new RuntimeException(
                sprintf('La API no esta operativa (HTTP %d).', $response['status'])
            );
        }
    }

    public function login(string $email, string $password): array
    {
        $response = $this->request(
            'POST',
            '/login',
            null,
            ['email' => $email, 'password' => $password]
        );

        if ($response['status'] !== 200) {
            throw new RuntimeException(
                sprintf('El login de una sesion fixture devolvio HTTP %d.', $response['status'])
            );
        }

        $data = $response['data'];
        if (
            !is_array($data)
            || !isset($data['token'], $data['usuario'])
            || !is_string($data['token'])
            || $data['token'] === ''
            || !is_array($data['usuario'])
        ) {
            throw new RuntimeException('El login no devolvio token y usuario validos.');
        }

        return [
            'token' => $data['token'],
            'usuario' => $data['usuario'],
        ];
    }

    public function get(string $path, string $token): array
    {
        return $this->request('GET', $path, $token, null);
    }

    public function put(string $path, string $token, ?array $body = null): array
    {
        return $this->request('PUT', $path, $token, $body);
    }

    public function assertNoInternalDetails(array $response): void
    {
        $raw = (string)($response['raw'] ?? '');
        $forbidden = [
            '/SQLSTATE/i',
            '/PDOException/i',
            '/Stack trace/i',
            '/Fatal error/i',
            '/Uncaught\s+\w+/i',
            '/[A-Z]:\\\\[^"\r\n]+\.php/i',
            '#/(?:var|home|srv|opt)/[^"\r\n]+\.php#i',
        ];

        foreach ($forbidden as $pattern) {
            if (preg_match($pattern, $raw) === 1) {
                throw new RuntimeException(
                    'Una respuesta HTTP expone detalles internos prohibidos.'
                );
            }
        }
    }

    private function request(
        string $method,
        string $path,
        ?string $token,
        ?array $body
    ): array {
        $url = $this->config->apiUrl() . $path;
        $handle = curl_init($url);
        if ($handle === false) {
            throw new RuntimeException('No se pudo inicializar el cliente HTTP.');
        }

        $headers = ['Accept: application/json'];
        if ($token !== null) {
            $headers[] = 'Authorization: Bearer ' . $token;
        }

        $payload = null;
        if ($body !== null) {
            $payload = json_encode($body, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
            $headers[] = 'Content-Type: application/json';
        }

        curl_setopt_array($handle, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_HEADER => false,
        ]);

        if ($payload !== null) {
            curl_setopt($handle, CURLOPT_POSTFIELDS, $payload);
        }

        $raw = curl_exec($handle);
        if ($raw === false) {
            $error = curl_error($handle);
            curl_close($handle);
            throw new RuntimeException(
                $this->config->redact('Fallo de conexion con la API: ' . $error)
            );
        }

        $status = (int)curl_getinfo($handle, CURLINFO_RESPONSE_CODE);
        curl_close($handle);

        $decoded = null;
        if ($raw !== '') {
            try {
                $decoded = json_decode((string)$raw, true, 512, JSON_THROW_ON_ERROR);
            } catch (JsonException $exception) {
                $decoded = null;
            }
        }

        return [
            'status' => $status,
            'data' => $decoded,
            'raw' => (string)$raw,
        ];
    }
}
