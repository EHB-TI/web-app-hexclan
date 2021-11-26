<?php

namespace App\Models;

class Token
{
    public function __construct($type, $identifier, $plaintext)
    {
        $this->type = $type;
        $this->identifier = $identifier;
        $this->plaintext = $plaintext;
    }
}
