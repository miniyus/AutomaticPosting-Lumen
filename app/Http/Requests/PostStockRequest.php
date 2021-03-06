<?php

namespace App\Http\Requests;

use Throwable;
use App\Data\DataTransferObjects\StockInfo;
use App\Response\ErrorCode;
use Illuminate\Http\Request;
use App\Response\ApiResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * request에 대한 처리
 */
class PostStockRequest
{
    /**
     * parse
     *
     * @param Request $request
     * @param string $method
     * @return Collection
     */
    public static function parse(string $method, Request $request)
    {
        $jsonArray = collect();

        try {
            if ($request->has('code') && $request->has('name') && $request->has('data')) {
                $jsonArray->add(self::convert($method, $request->all()));
            } else {
                self::failedValidation("Failed Parse Request: 필수 항목 누락");
            }
        } catch (Throwable $e) {
            self::failedValidation("Failed Parse Request: {$e->getMessage()}");
        }

        if ($jsonArray->isEmpty()) {
            self::failedValidation('Failed Parse Request: not found data');
        }

        return $jsonArray;
    }

    /**
     * convert
     * @param string $method
     * @param array $req
     *
     * @return Collection
     */
    public static function convert(string $method, array $req)
    {
        $req = collect($req);
        $stockInfo = new StockInfo;
        return collect([
            'file_name' => "{$method}_{$req->get('code')}",
            "{$method}_code" => $req->get('code'),
            "{$method}_name" => $req->get('name'),
            'updated_at' => Carbon::now()->format('Y-m-d'),
            'stock_data' => $stockInfo->mapList(collect($req->get('data')))
        ]);
    }

    /**
     * throw Exception
     *
     * @param array|string $error
     *
     * @throws HttpResponseException
     */
    protected static function failedValidation($error)
    {
        throw new HttpResponseException(ApiResponse::error(ErrorCode::VALIDATION_FAIL, $error));
    }
}
