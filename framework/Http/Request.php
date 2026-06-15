<?php

namespace Framework\Http;

class Request
{
    private array $query;
    private array $post;
    private array $server;
    private array $files;
    private array $errors        = [];
    private array $validatedData = [];

    public function __construct(
        array $query  = [],
        array $post   = [],
        array $server = [],
        array $files  = []
    ) {
        $this->query  = $query;
        $this->post   = $post;
        $this->server = $server;
        $this->files  = $files;
    }

    public static function fromGlobals(): static
    {
        return new static($_GET, $_POST, $_SERVER, $_FILES);
    }

    // ── Input ──────────────────────────────────────────────────────────────

    public function input(string $key, mixed $default = null): mixed
    {
        $value = $this->post[$key] ?? $this->query[$key] ?? $default;
        return is_string($value) ? trim($value) : $value;
    }

    public function only(array $keys): array
    {
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $this->input($key);
        }
        return $result;
    }

    public function except(array $keys): array
    {
        $all = $this->all();
        foreach ($keys as $key) {
            unset($all[$key]);
        }
        return $all;
    }

    public function all(): array
    {
        $merged = array_merge($this->query, $this->post);
        return array_map(fn($v) => is_string($v) ? trim($v) : $v, $merged);
    }

    public function has(string $key): bool
    {
        return isset($this->post[$key]) || isset($this->query[$key]);
    }

    public function filled(string $key): bool
    {
        return $this->has($key) && $this->input($key) !== '';
    }

    // ── HTTP Method ────────────────────────────────────────────────────────

    public function method(): string
    {
        return strtoupper($this->server['REQUEST_METHOD'] ?? 'GET');
    }

    public function isMethod(string $method): bool
    {
        return $this->method() === strtoupper($method);
    }

    public function isPost(): bool { return $this->isMethod('POST'); }
    public function isGet(): bool  { return $this->isMethod('GET');  }

    // ── Meta ───────────────────────────────────────────────────────────────

    public function ip(): string
    {
        $ip = $this->server['HTTP_X_FORWARDED_FOR'] ?? $this->server['REMOTE_ADDR'] ?? '';
        return substr(trim((string) $ip), 0, 45);
    }

    public function userAgent(): string
    {
        return $this->server['HTTP_USER_AGENT'] ?? '';
    }

    public function header(string $name, string $default = ''): string
    {
        $key = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
        return $this->server[$key] ?? $default;
    }

    // ── Files ──────────────────────────────────────────────────────────────

    public function file(string $key): ?array
    {
        return $this->files[$key] ?? null;
    }

    public function hasFile(string $key): bool
    {
        return isset($this->files[$key]) && $this->files[$key]['error'] === UPLOAD_ERR_OK;
    }

    // ── Validation ─────────────────────────────────────────────────────────

    /**
     * Validate input against rules.
     *
     * Supported rules (pipe-separated string or array):
     *   required          field must be present and non-empty
     *   nullable          skip remaining rules when field is empty
     *   string            must be a string
     *   numeric           must be numeric
     *   integer           must be a whole number
     *   email             must be a valid email address
     *   min:n             min length (strings) or min value (numbers)
     *   max:n             max length (strings) or max value (numbers)
     *   in:a,b,c          value must be one of the given options
     *
     * Example:
     *   $request->validate([
     *       'name'   => 'required|max:100',
     *       'email'  => 'required|email',
     *       'budget' => 'nullable|numeric|min:0',
     *       'status' => 'required|in:new,contacted,completed',
     *   ]);
     */
    public function validate(array $rules): bool
    {
        $this->errors        = [];
        $this->validatedData = [];

        foreach ($rules as $field => $ruleSet) {
            $list     = is_string($ruleSet) ? explode('|', $ruleSet) : (array) $ruleSet;
            $value    = $this->input($field);
            $nullable = in_array('nullable', $list, true);

            if ($nullable && ($value === null || $value === '')) {
                $this->validatedData[$field] = $value ?? '';
                continue;
            }

            foreach ($list as $rule) {
                if ($rule === 'nullable') continue;

                $error = $this->applyRule($field, $value, $rule);
                if ($error !== null) {
                    $this->errors[$field] = $error;
                    break;
                }
            }

            if (!isset($this->errors[$field])) {
                $this->validatedData[$field] = $value;
            }
        }

        return empty($this->errors);
    }

    private function applyRule(string $field, mixed $value, string $rule): ?string
    {
        $label = ucfirst(str_replace('_', ' ', $field));

        return match (true) {
            $rule === 'required'           => ($value === null || $value === '')
                                                ? "{$label} is required."
                                                : null,

            $rule === 'string'             => !is_string($value)
                                                ? "{$label} must be a string."
                                                : null,

            $rule === 'numeric'            => !is_numeric($value)
                                                ? "{$label} must be a number."
                                                : null,

            $rule === 'integer'            => filter_var($value, FILTER_VALIDATE_INT) === false
                                                ? "{$label} must be an integer."
                                                : null,

            $rule === 'email'              => !filter_var($value, FILTER_VALIDATE_EMAIL)
                                                ? "{$label} must be a valid email address."
                                                : null,

            str_starts_with($rule, 'min:') => $this->checkMin($label, $value, (int) substr($rule, 4)),
            str_starts_with($rule, 'max:') => $this->checkMax($label, $value, (int) substr($rule, 4)),
            str_starts_with($rule, 'in:')  => $this->checkIn($label, $value, substr($rule, 3)),

            default                        => null,
        };
    }

    private function checkMin(string $label, mixed $value, int $n): ?string
    {
        if (is_string($value)) {
            return mb_strlen($value) < $n ? "{$label} must be at least {$n} characters." : null;
        }
        if (is_numeric($value) && (float) $value < $n) {
            return "{$label} must be at least {$n}.";
        }
        return null;
    }

    private function checkMax(string $label, mixed $value, int $n): ?string
    {
        if (is_string($value)) {
            return mb_strlen($value) > $n ? "{$label} must be no more than {$n} characters." : null;
        }
        if (is_numeric($value) && (float) $value > $n) {
            return "{$label} must be no more than {$n}.";
        }
        return null;
    }

    private function checkIn(string $label, mixed $value, string $optionStr): ?string
    {
        $options = explode(',', $optionStr);
        if (!in_array($value, $options, true)) {
            return "{$label} must be one of: " . implode(', ', $options) . ".";
        }
        return null;
    }

    // ── Results ────────────────────────────────────────────────────────────

    public function validated(): array  { return $this->validatedData; }
    public function errors(): array     { return $this->errors; }
    public function hasErrors(): bool   { return !empty($this->errors); }
    public function firstError(): string
    {
        return array_values($this->errors)[0] ?? '';
    }
}
