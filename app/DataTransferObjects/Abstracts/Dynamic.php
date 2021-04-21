<?php

namespace App\DataTransferObjects\Abstracts;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Str;

// 동적으로 속성을 관리
abstract class Dynamic implements Arrayable, Jsonable
{
    /**
     * set 가능한 필드명 정의
     *
     * @var array
     */
    protected $fillable = ['date', 'current_assets', 'total_assets', 'floating_debt', 'total_debt', 'net_income'];

    /**
     * 실제 데이터가 들어가는 배열
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * 동적 getter, setter
     *
     * @param string $name getter or setter method name
     * @param array $args arguments
     *
     * @return mixed
     */
    public function __call($name, $args)
    {
        if (substr($name, 0, 3) == 'get') {
            return $this->getAttribute(Str::snake(substr($name, 3)));
        }

        if (substr($name, 0, 3) == 'set') {
            $this->setAttribute(Str::snake(substr($name, 3)), $args[0]);
            return $this;
        }
    }

    /**
     * 동적 속성 접근
     *
     * @param string $key 속성 명
     *
     * @return mixed
     */
    public function __get(string $key)
    {
        return $this->getAttribute($key);
    }

    /**
     * 동적 속성 값 설정
     *
     * @param string $key 속성 명
     * @param mixed $value 값
     *
     * @return $this
     */
    public function __set(string $key, $value)
    {
        $this->setAttribute($key, $value);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getAttribute(string $key)
    {
        if (isset($this->attributes[$key])) {
            return $this->attributes[$key];
        }
        return null;
    }

    /**
     * Undocumented function
     *
     * @param string $key
     * @param mixed $value
     *
     * @return $this
     */
    public function setAttribute(string $key, $value)
    {
        if (in_array($key, $this->fillable)) {
            $this->attributes[$key] = $value;
        }
        return $this;
    }

    /**
     * set attributes
     *
     * @param array $input
     *
     * @return $this
     */
    public function fill(array $input)
    {
        foreach ($input as $key => $value) {
            $this->setAttribute($key, $value);
        }

        return $this;
    }

    /**
     * return attributes array
     *
     * @param boolean $allowNull
     *
     * @return array|null
     */
    public function toArray()
    {
        return $this->attributes;
    }

    /**
     * return to json
     *
     * @param int $options
     *
     * @return void
     */
    public function toJson($options = JSON_UNESCAPED_UNICODE)
    {
        return json_encode($this->attributes);
    }
}
