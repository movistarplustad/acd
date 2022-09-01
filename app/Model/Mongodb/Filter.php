<?php

namespace Acd\Model\Mongodb;

class Filter
{
    public static function add($filters, $newFilter)
    {
        return $newFilter
            ? array_merge($filters, $newFilter)
            : $filters;
    }
    public static function periodOfValidity($field, $date)
    {
        if ($date) {
            $filter['$and'] = [
                ['$or' => [
                    [$field . '.end' => ['$gte' => $date]],
                    [$field . '.end' => '']
                ]],
                ['$or' => [
                    [$field . '.start' => ['$lte' => $date]],
                    [$field . '.start' => '']
                ]],
            ];
            return $filter;
        } else {
            return null;
        }
    }
}
