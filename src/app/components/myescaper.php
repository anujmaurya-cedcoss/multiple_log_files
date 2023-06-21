<?php
namespace App\Components;

use Phalcon\Escaper;

class Myescaper
{
    public function sanitize($str)
    {
        $escaper = new Escaper();
        return $escaper->escapeHtml($str);
    }
}
