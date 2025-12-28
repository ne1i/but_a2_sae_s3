<?php

namespace ButA2SaeS3\services;

use ButA2SaeS3\utils\HttpUtils;

class FormService
{
    public static function handleFormSubmission(
        callable $validator,
        callable $processor,
        string $successMessage,
        string $currentPage,
        string $prefix = ''
    ): void {
        if (!HttpUtils::isPost()) {
            return;
        }

        $formData = self::sanitizeFormData($_POST);
        $validationResult = $validator($formData);

        if ($validationResult->isValid()) {
            try {
                $processor($validationResult->value());
                self::setSuccessMessage($successMessage, $prefix);
                header("Location: " . self::withRedirectSuccess($currentPage, $prefix));
            } catch (\Throwable $e) {
                self::setFormState($formData, ['_form' => $e->getMessage()], $prefix);
                header("Location: " . self::withoutQuerySuccess($currentPage));
            }
            exit();
        }

        self::setFormState($formData, $validationResult->messages(), $prefix);
        header("Location: " . self::withoutQuerySuccess($currentPage));
        exit();
    }

    public static function restoreFormData(string $prefix = ''): array
    {
        $dataKey = self::key('form_data', $prefix);
        $errorsKey = self::key('form_errors', $prefix);

        $data = $_SESSION[$dataKey] ?? [];
        $errors = $_SESSION[$errorsKey] ?? [];

        unset($_SESSION[$dataKey], $_SESSION[$errorsKey]);

        return ['data' => $data, 'errors' => $errors];
    }

    public static function getSuccessMessage(string $prefix = ''): ?string
    {
        if (!isset($_GET['success']) || $_GET['success'] !== '1') {
            return null;
        }

        if ($prefix !== '' && (($_GET['success_form'] ?? '') !== $prefix)) {
            return null;
        }

        $key = self::key('success_message', $prefix);
        $message = $_SESSION[$key] ?? null;
        unset($_SESSION[$key]);
        return $message;
    }

    public static function getErrorMessage(string $prefix = ''): ?string
    {
        $key = self::key('error_message', $prefix);
        $message = $_SESSION[$key] ?? null;
        unset($_SESSION[$key]);
        return $message;
    }

    public static function setSuccessMessage(string $message, string $prefix = ''): void
    {
        $_SESSION[self::key('success_message', $prefix)] = $message;
    }

    public static function setErrorMessage(string $message, string $prefix = ''): void
    {
        $_SESSION[self::key('error_message', $prefix)] = $message;
    }

    private static function setFormState(array $data, array $errors, string $prefix = ''): void
    {
        $_SESSION[self::key('form_data', $prefix)] = $data;
        $_SESSION[self::key('form_errors', $prefix)] = $errors;
    }

    private static function key(string $baseKey, string $prefix): string
    {
        if ($prefix === '') {
            return $baseKey;
        }
        return $baseKey . '__' . $prefix;
    }

    private static function withRedirectSuccess(string $url, string $prefix = ''): string
    {
        return self::withQuery($url, [
            'success' => '1',
            'success_form' => $prefix !== '' ? $prefix : null
        ]);
    }

    private static function withoutQuerySuccess(string $url): string
    {
        [$base, $fragment] = array_pad(explode('#', $url, 2), 2, null);
        return $fragment !== null ? ($base . '#' . $fragment) : $base;
    }

    private static function withQuery(string $url, array $params): string
    {
        [$base, $fragment] = array_pad(explode('#', $url, 2), 2, null);
        $baseParts = explode('?', $base, 2);
        $path = $baseParts[0];
        $existingQuery = $baseParts[1] ?? '';

        $queryParams = [];
        if ($existingQuery !== '') {
            parse_str($existingQuery, $queryParams);
        }

        foreach ($params as $k => $v) {
            if ($v === null) {
                unset($queryParams[$k]);
                continue;
            }
            $queryParams[$k] = $v;
        }

        $query = http_build_query($queryParams);
        $out = $query !== '' ? ($path . '?' . $query) : $path;
        return $fragment !== null ? ($out . '#' . $fragment) : $out;
    }

    private static function sanitizeFormData(array $data): array
    {
        return array_map(function ($value) {
            return is_string($value) ? trim($value) : $value;
        }, $data);
    }
}
