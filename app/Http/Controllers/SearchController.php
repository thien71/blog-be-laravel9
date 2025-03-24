<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Services\SearchService;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    protected $searchService;

    public function __construct(SearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    public function index(Request $request)
    {
        $posts = $this->searchService->search($request);

        return PostResource::apiPaginate($posts, $request);
    }
}
