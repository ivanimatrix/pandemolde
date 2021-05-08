<?php

namespace Pan\Db;


class DBRecordset
{

    private static $_rows = array();

    public function __construct($rows)
    {
        self::$_rows = $rows;
    }


    public static function getRows()
    {
        return self::$_rows;
    }


    public static function getRow($row)
    {
        $rows = self::$_rows;
        if (!isset($rows[$row]))
            return null;

        return $rows[$row];
    }


    public static function getFirst()
    {

    }


    public static function getLast()
    {

    }

}