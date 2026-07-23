<?php

if (!function_exists('easyparte_env')) {
    function easyparte_env($key, $default = null) {
        $value = getenv($key);

        if ($value === false && isset($_ENV[$key])) {
            $value = $_ENV[$key];
        }

        if ($value === false && isset($_SERVER[$key])) {
            $value = $_SERVER[$key];
        }

        return ($value === false || $value === '') ? $default : $value;
    }
}

if (!function_exists('easyparte_parse_env_value')) {
    function easyparte_parse_env_value($value) {
        $value = trim($value);

        if ($value === '') {
            return '';
        }

        $first = $value[0];
        $last = substr($value, -1);

        if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
            $value = substr($value, 1, -1);
        }

        return $value;
    }
}

if (!function_exists('easyparte_load_env')) {
    function easyparte_load_env($paths = null) {
        if ($paths === null) {
            $paths = [
                dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . '.env',
                dirname(__DIR__) . DIRECTORY_SEPARATOR . '.env',
            ];
        }

        foreach ($paths as $path) {
            if (!is_readable($path) || !is_file($path)) {
                continue;
            }

            $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

            if ($lines === false) {
                error_log('[EasyParte][ENV] Unable to read env file: ' . $path);
                continue;
            }

            foreach ($lines as $line) {
                $line = trim($line);

                if ($line === '' || strpos($line, '#') === 0) {
                    continue;
                }

                if (strpos($line, '=') === false) {
                    continue;
                }

                [$key, $value] = explode('=', $line, 2);
                $key = trim($key);

                if ($key === '') {
                    continue;
                }

                $value = easyparte_parse_env_value($value);

                $_ENV[$key] = $value;
                $_SERVER[$key] = $value;
                putenv($key . '=' . $value);
            }
        }
    }
}

easyparte_load_env();
?>
