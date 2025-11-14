<?php

namespace Ocpi\Support\Objects;

use Carbon\CarbonInterface;

readonly class PaginationOCPIResponse extends OCPIResponse
{
    public function __construct(
        int $statusCode,
        CarbonInterface $timestamp,
        private int $total,
        private int $limit,
        object|array|string|null $data = null,
        ?string $statusMessage = null,
        private ?string $link = null,
    ) {
        parent::__construct($statusCode, $timestamp, $data, $statusMessage);
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function isNextPage(): bool
    {
        return (bool) $this->link;
    }

    public function toArray(): array
    {
        return parent::toArray() + [
            'total' => $this->total,
            'limit' => $this->limit,
            'link' => $this->link,
        ];
    }
}
