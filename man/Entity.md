# \Pan\Kore\Entity::class

## Atributos

- `protected $table : string` nombre de la tabla asociada a la entidad.
- `protected $primary_key : string|array` nombre de la llave/clave primaria de la tabla. Si es una clave compuesta, se debe definir como un `array`.
- `protected $query_fields : array` arreglo con el nombre de los campos de la tabla que siempre serán consultados.

## Métodos

- `getByPK($pk : mixed)` obtener registro asociado a la primary key pasada como parámetro.
