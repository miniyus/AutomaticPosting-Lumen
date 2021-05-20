<?php

namespace App\Services\OpenDart;

use App\Services\Service;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Carbon;
use Illuminate\Pagination\Paginator;
use App\Data\DataTransferObjects\CorpCode;
use App\Exceptions\ApiErrorException;
use Illuminate\Support\Collection;
use JsonMapper_Exception;

class OpenDartService extends Service
{
    /**
     * @var OpenDartClient
     */
    protected OpenDartClient $module;

    public function __construct(OpenDartClient $client)
    {
        $this->module = $client;
    }

    /**
     * 회사 고유코드 저장
     *
     * @return bool
     * @throws ApiErrorException
     */
    public function saveCorpCodes(): bool
    {
        if ($this->module->requestCorpCodes()) {
            return true;
        }

        $this->throw(self::CONFLICT, 'failed store corp codes');
    }

    /**
     * Undocumented function
     *
     * @param string|null $code
     *
     * @return \App\Data\DataTransferObjects\Paginator|Collection
     * @throws FileNotFoundException
     * @throws JsonMapper_Exception
     */
    public function getCorpCode(string $code = null)
    {
        if (is_null($code)) {
            return $this->module->getCorpCode();
        }

        $codes = explode(',', $code);
        $res = collect();
        foreach ($codes as $code) {
            $res->add($this->module->getCorpCode($code)->first());
        }

        return $res;
    }

    /**
     * Undocumented function
     *
     * @param string $stockCode
     *
     * @return CorpCode
     * //
     * @throws FileNotFoundException
     * @throws JsonMapper_Exception
     */
    public function findCorpCodeByStockCode(string $stockCode): CorpCode
    {
        $corpCodes = $this->module->getCorpCode();

        return $corpCodes->filter(function ($item) use ($stockCode) {
            if ($item instanceof CorpCode) {
                return $item->getStockCode() == $stockCode;
            }
            return false;
        })->first();
    }

    /**
     * 단일 회사 주요 계정가져오기
     *
     * @param string $stockCode
     * @param string|null $year
     * @param string $reprtCode
     *
     * @return Collection
     * @throws FileNotFoundException
     * @throws JsonMapper_Exception
     */
    public function getSingle(string $stockCode, string $year = null, string $reprtCode = '11011'): Collection
    {
        if (is_null($year)) {
            $year = Carbon::now()->format('Y');
        }

        if (!is_numeric($year) || strlen($year) != 4) {
            $this->throw(self::VALIDATION_FAIL, ['year' => ["year parameter must be 'yyyy' format"]]);
        }

        $corpCode = $this->findCorpCodeByStockCode($stockCode);

        if (is_null($corpCode)) {
            $this->throw(self::RESOURCE_NOT_FOUND, "can not found sotck: " . $stockCode);
        }

        return $this->module->getSinglAcnt($corpCode->getCorpCode(), $year, $reprtCode);
    }

    /**
     * 다중 회사 주요 계정 가져오기
     *
     * @param array $stockCodes
     * @param string|null $year
     * @param string $reprtCode
     * @return Collection
     * @throws FileNotFoundException
     * @throws JsonMapper_Exception
     */
    public function getMultiple(array $stockCodes, string $year = null, string $reprtCode = '11011'): Collection
    {
        $corpCodes = collect();

        if (is_null($year)) {
            $year = Carbon::now()->subYear()->format('Y');
        }

        if (!is_numeric($year) || strlen($year) != 4) {
            $this->throw(self::VALIDATION_FAIL, ['year' => ["year parameter must be 'yyyy' format"]]);
        }

        foreach ($stockCodes as $stockCode) {
            $corpCode = $this->findCorpCodeByStockCode($stockCode);
            if (!is_null($corpCode)) {
                $corpCodes->add($corpCode->getCorpCode());
            }
        }

        $res = $this->module->getMultiAcnt($corpCodes->all(), $year);
        if ($res->isEmpty()) {
            $this->throw(self::RESOURCE_NOT_FOUND, "can not found stocks");
        }

        if ($res->has('error')) {
            $this->throw(self::CONFLICT, $res);
        }

        return $res;
    }
}
