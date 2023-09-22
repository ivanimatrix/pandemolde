<?php

namespace pan\db\pgsql;


class DBPgsql
{


    public static function getPkTable($table)
    {
        $query_string = "SELECT c.column_name
        FROM information_schema.table_constraints tc 
        JOIN information_schema.constraint_column_usage AS ccu USING (constraint_schema, constraint_name) 
        JOIN information_schema.columns AS c ON c.table_schema = tc.constraint_schema
          AND tc.table_name = c.table_name AND ccu.column_name = c.column_name
        WHERE constraint_type = 'PRIMARY KEY' and tc.table_name = '" . $table . "';";

        $_Entity = new \Pan\Kore\Entity();
        $pk = $_Entity->raw($query_string);
        $arr_pk = [];
        if ($pk) {
            foreach ($pk as $p) {
                $arr_pk[] = $p->column_name;
            }
        }
        return $arr_pk;
    }


    public static function getTables()
    {
        $query_string = "SELECT *
        FROM pg_catalog.pg_tables
        WHERE schemaname != 'pg_catalog' AND 
            schemaname != 'information_schema';";

        $_Entity = new \Pan\Kore\Entity();
        $tables = $_Entity->raw($query_string);
        return $tables;
    }
}
