<?php

namespace App\Service;

use App\Repository\BuildRepository;
use App\Repository\CategoryRepository;
use App\Repository\McversionRepository;
use Symfony\Component\HttpFoundation\Request;

final readonly class HomeService
{
    public function __construct(
        private BuildRepository $buildRepository,
        private CategoryRepository $categoryRepository,
        private McversionRepository $mcversionRepository,
    ) {
    }

    public function getHomeData(Request $request, int $page, int $limit): array
    {
        $filters = [
            'search' => trim($request->query->get('search', '')),
            'versions' => $request->query->get('version') ?: null,
            'category' => $request->query->get('category') ?: null,
            'difficulty' => $request->query->get('difficulty') ?: null,
            'sort' => $request->query->get('sort', 'DESC'),
        ];

        $items = $this->buildRepository->findPaginatedOnlineBuilds($page, $limit, $filters);
        $totalItems = $this->buildRepository->countOnlineBuildsWithFilters($filters);

        return [
            'controller_name' => 'HomeController',
            'items' => $items,
            'totalItems' => $totalItems,
            'totalPages' => ceil($totalItems / $limit),
            'currentPage' => $page,
            'filters' => $filters,
            'categories' => $this->categoryRepository->findAll(),
            'request' => $request,
            'versions' => $this->mcversionRepository->findBy([], ['id' => 'DESC']),
        ];
    }
}
