<?php

namespace Models;

class User
{
    private ?int $Id;
    private string $Name;
    private string $Email;
    private string $Password;

    public function __construct( string $name, string $email, string $password,?int $id = null)
    {
        $this->Id = $id;
        $this->Name = $name;
        $this->Email = $email;
        $this->Password = $password;
    }


    public function getId(): ?int
    {
        return $this->Id;
    }
    public function getName(): string
    {
        return $this->Name;
    }

    public function getEmail(): string
    {
        return $this->Email;
    }

    public function getPassword(): string
    {
        
        return $this->Password;
    }

    public function setId(int $id): void
    {
        $this->Id = $id;
    }
    public function setName(string $name): void
    {
        $name =  preg_replace("/[\'\"<>]/", '', $name );
        $this->Name = $name;
    }

    public function setEmail(string $email): void
    {
        $email = preg_replace("/[^a-zA-Z0-9@._\-]/", '', $email);
        $this->Email = $email;
    }

    public function setPassword(string $password): void
    {
        $password =  preg_replace("/[\'\"<>]/", '', $password );
        $this->Password = $password;
    }

}
