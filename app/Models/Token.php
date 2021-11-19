<?php

namespace App\Models;

class Token
{
    public function __construct($id, $type, $plaintext)
    {
        $this->id = $id;
        $this->type = $type;
        $this->plaintext = $plaintext;
    }

    public function getId()
    {
        $this->id;
    }

    public function getType()
    {
        $this->type;
    }

    public function getPlaintext()
    {
        $this->plaintext;
    }
}
