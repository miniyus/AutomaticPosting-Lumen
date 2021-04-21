<?php

namespace App\Http\Controllers;

use App\Response\ErrorCode;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Http\Controllers\DefaultController;

class SectorController extends DefaultController
{
    /**
     * open dart service
     *
     * @var Collection
     */
    protected $sectors;

    public function __construct()
    {
        $this->sectors = collect(config('sectors'));
    }

    /**
     * Undocumented function
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        return $this->response($this->sectors);
    }

    /**
     * Undocumented function
     *
     * @param Request $request
     * @param string $market
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, string $market, string $sector = '')
    {
        if ($this->sectors->has($market)) {
            $res = $this->sectors->get($market);
            if (!empty($sector)) {
                $res = $res[$sector];
            }
            return $this->response($res);
        }

        return $this->error(ErrorCode::NOT_FOUND, "{$market} is not found");
    }
}
