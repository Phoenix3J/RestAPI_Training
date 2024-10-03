<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use function PHPUnit\Framework\isNull;

class ProductController extends Controller
{
    public function index()
    {
        // $products = Product::all();

        $products = Product::paginate(2);

        // $data = [
        //     'status' => 200,
        //     'message' => 'hai, test api nih',
        //     'responseCode' => '00',
        //     'data' => $products
        // ];

        return response()->json($products, Response::HTTP_OK);
    }


    // memunculkan detail product
    public function show($id)
    {
        $product = Product::find($id);
        //jika data tidak ditemukan
        if (is_null($product)) {
            $data = [
                'status' => Response::HTTP_NOT_FOUND,
                'message' => 'Data produk tidak ditemukan',

            ];
            return response()->json($data, Response::HTTP_NOT_FOUND);
        } else {
            //jika ditemukan
            $data = [
                'status' => Response::HTTP_OK,
                'message' => 'Data produk berhasil ditemukan',
                'data' => $product
            ];
            return response()->json($data, Response::HTTP_OK);
        }
    }




    //create product
    public function store(Request $request)
    {
        //validasi request
        $request->validate([
            'name' => 'required|string|max:20',
            'price' => 'required|integer',
            'description' => 'required|string|max:120',
            'image' => 'image|required|max:2048|mimes:png,jpg,jpeg'
        ]);

        $input = $request->all();
        //logic upload gambar
        if ($image = $request->file('image')) {
            $target = 'assets/images/';
            $product_img = date('YmdHis') . "." . $image->getClientOriginalExtension();
            $image->move($target, $product_img);
            $input['image'] = $product_img;
        }
        // masukkan ke database
        Product::create($input);

        $data = [
            'status' => Response::HTTP_CREATED,
            'message' => 'Data gambar produk berhasil ditambahkan',
            'data' => $input
        ];
        return response()->json($data, Response::HTTP_CREATED);
    }





    //update product
    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if ($product) {
            //validasi request
            $request->validate([
                'name' => 'string|max:20',
                'price' => 'integer',
                'description' => 'string|max:120',
                //'image' => 'image|max:2048|mimes:png,jpg,jpeg'
            ]);

            $input = $request->all();
            //logic upload gambar
            if ($image = $request->file('image')) {
                $target = 'assets/images/';
                //jika ada image
                unlink($target . $product->image);
                $product_img = date('YmdHis') . "." . $image->getClientOriginalExtension();
                $image->move($target, $product_img);
                $input['image'] = $product_img;
            } else {
                //jika tidak ada image
                $input['image'] = $product->image;
            }

            //update data ke database
            $product->update($input);
            $data = [
                'status' => Response::HTTP_OK,
                'message' => 'data produk berhasil di-update!',
                'data' => $product
            ];
            return response()->json($data, Response::HTTP_OK);
        } else {
            $data = [
                'status' => Response::HTTP_NOT_FOUND,
                'message' => 'Data produk tidak ditemukan',

            ];
            return response()->json($data, Response::HTTP_NOT_FOUND);
        }
    }

    //delete product
    public function destroy($id)
    {
        $product = Product::find($id);

        if ($product) {
            $target = 'assets/images/';
            unlink($target . $product->image);
            $product->delete();
            $data = [
                'status' => Response::HTTP_OK,
                'message' => 'Data produk berhasil dihapus'
            ];
            return response()->json($data, Response::HTTP_OK);
        } else {
            $data = [
                'status' => Response::HTTP_NOT_FOUND,
                'message' => 'Data produk tidak ditemukan, apaan yg mau dihapus'
            ];
            return response()->json($data, Response::HTTP_NOT_FOUND);
        }
    }
}
