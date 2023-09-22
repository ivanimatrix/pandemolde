<?php

namespace pan\db\mysql;


class DBMysql
{


    public static function getPkTable($table)
    {
        $query_string = "SELECT k.column_name
        FROM information_schema.table_constraints t
        JOIN information_schema.key_column_usage k
        USING(constraint_name,table_schema,table_name)
        WHERE t.constraint_type='PRIMARY KEY'
          AND t.table_schema='" . DB_NAME . "'
          AND t.table_name='" . $table . "';";
        $_Entity = new \Pan\Kore\Entity();
        $primary_key = $_Entity->raw($query_string);
        $arr_pk = [];
        if ($primary_key) {
            foreach ($primary_key as $pk) {
                $arr_pk[] = $pk->column_name;
            }
        }
        return $arr_pk;
    }

    public static function getTables()
    {
        $query_string = "SHOW TABLES";

        $_Entity = new \Pan\Kore\Entity();
        $tables = $_Entity->raw($query_string);
        $arr_tables = [];
        if ($tables) {
            foreach ($tables as $table) {
                $arr_tables[] = $table->{"Tables_in_" . DB_NAME};
            }
        }
        return $arr_tables;
    }
}
