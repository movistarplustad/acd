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
        if(!$date) return null;

        $filter['$and'] = [
                ['$or' => [
                        [$field.'.end' => ['$gte' => $date]],
                        [$field.'.end' => '']
                ]],
                ['$or' => [
                        [$field.'.start' => ['$lte' => $date]],
                        [$field.'.start' => '']
                ]],
        ];
        return $filter;
    }
    public static function profile(?string $profile)
    {
        if(!$profile) return null;

        $filter['$or'] = [
            ['profile' => ['$size' => 0]],
            ['profile' => ['$in' => [$profile]]]
        ];
        return $filter;
    }
}