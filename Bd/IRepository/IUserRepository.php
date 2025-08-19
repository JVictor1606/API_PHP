<?php 


namespace Bd\IRepository;

use Models\User;


 interface IUserRepository
 {
    public function CreateUser(User $newUser) : User;
    public function UpdateUser(User $user): User;
    public function DeleteUser(int $id): bool;
    public function GetAllUser() :array;
    public function GetUserById(int $id): ?User;
    public function GetUserByEmail(string $email): ?User;
 }

?>