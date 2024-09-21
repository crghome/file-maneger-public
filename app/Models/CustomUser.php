<?php
use Illuminate\Auth\UserInterface;
use Illuminate\Foundation\Auth\User as Authenticatable;

class CustomUser implements Authenticatable
{
    protected $id;
    protected $username;
    protected $password;

    public function __construct($id, $username, $password)
    {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
    }

    public function getAuthIdentifier()
    {
        return $this->id;
    }

    public function getAuthPassword()
    {
        return $this->password;
    }

    // Other methods as needed
}