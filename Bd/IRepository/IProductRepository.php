<?php 

namespace Bd\IRepository;

use Models\Product;


 interface IProductRepository
 {
    public function CreateProduct(Product $newProduct) : Product;
    public function UpdateProduct(Product $product): Product;
    public function DeleteProduct(int $id): bool;
    public function GetAllProduct() :array;
    public function GetProductById(int $id) :?Product;
    public function GetAllProductByUserId(int $user_id) :array;
    
 }

?>