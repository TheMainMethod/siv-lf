<?php
class Password
{
    public static function hash($password)
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 11]);
    }
    public static function verify($password, $hash)
    {
        return password_verify($password, $hash);
    }
}

class FormStringElement
{
    private $temporal_value;
    private $final_value;
    private $error_value;

    public function __construct($value)
    {
        $this->temporal_value = $value;
        $this->final_value = $this->error_value = "";
    }

    public function validateEmpty($error_string)
    {
        if (empty($this->temporal_value))
        {
            $this->error_value = $error_string;
        }
        else
        {
            $this->final_value = $this->temporal_value;
        }
    }

    public function getTempValue()
    {
        return $this->temporal_value;
    }

    public function getFinalValue()
    {
        return $this->final_value;
    }

    public function setFinalValue($text)
    {
        $this->final_value = $text;
    }

    public function getErrorValue()
    {
        return $this->error_value;
    }

    public function setErrorValue($text)
    {
        $this->error_value = $text;
    }

    public function noErrors()
    {
        return empty($this->error_value);
    }
}

//si no se usa, borrar
function isInteger($val)
{
    if (!is_scalar($val) || is_bool($val)) {
        return false;
    }
    if (is_float($val + 0) && ($val + 0) > PHP_INT_MAX) {
        return false;
    }
    return is_float($val) ? false : preg_match('~^((?:\+|-)?[0-9]+)$~', $val);
}


?>