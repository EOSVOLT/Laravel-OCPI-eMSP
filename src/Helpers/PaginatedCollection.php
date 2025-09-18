<?php

namespace Ocpi\Helpers;

abstract class PaginatedCollection extends TypeCollection
{
    public const DEFAULT_FIRST_PAGE = 1;
    public const DEFAULT_PER_PAGE = 20;
    public const DEFAULT_LAST_VIEW_PER_PAGE = 10;
    public const DEFAULT_NO_RESULTS_TOTAL = 0;

    public function __construct(
        private readonly int $page = self::DEFAULT_FIRST_PAGE,
        private readonly int $perPage = self::DEFAULT_PER_PAGE,
        private readonly int $totalPages = self::DEFAULT_NO_RESULTS_TOTAL,
        private readonly int $totalResults = 0,
        array $items = []
    ) {
        parent::__construct($items);
    }

    /**
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @return int
     */
    public function getPerPage(): int
    {
        return $this->perPage;
    }

    /**
     * @return int
     */
    public function getTotalPages(): int
    {
        return $this->totalPages;
    }

    /**
     * @return int
     */
    public function getTotalResults(): int
    {
        return $this->totalResults;
    }

    /**
     * @return int|null
     */
    public function getNextPage(): ?int
    {
        return $this->page < $this->totalPages ? $this->page + 1 : null;
    }


    /**
     * @return array
     */
    public function getPagination(): array
    {
        return [
            'current_page' => $this->getPage(),
            'next_page' => $this->getNextPage(),
            'last_page' => $this->getTotalPages(),
            'per_page' => $this->getPerPage(),
            'results' => $this->getTotalResults(),
        ];
    }
}