# \Pan\Utils\LoggerPan::class

## Constructor
- `\Pan\Utils\LoggerPan(string $log_file)`
  - `$log_file` : Corresponde a la ruta (nombre) de archivo para el log

## Constantes
- `\Pan\Utils\LoggerPan::LOG_INFO = 1` 
- `\Pan\Utils\LoggerPan::LOG_WARNING = 2`
- `\Pan\Utils\LoggerPan::LOG_ERROR = 3`

## Métodos
- `setLogFile(string $log_file)` : Establecer la ruta del archivo para log.
  - `$log_file : string` Ruta (nombre) del archivo
- `writeLog(string $message[, int $type)` : Escribe $message en el archivo de log indicado
  - `$message` : Texto correspondiente al mensaje que se escribe en el archivo log.
  - `$type` : Opcional. Indica el nivel (tipo) de mensaje. Por defecto es `\Pan\Utils\LoggerPan::LOG_INFO`, pero puede ser `\Pan\Utils\LoggerPan::LOG_WARNING` o `\Pan\Utils\LoggerPan::LOG_ERROR`
- `clearLog()` : Limpia el contenido del archivo log