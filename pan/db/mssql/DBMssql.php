<?php

namespace pan\db\mssql;


class DBMssql {


    public static function getPkTable($table)
    {

    }

    public static function getTables()
    {
        $query_string = "SHOW TABLES";

        return $query_string;
    }
}