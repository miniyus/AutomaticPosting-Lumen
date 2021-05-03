<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Main\PostsService;
use App\Services\Main\MainService;
use App\Services\Kiwoom\KoaService;
use App\Services\OpenDart\OpenDartService;
use App\Http\Controllers\DefaultController;

class PostsController extends DefaultController
{
    /**
     * Undocumented variable
     *
     * @var MainService
     */
    protected $mainService;

    /**
     * Undocumented variable
     *
     * @var PostsService
     */
    protected $postsService;

    public function __construct(MainService $mainService, PostsService $postsService)
    {
        $this->mainService = $mainService;
        $this->postsService = $postsService;
    }

    /**
     * Undocumented function
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        return view('list', $this->postsService->paginate());
    }

    public function show(Request $request, string $name = 'sector')
    {
        return view('post', ['data' => $this->mainService->getRefinedData($name)]);
    }

    public function refine(Request $request, string $name)
    {
        return $this->response($this->mainService->getRefinedData($name));
    }
}