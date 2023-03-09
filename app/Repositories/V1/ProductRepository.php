<?php
namespace App\Repositories\V1;

use App\Models\Product;
use App\Repositories\BaseRepository\V1;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ProductRepository extends BaseRepository
{
    public function storeProduct($req)
    {
        $slug = $req['name'];
        $num = Str::random(1);
        $uniqueSlug = Product::where('name', $slug)->count();

        if ($uniqueSlug > 0) {
            $slug = Str::slug($slug . '-' . $num);
        }

        $imageName = time() . '.' . $req['image']->getClientOriginalExtension();
        $req['image']->move(public_path('/product_image'), $imageName);

        $data = [
            'name' => $req['name'],
            'author' => auth()->user()->id,
            'description' => $req['description'],
            'price' => $req['price'],
            'slug' => Str::slug($slug),
            'image' => $imageName,
            'category' => $req['category'],
        ];

        $productStore = Product::create($data);
        return $productStore;
    }

    public function updateProduct($req, $id)
    {
        $databaseimage = Product::select('image')
            ->where('id', '=', $id)
            ->first();
        $imagePath = public_path('product_image/' . $databaseimage['image']);

        if (File::exists($imagePath)) {
            unlink($imagePath);
        }

        $imageName = time() . '.' . $req['image']->getClientOriginalExtension();
        $req['image']->move(public_path('/product_image'), $imageName);

        $data = [
            'name' => $req['name'],
            'author' => auth()->user()->id,
            'description' => $req['description'],
            'price' => $req['price'],
            'image' => $imageName,
            'category' => $req['category'],
        ];

        $productUpdate = Product::find($id);
        $productUpdate->update($data);
        return $productUpdate;
    }
    public function deleteProduct($id)
    {
        $databaseimage = Product::select('image')
            ->where('id', $id)
            ->first();
        $imagePath = public_path('product_image/' . $databaseimage['image']);

        if (File::exists($imagePath)) {
            unlink($imagePath);
        }

        $deleteData = Product::where('id', $id)->delete();
        return $deleteData;
    }
    public function viewProduct($page, $pageLimit, $req)
    {

        $keyGet=array_keys($req);
       


        if (isset($req['filter_by'])) {
            $query = Product::skip($page)
                ->take($pageLimit);

            $match = $req['filter_data'];
           
            $key = 'name';  // colomn name of filter
         

            $value = $req['filter_by'];
            $valueTo = (!empty($req['filter_to'])) ? $req['filter_to'] : 0;

            if ($match == 'equals') {
                $query = $query->where($key, '=', $value);
            } elseif ($match == 'notContains') {
                $query = $query->where(
                    $key,
                    'NOT LIKE',
                    '%' . $value . '%'
                );
            } elseif ($match == 'contains') {
                $query = $query->where($key, 'like', '%' . $value . '%');

            } elseif ($match == 'notEqual') {
                $query = $query->where($key, '!=', $value);
            } elseif ($match == 'startsWith') {
                // return $value;
                $query = $query = $query->where($key, 'like', $value . '%');
            } elseif ($match == 'endsWith') {
                $query = $query->where($key, 'like', '%' . $value);
            }
           
         
                elseif ($match == "inRange") {
                    $query = $query->whereBetween("price", [$value, $valueTo]);
                }
            
            return $query->get();
        }
         













        $viewData = Product::leftJoin(
            'users',
            'users.id',
            '=',
            'products.author'
        )
            ->select('products.*', 'users.name as author')
            ->skip($page)
            ->take($pageLimit)
            ->get();
        return $viewData;
    }
}
