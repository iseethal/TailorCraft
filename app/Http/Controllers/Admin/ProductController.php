<?php

namespace App\Http\Controllers\Admin;

use App\Models\Size;
use App\Models\Color;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductSize;
use Illuminate\Support\Str;
use App\Models\ProductColor;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use App\Models\ProductVariant;
use App\Http\Controllers\Controller;
use App\DataTables\ProductsDataTable;
use App\Http\Requests\ProductRequest;
use Illuminate\Support\Facades\Storage;


class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    protected $product;
    protected $color;
    protected $size;
    protected $productColor;
    protected $productSize;
    protected $productVariant;
    protected $productImage;

    public function __construct(
        Product $product,
        Color $color,
        Size $size,
        ProductColor $productColor,
        ProductSize $productSize,
        ProductVariant $productVariant,
        ProductImage $productImage
    ) {
        $this->product        = $product;
        $this->color          = $color;
        $this->size           = $size;
        $this->productColor   = $productColor;
        $this->productSize    = $productSize;
        $this->productVariant = $productVariant;
        $this->productImage   = $productImage;
    }

    public function index(ProductsDataTable $dataTable)
    {
        return $dataTable->render('admin.product.index');
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $existingColors = $this->color->select('id','color_name','color_hex')->get();
        $existingSizes  = $this->size->select('id','size_name')->get();
        $obj = $this->product;
        $categories = Category::whereNull('parent_id')->with('children')->get();

        return view('admin.product.create', compact('existingColors', 'existingSizes','obj','categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
   public function store(ProductRequest $request)
    {
        try {
            $data = $request->validated();
            $data['product_description'] = $data['description'] ?? '';
            $data['product_short_description'] = $data['short_description'] ?? '';
            unset($data['description'], $data['short_description']);

            $product = $this->product->create($data);

              if (isset($data['category_ids'])) {
                    $product->categories()->sync($data['category_ids']);
              }

            $productColorIds = [];

            // New Colors
            if ($request->has('new_colors')) {
                foreach ($request->new_colors as $uniqueKey => $colorData) {
                    $color = $this->color->create([
                        'color_name' => $colorData['name'],
                        'color_hex' => $colorData['hex'] ?? null,
                        'color_swatch_image' => null,
                        'status' => 1,
                    ]);
                    $productColorIds[] = $color->id;
                }
            }

            // Selected Colors
            if ($request->has('selected_colors')) {
                foreach ($request->selected_colors as $colorId) {
                    if (is_numeric($colorId)) {
                        $productColorIds[] = $colorId;
                    }
                }
            }

            foreach ($productColorIds as $colorId) {
                $this->productColor->create([
                    'product_id' => $product->id,
                    'color_id' => $colorId,
                    'color_image' => null,
                    'is_default' => 0,
                ]);
            }

            // Sizes
            $productSizeIds = [];
            if ($request->has('new_sizes')) {
                foreach ($request->new_sizes as $uniqueKey => $sizeData) {
                    $size = $this->size->create([
                        'size_name' => $sizeData['name'],
                        'status' => 1,
                    ]);
                    $productSizeIds[] = $size->id;
                }
            }
            if ($request->has('selected_sizes')) {
                foreach ($request->selected_sizes as $sizeId) {
                    if (is_numeric($sizeId)) {
                        $productSizeIds[] = $sizeId;
                    }
                }
            }
            foreach ($productSizeIds as $sizeId) {
                $this->productSize->create([
                    'product_id' => $product->id,
                    'size_id' => $sizeId,
                ]);
            }

            // Variants
            if ($request->has('variants')) {
                foreach ($request->variants as $variant) {
                    $this->productVariant->create([
                        'product_id'   => $product->id,
                        'color_id'     => is_numeric($variant['color_key']) ? $variant['color_key'] : null,
                        'size_id'      => is_numeric($variant['size_key']) ? $variant['size_key'] : null,
                        'size_text'    => $variant['size_name'] ?? null,
                        'price'        => $variant['price'],
                        'discount_price' => $variant['discount_price'] ?? null,
                        'stock'        => $variant['stock'],
                        'sku'          => $variant['sku'],
                        'status'       => 1,
                    ]);
                }
            }

            return redirect()->route('products.create')->with('success', 'Product added successfully.');
        } catch (\Exception $e) {
            dd($e->getMessage(), $e->getFile(), $e->getLine());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */

    public function edit($id)
    {
        $id = decrypt($id);
        if($obj = $this->product->find($id)){
            $obj = $this->product->findOrFail($id);
            $existingColors = $this->color->select('id','color_name','color_hex')->get();
            $existingSizes  = $this->size->select('id','size_name')->get();
            $variants = $obj->variants()->with(['color', 'size','images'])->get();
            $productColors = $variants->pluck('color')->filter()->unique('id')->values();
            $productSizes = $variants->pluck('size')->filter()->unique('id')->values();
            $categories = Category::whereNull('parent_id')->with('children')->get();
            $productCategories = $obj->categories->pluck('id')->toArray();

            return view('admin.product.create', compact('obj', 'existingColors', 'existingSizes','variants','productColors','productSizes','categories','productCategories'));
         } else{
            return redirect('notfound');
        }

    }

    /**
     * Update the specified resource in storage.
     */
   public function update(ProductRequest $request, $id)
    {
        try {
            $data = $request->validated();
            $data['product_description'] = $data['description'] ?? '';
            $data['product_short_description'] = $data['short_description'] ?? '';
            unset($data['description'], $data['short_description']);

            $product = $this->product->findOrFail($id);
            $product->update($data);

             $product->categories()->sync($data['category_ids'] ?? []);

            // COLORS
            $productColorIds = [];

            // New Colors
            if ($request->has('new_colors')) {
                foreach ($request->new_colors as $uniqueKey => $colorData) {
                    $color = $this->color->create([
                        'color_name' => $colorData['name'],
                        'color_hex' => $colorData['hex'] ?? null,
                        'color_swatch_image' => null,
                        'status' => 1,
                    ]);
                    $productColorIds[] = $color->id;
                }
            }

            // Selected Colors (existing)
            if ($request->has('selected_colors')) {
                foreach ($request->selected_colors as $colorId) {
                    if (is_numeric($colorId)) {
                        $productColorIds[] = $colorId;
                    }
                }
            }

            // Sync via belongsToMany
            $product->colorEntities()->sync($productColorIds);

            // SIZES
            $productSizeIds = [];

            if ($request->has('new_sizes')) {
                foreach ($request->new_sizes as $uniqueKey => $sizeData) {
                    $size = $this->size->create([
                        'size_name' => $sizeData['name'],
                        'status' => 1,
                    ]);
                    $productSizeIds[] = $size->id;
                }
            }

            if ($request->has('selected_sizes')) {
                foreach ($request->selected_sizes as $sizeId) {
                    if (is_numeric($sizeId)) {
                        $productSizeIds[] = $sizeId;
                    }
                }
            }

            // Sync product sizes (assume you have belongsToMany for sizes too)
            if (method_exists($product, 'sizeEntities')) {
                $product->sizeEntities()->sync($productSizeIds);
            } else {
                // fallback: hasMany approach
                $this->productSize->where('product_id', $product->id)->delete();
                foreach ($productSizeIds as $sizeId) {
                    $this->productSize->create([
                        'product_id' => $product->id,
                        'size_id' => $sizeId,
                    ]);
                }
            }


            // VARIANTS
            // Delete variants

           if ($request->has('deleted_variants')) {
                $this->productVariant
                    ->whereIn('id', $request->deleted_variants)
                    ->delete();
            }

            $variantIdMap = []; // key = form index, value = DB ID

            // 2. Update or create variants
            if ($request->has('variants')) {
                foreach ($request->variants as $index => $variant) {

                    if (!empty($variant['id'])) {
                        // UPDATE EXISTING
                        $pv = $this->productVariant->find($variant['id']);
                        if ($pv) {
                            $pv->update([
                                'color_id'  => is_numeric($variant['color_key']) ? $variant['color_key'] : null,
                                'size_id'   => is_numeric($variant['size_key']) ? $variant['size_key'] : null,
                                'size_text' => $variant['size_name'] ?? null,
                                'price'     => $variant['price'],
                                'discount_price' => $variant['discount_price'] ?? null,
                                'stock'     => $variant['stock'],
                                'sku'       => $variant['sku'],
                                'status'    => 1,
                            ]);

                            $variantIdMap[$index] = $pv->id;
                        }
                    } else {
                        // CREATE NEW
                        $pv = $this->productVariant->create([
                            'product_id' => $product->id,
                            'color_id'   => is_numeric($variant['color_key']) ? $variant['color_key'] : null,
                            'size_id'    => is_numeric($variant['size_key']) ? $variant['size_key'] : null,
                            'size_text'  => $variant['size_name'] ?? null,
                            'price'      => $variant['price'],
                            'discount_price' => $variant['discount_price'] ?? null,
                            'stock'      => $variant['stock'],
                            'sku'        => $variant['sku'],
                            'status'     => 1,
                        ]);

                        $variantIdMap[$index] = $pv->id;
                    }
                }
            }

            // save main image
           $this->uploadMainImage($request, $product);

            // save gallery
           $this->uploadGalleryImages($request, $product);

          //   save variant images
           $this->uploadVariantImages($request, $product);


            return redirect()->back()->with('success', 'Product updated successfully.');

        } catch (\Exception $e) {
            dd($e->getMessage(), $e->getFile(), $e->getLine());
        }
    }


    private function uploadMainImage(Request $request, Product $product)
    {
        if (!$request->hasFile('main_image')) {
            return;
        }

        $image = $request->file('main_image');
        $path = $image->store('products', 'public');

        $oldMainImage = ProductImage::where('product_id', $product->id)
                                    ->where('is_primary', 1)
                                    ->first();

        if ($oldMainImage) {
            Storage::disk('public')->delete($oldMainImage->image_path);

            $oldMainImage->delete();
        }

        ProductImage::create([
            'product_id' => $product->id,
            'image_path' => $path,
            'is_primary' => 1,
            'status' => 1,
            'position' => 1,
        ]);
    }

    private function uploadGalleryImages(Request $request, Product $product)
    {
        if (!$request->hasFile('product_images')) {
            return;
        }
        foreach ($request->file('product_images') as $image) {

            $path = $image->store('products/gallery', 'public');

            ProductImage::create([
                'product_id' => $product->id,
                'image_path' => $path,
                'is_primary' => 0,
                'status' => 1,
            ]);
        }
    }

private function uploadVariantImages(Request $request, Product $product)
{
    if (!$request->has('variant_images')) {
        return;
    }

    foreach ($request->variant_images as $key => $imageGroup) {

        // Get Color ID
        $productColorId = $request->variant_color_ids[$key]
                          ?? ($request->variant_colors[$key] ?? null)
                          ?? 0;

        // Get Variant ID (only if exists — edit case)
        $productVariantId = $request->variants[$key]['id'] ?? null;

        foreach ($imageGroup as $index => $imageFile) {

            if (!$imageFile instanceof \Illuminate\Http\UploadedFile) {
                continue;
            }

            // SAVE FILE
            $path = $imageFile->store('products/variant', 'public');

            // INSERT INTO product_images TABLE
            ProductImage::create([
                'product_id'         => $product->id,
                'product_color_id'   => $productColorId,
                'product_variant_id' => $productVariantId, // WILL NOT BE NULL NOW
                'image_path'         => $path,
                'is_primary'         => 0,
                'position'           => $index + 1,
                'status'             => 1,
            ]);
        }
    }
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function updateImageOrder(Request $request, $id)
    {
        foreach ($request->order as $item) {
            ProductImage::where('id', $item['id'])
                ->update(['position' => $item['position']]);
        }

        return response()->json(['message' => 'Image order updated']);
    }

}
