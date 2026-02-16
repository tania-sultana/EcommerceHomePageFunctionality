<?php

namespace App\Repositories;

use App\Models\Product;
use Arafat\LaravelRepository\Repository;
use Illuminate\Http\Request;

class ProductRepository extends Repository
{
    /**
     * base method
     *
     * @method model()
     */
    public static function model()
    {
        return Product::class;
    }

    public static function storeByRequest(Request $request): Product
    {
        $media = null;
        if ($request->hasFile('thumbnail')) {
            $media = MediaRepository::storeByRequest($request->thumbnail, 'product');
        }

        return self::create([
            'name' => $request->name,
            'price' => $request->price,
            'short_description' => $request->short_description,
            'media_id' => $media ? $media->id : null,
        ]);

        return $product;
    }

    public static function updateByRequest(Request $request, Product $product)
    {
        $media = $product->media;

        if ($request->hasFile('thumbnail') && $media) {
            $media = MediaRepository::updateByRequest(
                $request->file('thumbnail'),
                'products',
                'image',
                $media
            );
        } elseif ($request->hasFile('thumbnail') && ! $media) {
            $media = MediaRepository::storeByRequest(
                $request->file('thumbnail'),
                'products',
                'image'
            );
        }

        self::update($product, [
            'name' => $request->name,
            'price' => $request->price,
            'short_description' => $request->short_description,
            'media_id' => $media?->id ?? $product->media_id,
        ]);
    }
}
