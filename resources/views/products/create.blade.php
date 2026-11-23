{{--
    /products/create – the product list with the Add Product modal already open.
    The list page always contains the Add modal; ProductController@create passes
    $openCreate = true so it starts opened. (On the list page the Add Product
    button opens the same modal without loading this URL.)
--}}
@extends('products.index')
