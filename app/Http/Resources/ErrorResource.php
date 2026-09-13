<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ErrorResource extends ResourceCollection
{
    public $message;

    public $resource;

    public $th;

    /**
     * __construct
     *
     * @param  mixed  $message
     * @param  mixed  $resource
     * @return void
     */
    public function __construct($th, $message = 'Terjadi kesalahan!', $resource = [])
    {
        parent::__construct($resource);
        $this->message = $message;
        $this->resource = $resource;
        $this->th = $th;
    }

    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray($request)
    {
        $response = [
            'success' => false,
            'message' => $this->message,
            'error' => [
                'message' => $this->th ? $this->th->getMessage() : 'Undefined',
                'file' => $this->th ? $this->th->getFile() : 'Undefined',
                'line' => $this->th ? $this->th->getLine() : 'Undefined',
            ],
            'data' => $this->resource,
        ];

        return $response;

    }
}
