<?php

namespace Pan\Db;


class DBInfo
{

    /**
     * Obtener columna(s) PK de una tabla
     * @param string $table Nombre de la tabla
     * @return mixed Retorna nombre columna(s) PK, o null si parametro $table no es pasado
     *
     */
    public function getTablePK($table = null)
    {
        if (is_null($table))
            return null;

        switch (DB_TYPE) {
            case 'MYSQL':
                $pk = \pan\db\mysql\DBMysql::getPkTable($table);
                break;

            case 'PGSQL':
                $pk = \pan\db\pgsql\DBPgsql::getPkTable($table);
                break;

            case 'MSSQL':
                $pk = \pan\db\mssql\DBMssql::getPkTable($table);
                break;
        }

        return $pk;
    }


    /**
     * @param null $table
     * @return mixed Valor del ultimo ID ingresado, o null en caso de falla
     */
    public function getLastInsertID($table = null)
    {
        if (is_null($table))
            return null;

        switch (DB_TYPE) {
            case 'MYSQL':
                break;

            case 'PGSQL':
                break;

            case 'MSSQL':
                break;
        }
    }


    public function getTables()
    {
        $arr_tables = [];
        switch (DB_TYPE) {
            case 'MYSQL':
                $arr_tables = \pan\db\mysql\DBMysql::getTables();
                break;

            case 'PGSQL':
                $tables = \pan\db\pgsql\DBPgsql::getTables();
                if ($tables) {
                    foreach ($tables as $table) {
                        $arr_tables[] = $table->tablename;
                    }
                }
                break;

            case 'MSSQL':
                $tables = \pan\db\mssql\DBMssql::getTables();
                break;
        }

        return $arr_tables;
    }
}
