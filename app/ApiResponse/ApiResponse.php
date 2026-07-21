<?php
declare(strict_types=1);

namespace App\ApiResponse;

use Illuminate\Http\JsonResponse;


final class ApiResponse
{
    /**
     * Create a new class instance.
     */
    private ?string $message = null;
    private int $status = 200;
    private mixed $data = null;
    private array $appends = [];

    public static function builder(): ApiResponseBuilder
    {
        return new ApiResponseBuilder();
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    public function setData(mixed $data): void
    {
        $this->data = $data;

    }

    public function setAppends(array $appends): void
    {
        $this->appends = $appends;
    }

    public function response(): JsonResponse
    {
        $body = [];

        if ($this->message !== null) {
            $body['message'] = $this->message;
        }
        if ($this->data !== null) {
            $body['data'] = $this->data;
        }

        $body = $body + $this->appends;
        return response()->json(
            data: $body,
            status: $this->status
        );

    }

}
