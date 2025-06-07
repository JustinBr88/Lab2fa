<?php
class SanitizarEntrada {

    // Sanitiza una cadena eliminando espacios y etiquetas HTML
    public static function limpiarCadena($cadena) {
        return trim(strip_tags($cadena));
    }

    // Sanitiza texto en general (nombre, apellido, usuario, etc.)
    public static function limpiarTexto($valor) {
        return htmlspecialchars(strip_tags(trim($valor)));
    }

    // Sanitiza correos eliminando caracteres inválidos
    public static function limpiarCorreo($correo) {
        return filter_var(trim($correo), FILTER_SANITIZE_EMAIL);
    }

    // Valida que el correo sea correcto
    public static function validarCorreo($correo) {
        return filter_var($correo, FILTER_VALIDATE_EMAIL);
    }

    // Sanitiza números enteros
    public static function limpiarEntero($valor) {
        return filter_var($valor, FILTER_SANITIZE_NUMBER_INT);
    }

    // Valida que un texto solo contenga letras y espacios
    public static function soloLetras($texto) {
        return preg_match("/^[a-zA-Z\s]+$/", $texto);
    }

    // Valida sexo (solo M o F)
    public static function validarSexo($sexo) {
        return in_array(strtoupper($sexo), ['M', 'F']);
    }

    // Sanitiza todos los datos de un array asociativo (por ejemplo $_POST)
    public static function limpiarArray($array) {
        $limpio = [];
        foreach ($array as $key => $valor) {
            // Puedes personalizar según el campo
            if (strpos($key, 'correo') !== false) {
                $limpio[$key] = self::limpiarCorreo($valor);
            } elseif (strpos($key, 'nombre') !== false || strpos($key, 'apellido') !== false || strpos($key, 'usuario') !== false) {
                $limpio[$key] = self::limpiarTexto($valor);
            } elseif (strpos($key, 'sexo') !== false) {
                $limpio[$key] = strtoupper(self::limpiarTexto($valor));
            } elseif (strpos($key, 'clave') !== false || strpos($key, 'contrasena') !== false) {
                $limpio[$key] = trim($valor); // No sanitizar password con strip_tags
            } else {
                $limpio[$key] = self::limpiarCadena($valor);
            }
        }
        return $limpio;
    }
}
?>
