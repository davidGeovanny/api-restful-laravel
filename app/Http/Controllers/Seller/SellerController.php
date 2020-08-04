<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

use App\Seller;

class SellerController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $vendedores = Seller::has('products')->get();

        return $this->showAll($vendedores);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Seller $seller)
    {
        // $vendedor = Seller::has('products')->findOrFail($id);

        return $this->showOne($seller);
    }
}
