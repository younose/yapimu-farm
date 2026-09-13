<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class SuccessResource extends ResourceCollection
{
    public $message;

    public $resource;

    /**
     * __construct
     *
     * @param  mixed  $message
     * @param  mixed  $resource
     * @return void
     */
    public function __construct($message = 'Berhasil!', $resource = [])
    {
        parent::__construct($resource);
        $this->message = $message;
        $this->resource = $resource;
    }

    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray($request)
    {
        return [
            'success' => true,
            'message' => $this->message,
            'data' => $this->resource,
        ];
    }
}
