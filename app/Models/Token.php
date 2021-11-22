<?php

namespace App\Models;

class Token
{
    public function __construct($type, $identifier, $plaintext, ?int $id = null)
    {
        $this->type = $type;
        $this->identifier = $identifier;
        $this->plaintext = $plaintext;
        $this->id = $id;
    }
}
