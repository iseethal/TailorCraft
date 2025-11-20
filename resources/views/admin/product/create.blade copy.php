@extends('layouts.admin')

@section('content')
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet" />

<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>

    {{-- <form id="productForm" action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
  @csrf --}}
    <form id="productForm" method="POST"
        action="{{ isset($obj->id) ? route('products.update', $obj->id) : route('products.store') }}"
        enctype="multipart/form-data">
        @csrf
        @if (isset($obj->id))
            @method('PUT')
        @endif


        <div class="row gx-4">



            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif


            <!-- LEFT: Product details, Colors, Sizes, Variants (variants moved here) -->
            <div class="col-lg-8">
                <div class="card mb-3">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 h5 class="mb-0">Add New Product</h5>
                        <small>Product details & variants</small>
                    </div>


                    <div class="card-body">
                        <div class="row g-3">


                            <!-- Product name & description -->
                            <div class="col-12">
                                <label class="form-label fw-semibold">Product Name</label>
                                <input id="productName" name="product_name" type="text" class="form-control"
                                    placeholder="Cotton Kurta" value="{{ $obj->product_name }}" required>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Short Description</label>
                                <textarea name="short_description" rows="3" class="form-control" placeholder="Short description for product">{{ $obj->product_short_description }}</textarea>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Description</label>
                                <input type="hidden" name="description" id="description"
                                    value="{{ $obj->product_description ?? '' }}" />
                                <div id="editor" style="height: 150px;"></div>

                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Base SKU</label>
                                <input id="baseSku" name="product_sku" type="text" class="form-control"
                                    placeholder="SANSU-001" value="{{ $obj->product_sku }}">
                                <div class="form-text small">Used as prefix for variant SKUs (editable).</div>
                            </div>

                            <div class="col-md-6 text-end">
                                <!-- lightweight product meta -->
                                <!-- <div class="text-muted small mt-4">Created by: <strong class="text-body">Local User</strong></div> -->
                            </div>

                        </div> <!-- /.row -->

                        <hr class="my-4">

                        <!-- toggles -->
                        <div class="row g-2 align-items-center mb-3">
                            <div class="col-auto">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" id="hasColors" type="checkbox"
                                        onchange="toggleColors()">
                                    <label class="form-check-label" for="hasColors">Has Colors</label>
                                </div>
                            </div>

                            <div class="col-auto">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" id="hasSizes" type="checkbox" onchange="toggleSizes()">
                                    <label class="form-check-label" for="hasSizes">Has Sizes</label>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-text small">Enable color or size when product has variants. If none
                                    enabled, single price & stock are used.</div>
                            </div>
                        </div>

                        <!-- COLORS -->
                        <div id="colorsSection" class="mb-3 visually-hidden">
                            <div class="card bg-light p-3 border">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <strong>Colors</strong>
                                    <div>
                                        <button type="button" class="btn btn-sm btn-outline-secondary me-2"
                                            onclick="showAddColorRow()"><i class="bi bi-plus"></i> New</button>
                                        <button type="button" class="btn btn-sm btn-primary"
                                            onclick="addSelectedColor()"><i class="bi bi-check-circle"></i> Add
                                            Selected</button>
                                    </div>
                                </div>

                                <div class="row g-2 mb-2">
                                    <div class="col-md-8">
                                        <select id="existingColorSelect" class="form-select">
                                            <option value="">-- choose existing color --</option>


                                        </select>

                                    </div>
                                    <div class="col-md-4 text-end">
                                        <button type="button" class="btn btn-sm btn-danger"
                                            onclick="clearSelectedColors()">Clear</button>
                                    </div>
                                </div>

                                <div id="selectedColorsList" class="mb-2"></div>

                                <div id="newColorRow" class="row g-2 visually-hidden">
                                    <div class="col-md-5">
                                        <input id="newColorName" class="form-control"
                                            placeholder="Color name (e.g. Maroon)">
                                    </div>
                                    <div class="col-md-3">
                                        <input id="newColorHex" class="form-control form-control-color" type="color"
                                            value="#ffffff">
                                    </div>
                                    <div class="col-md-3">
                                        <input id="newColorSwatch" class="form-control" type="file" accept="image/*">
                                    </div>
                                    <div class="col-md-1 d-grid">
                                        <button type="button" class="btn btn-primary btn-sm"
                                            onclick="addNewColor()">Add</button>
                                    </div>
                                </div>

                                <div class="form-text small mt-2">Select existing colors or add a new one. Color swatch
                                    optional.</div>
                            </div>
                        </div>

                        <!-- SIZES -->
                        <div id="sizesSection" class="mb-3 visually-hidden">
                            <div class="card bg-light p-3 border">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <strong>Sizes</strong>
                                    <div>
                                        <button type="button" class="btn btn-sm btn-outline-secondary me-2"
                                            onclick="showAddSizeRow()"><i class="bi bi-plus"></i> New</button>
                                        <button type="button" class="btn btn-sm btn-primary"
                                            onclick="addSelectedSize()"><i class="bi bi-check2-circle"></i> Add
                                            Selected</button>
                                    </div>
                                </div>

                                <div class="row g-2 mb-2">
                                    <div class="col-md-8">
                                        <select id="existingSizeSelect" class="form-select">
                                            <option value="">-- choose existing size --</option>
                                        </select>
                                    </div>
                                </div>

                                <div id="selectedSizesList" class="mb-2"></div>

                                <div id="newSizeRow" class="row g-2 visually-hidden">
                                    <div class="col-md-8">
                                        <input id="newSizeName" class="form-control"
                                            placeholder="Size (e.g. S, M, L, 30)">
                                    </div>
                                    <div class="col-md-4 d-grid">
                                        <button type="button" class="btn btn-primary"
                                            onclick="addNewSize()">Add</button>
                                    </div>
                                </div>

                                <div class="form-text small mt-2">Add sizes you offer for this product.</div>
                            </div>
                        </div>

                        <!-- VARIANTS (moved immediately after sizes) -->
                        <div id="variantsControlsLeft" class="mb-3 visually-hidden">
                            <div class="d-flex gap-2 align-items-center mb-2">
                                <button type="button" class="btn btn-success btn-sm" onclick="generateVariants()"><i
                                        class="bi bi-list-check"></i> Generate Variants</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm"
                                    onclick="clearVariants()"><i class="bi bi-x-circle"></i> Clear</button>
                                <div class="form-check ms-3">
                                    <input id="skuAutoEnable" class="form-check-input" type="checkbox" checked>
                                    <label class="form-check-label" for="skuAutoEnable">Auto-generate SKU</label>
                                </div>
                            </div>
                            <div class="form-text small">Pattern: <code>BASE-COL-SIZE</code> — editable per row.</div>
                        </div>

                        <div id="variantsSectionLeft" class="visually-hidden">
                            <div class="table-responsive">
                                <table class="table table-sm align-middle" id="variantsTableLeft">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Color</th>
                                            <th>Size</th>
                                            <th>Price</th>
                                            <th>Stock</th>
                                            <th>SKU</th>
                                            <th>Images</th>


                                            <th style="width:60px"></th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                            <div class="form-text small mt-2">Upload images per variant — inputs named
                                <code>variant_images[INDEX][]</code>.</div>
                        </div>

                        @if(isset($variants) && $variants->count() > 0)
                            <div class="mt-3">
                                <h6 class="fw-semibold">Stored Variant Images</h6>

                                <div class="d-flex flex-wrap">
                                    @foreach($variants as $variant)
                                        @foreach($variant->images as $img)
                                            <img src="{{ asset('storage/' . $img->image_path) }}"
                                                style="width:60px;height:60px;object-fit:cover;
                                                        border:1px solid #ccc;border-radius:6px;
                                                        margin:4px;">
                                        @endforeach
                                    @endforeach
                                </div>
                            </div>
                        @endif


                        <!-- Single price/stock fallback -->
                        <div id="singleStockSection" class="card mt-4 p-3">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Price</label>
                                    <input name="price" step="0.01" type="number" class="form-control"
                                        placeholder="999.00">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Stock</label>
                                    <input name="stock" type="number" class="form-control" placeholder="10">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Weight / Unit (opt)</label>
                                    <input name="weight" type="text" class="form-control" placeholder="e.g. 0.5 Kg">
                                </div>
                            </div>
                        </div>

                    </div> <!-- /.card-body -->
                </div> <!-- /.card -->
            </div> <!-- /.col-lg-7 -->


            <!-- RIGHT: Category, Status, Main Image, Gallery -->
            <div class="col-lg-4">
                <div class="card mb-3">
                   <div class="card-header bg-secondary text-white">
                        <h6 class="mb-0">Images & Product Meta</h6>
                    </div>

                    <div class="card-body">

                        <!-- Category -->
<div class="mb-3">
    <label class="form-label fw-semibold">Categories</label>
    <select name="category_ids[]" class="form-select" multiple id="category-select">
        @foreach($categories as $category)
            <option value="{{ $category->id }}"
                {{ in_array($category->id, $productCategories ?? []) ? 'selected' : '' }}>
                {{ $category->name }}
            </option>

            @if($category->children)
                @foreach($category->children as $sub)
                    <option value="{{ $sub->id }}"
                        {{ in_array($sub->id, $productCategories ?? []) ? 'selected' : '' }}>
                        -- {{ $sub->name }}
                    </option>

                    @if($sub->children)
                        @foreach($sub->children as $subsub)
                            <option value="{{ $subsub->id }}"
                                {{ in_array($subsub->id, $productCategories ?? []) ? 'selected' : '' }}>
                                ---- {{ $subsub->name }}
                            </option>
                        @endforeach
                    @endif

                @endforeach
            @endif
        @endforeach
    </select>
</div>







                        <!-- Status -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="active" class="form-select">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>

                        <!-- Main Image -->
                        {{-- <div class="mb-3">
                            <label class="form-label fw-semibold">Main Image</label>
                            <input name="main_image" type="file" accept="image/*" class="form-control">
                            <div class="form-text small">Recommended size: 800×1000 px</div>
                        </div> --}}

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Main Image</label>

                            <!-- File Input -->
                            <input name="main_image" type="file" accept="image/*" class="form-control" id="mainImageInput">

                            <div class="form-text small">Recommended size: 800×1000 px</div>

                            <!-- EXISTING SAVED MAIN IMAGE -->
                            @if(isset($obj) && $obj->primaryImage)
                                @php
                                    $mainImage = $obj->primaryImage->where('is_primary', 1)->first();
                                @endphp

                                @if($mainImage)
                                    <div class="mt-2">
                                        <p class="fw-semibold small">Current Image:</p>
                                        <img src="{{ asset('storage/' . $mainImage->image_path) }}"
                                            alt="Main Image"
                                            class="img-thumbnail"
                                            style="max-height:160px;">
                                    </div>
                                @endif
                            @endif

                            <!-- PREVIEW NEW IMAGE -->
                            <div class="mt-3" id="previewContainer" style="display:none;">
                                <p class="fw-semibold small">Selected Image Preview:</p>
                                <img id="previewImage" class="img-thumbnail" style="max-height:160px;">
                            </div>
                        </div>


                        <hr>

                        <!-- Product gallery -->
                      <div class="mb-3">
                        <label class="form-label fw-semibold">Product Images (Gallery)</label>

                        <input id="galleryInput" name="product_images[]" type="file" class="form-control" accept="image/*" multiple>

                        <div class="form-text small">
                            Upload multiple images — first will be used as product listing image.
                        </div>

                        <div id="galleryPreview" class="d-flex flex-wrap gap-2 mt-2"></div>

                       @if(isset($obj) && $obj->galleryImages->count() > 0)
    <label class="form-label fw-semibold mt-3">Existing Gallery Images</label>

  <div id="sortableGallery" class="d-flex flex-wrap gap-2">
    @foreach($obj->galleryImages->sortBy('position') as $image)
        <div class="gallery-item"
             data-id="{{ $image->id }}"
             style="
                width: 90px;
                height: 90px;
                border: 1px solid #ddd;
                border-radius: 6px;
                overflow: hidden;
                padding: 3px;
                cursor: grab;
            ">
            <img src="{{ asset('storage/' . $image->image_path) }}"
                 style="width:100%; height:100%; object-fit:cover;">
        </div>
    @endforeach
</div>

<button id="saveGalleryOrder" class="btn btn-primary btn-sm mt-3">
    Save Order
</button>

@endif



                    </div>


                        <!-- Product gallery -->



                    </div> <!-- /.card-body -->

                    <div class="card-footer text-end">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Save Product</button>
                    </div>
                </div>
            </div> <!-- /.col-lg-5 -->

        </div> <!-- /.row -->

        <!-- hidden container for variant meta hidden inputs -->
        <div id="variantInputsContainer"></div>
        <div id="deletedVariantsContainer"></div>

    </form>

@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>




    <script>
        const quill = new Quill('#editor', {
            theme: 'snow'
        });


        $('#productForm').validate({
            ignore: [], // don't ignore hidden fields like description
            rules: {
                product_name: {
                    required: true,
                },

            },
            messages: {
                product_name: "Product name cannot be blank",
            },
            errorClass: "text-danger",
            errorPlacement: function(error, element) {
                if (element.attr("name") === "description") {
                    error.insertAfter("#editor"); // show error below Quill
                } else {
                    error.insertAfter(element);
                }
            },
            submitHandler: function(form) {
                // Copy Quill content into hidden input before submit
                $('#description').val(quill.root.innerHTML);
                form.submit();
            }
        });

        // Set initial content for edit
        const descriptionInput = document.getElementById('description');
        if (descriptionInput.value) {
            quill.root.innerHTML = descriptionInput.value;
        }

        // Copy Quill content into hidden input before submitting form
        document.getElementById('productForm').addEventListener('submit', function(e) {
            descriptionInput.value = quill.root.innerHTML;
        });


        /* Server: inject these arrays via Blade
           const existingColors = {!! json_encode($existingColors ?? []) !!};
           const existingSizes  = {!! json_encode($existingSizes ?? []) !!};
        */
        const existingColors = {!! json_encode($existingColors ?? []) !!};
        const existingSizes = {!! json_encode($existingSizes ?? []) !!};



        document.addEventListener('DOMContentLoaded', () => {
            const csel = document.getElementById('existingColorSelect');
            existingColors.forEach(c => {
                const opt = document.createElement('option');
                opt.value = c.id;
                opt.textContent = `${c.color_name}${c.color_hex ? ' ('+c.color_hex+')' : ''}`;
                opt.dataset.name = c.color_name;
                opt.dataset.hex = c.color_hex || '';
                csel.appendChild(opt);
            });

            const ssel = document.getElementById('existingSizeSelect');
            existingSizes.forEach(s => {
                const opt = document.createElement('option');
                opt.value = s.id;
                opt.textContent = s.size_name;
                opt.dataset.name = s.size_name;
                ssel.appendChild(opt);
            });

            // Prefill variants
            if (existingVariants.length > 0) {
                document.getElementById('hasColors').checked = existingVariants.some(v => v.color_id);
                document.getElementById('hasSizes').checked = existingVariants.some(v => v.size_id);
                toggleColors();
                toggleSizes();

                const tbody = document.querySelector('#variantsTableLeft tbody');
                const metaContainer = document.getElementById('variantInputsContainer');
                const selectedColorsList = document.getElementById('selectedColorsList');
                const selectedSizesList = document.getElementById('selectedSizesList');

                const addedColorIds = [];
                const addedSizeIds = [];

                existingVariants.forEach((v, idx) => {
                    // Only add variant row once
                    const tr = document.createElement('tr');
                    tr.dataset.variantIndex = idx;
                    const colorName = v.color ? escapeHtml(v.color.color_name) : '';
                    const sizeName = v.size ? escapeHtml(v.size.size_name) : escapeHtml(v.size_text ?? '');
                    tr.innerHTML = `
        <td>${colorName}</td>
        <td>${sizeName}</td>
        <td><input required type="number" step="0.01" name="variants[${idx}][price]" class="form-control form-control-sm" value="${v.price}"></td>
        <td><input required type="number" name="variants[${idx}][stock]" class="form-control form-control-sm" value="${v.stock}"></td>
        <td><input type="text" name="variants[${idx}][sku]" class="form-control form-control-sm" value="${v.sku}"></td>
        <td><input type="file" name="variant_images[${idx}][]" accept="image/*" multiple class="form-control form-control-sm"></td>
        <td class="text-end"><button type="button" class="btn btn-sm btn-outline-danger" onclick="removeVariantRow(this)">✕</button></td>
      `;
                    tbody.appendChild(tr);

                    // Hidden meta
                    const metaDiv = document.createElement('div');
                    metaDiv.innerHTML = `
         <input type="hidden" name="variants[${idx}][id]" value="${v.id}">
         <input type="hidden" name="variant_ids[${idx}]" value="${v.id}">
         <input type="hidden" name="variant_color_ids[${idx}]" value="${v.color_id ?? ''}">
        <input type="hidden" name="variants[${idx}][color_key]" value="${v.color_id ?? ''}">
        <input type="hidden" name="variants[${idx}][color_name]" value="${colorName}">
        <input type="hidden" name="variants[${idx}][size_key]" value="${v.size_id ?? ''}">
        <input type="hidden" name="variants[${idx}][size_name]" value="${sizeName}">
      `;
                    metaContainer.appendChild(metaDiv);

                    // Only add color chip once
                    if (v.color_id && !addedColorIds.includes(v.color_id)) {
                        const div = document.createElement('div');
                        div.className = 'd-inline-block me-2 mb-2 color-chip';
                        div.dataset.id = v.color_id;
                        div.innerHTML =
                            `<input type="hidden" name="selected_colors[]" value="${v.color_id}"><div class="border rounded py-1 px-2 d-flex align-items-center">
            ${v.color.color_hex ? `<span style="width:18px;height:18px;border-radius:50%;background:${v.color.color_hex};margin-right:8px;border:1px solid #ddd"></span>` : ''}
            <span class="me-2">${colorName}</span>
            <button type="button" class="btn btn-sm btn-outline-danger ms-1" onclick="removeSelectedColor(this)">✕</button></div>`;
                        selectedColorsList.appendChild(div);
                        addedColorIds.push(v.color_id);
                    }

                    // Only add size chip once
                    const sizeKey = v.size_id ?? v.size_text;
                    if (sizeKey && !addedSizeIds.includes(sizeKey)) {
                        const div = document.createElement('div');
                        div.className = 'd-inline-block me-2 mb-2 size-chip';
                        div.dataset.id = v.size_id ?? `text_${v.size_text}`;
                        div.innerHTML =
                            `<input type="hidden" name="selected_sizes[]" value="${v.size_id ?? v.size_text}"><div class="border rounded py-1 px-2 d-flex align-items-center">
            <span class="me-2">${v.size ? v.size.size_name : v.size_text}</span>
            <button type="button" class="btn btn-sm btn-outline-danger ms-1" onclick="removeSelectedSize(this)">✕</button></div>`;
                        selectedSizesList.appendChild(div);
                        addedSizeIds.push(sizeKey);
                    }
                });

                refreshVariantsControls();
            }
        });


        /* UI toggles */
        function toggleColors() {
            document.getElementById('colorsSection').classList.toggle('visually-hidden', !document.getElementById(
                'hasColors').checked);
            refreshVariantsControls();
        }

        function toggleSizes() {
            document.getElementById('sizesSection').classList.toggle('visually-hidden', !document.getElementById('hasSizes')
                .checked);
            refreshVariantsControls();
        }

        function showAddColorRow() {
            document.getElementById('newColorRow').classList.remove('visually-hidden');
        }

        function showAddSizeRow() {
            document.getElementById('newSizeRow').classList.remove('visually-hidden');
        }

        /* Colors */
        function addSelectedColor() {
            const sel = document.getElementById('existingColorSelect');
            if (!sel.value) return alert('Choose a color to add.');
            const id = sel.value;
            if (document.querySelector(`#selectedColorsList .color-chip[data-id="${id}"]`)) return alert(
                'Color already added.');
            const name = sel.selectedOptions[0].dataset.name || sel.selectedOptions[0].text;
            const hex = sel.selectedOptions[0].dataset.hex || '';
            const div = document.createElement('div');
            div.className = 'd-inline-block me-2 mb-2 color-chip';
            div.dataset.id = id;
            div.innerHTML = `
    <input type="hidden" name="selected_colors[]" value="${id}">
    <div class="border rounded py-1 px-2 d-flex align-items-center">
      ${hex ? `<span style="width:18px;height:18px;border-radius:50%;background:${hex};display:inline-block;margin-right:8px;border:1px solid #ddd"></span>` : ''}
      <span class="me-2">${escapeHtml(name)}</span>
      <button type="button" class="btn btn-sm btn-outline-danger ms-1" onclick="removeSelectedColor(this)">✕</button>
    </div>`;
            document.getElementById('selectedColorsList').appendChild(div);
            refreshVariantsControls();
        }

        function addNewColor() {
            const name = document.getElementById('newColorName').value.trim();
            if (!name) return alert('Color name required');
            const hex = document.getElementById('newColorHex').value || '';
            const uniqueId = 'new_' + Date.now();
            const div = document.createElement('div');
            div.className = 'd-inline-block me-2 mb-2 color-chip';
            div.dataset.id = uniqueId;
            div.innerHTML = `
    <input type="hidden" name="selected_colors[]" value="${uniqueId}">
    <input type="hidden" name="new_colors[${uniqueId}][name]" value="${escapeHtml(name)}">
    <input type="hidden" name="new_colors[${uniqueId}][hex]" value="${escapeHtml(hex)}">
    <div class="border rounded py-1 px-2 d-flex align-items-center">
      ${hex ? `<span style="width:18px;height:18px;border-radius:50%;background:${hex};display:inline-block;margin-right:8px;border:1px solid #ddd"></span>` : ''}
      <span class="me-2">${escapeHtml(name)}</span>
      <button type="button" class="btn btn-sm btn-outline-danger ms-1" onclick="removeSelectedColor(this)">✕</button>
    </div>`;
            document.getElementById('selectedColorsList').appendChild(div);

            // for reliable swatch upload for new colors, add explicit file inputs per new color if needed
            document.getElementById('newColorName').value = '';
            document.getElementById('newColorHex').value = '#ffffff';
            document.getElementById('newColorRow').classList.add('visually-hidden');
            refreshVariantsControls();
        }

        function removeSelectedColor(btn) {
            btn.closest('.color-chip').remove();
            refreshVariantsControls();
        }

        function clearSelectedColors() {
            document.getElementById('selectedColorsList').innerHTML = '';
            refreshVariantsControls();
        }

        /* Sizes */
        function addSelectedSize() {
            const sel = document.getElementById('existingSizeSelect');
            if (!sel.value) return alert('Choose a size to add.');
            const id = sel.value;
            if (document.querySelector(`#selectedSizesList .size-chip[data-id="${id}"]`)) return alert(
                'Size already added.');
            const name = sel.selectedOptions[0].dataset.name || sel.selectedOptions[0].text;
            const div = document.createElement('div');
            div.className = 'd-inline-block me-2 mb-2 size-chip';
            div.dataset.id = id;
            div.innerHTML = `
    <input type="hidden" name="selected_sizes[]" value="${id}">
    <div class="border rounded py-1 px-2 d-flex align-items-center">
      <span class="me-2">${escapeHtml(name)}</span>
      <button type="button" class="btn btn-sm btn-outline-danger ms-1" onclick="removeSelectedSize(this)">✕</button>
    </div>`;
            document.getElementById('selectedSizesList').appendChild(div);
            refreshVariantsControls();
        }

        function addNewSize() {
            const name = document.getElementById('newSizeName').value.trim();
            if (!name) return alert('Size name required');
            const uniqueId = 'new_' + Date.now();
            const div = document.createElement('div');
            div.className = 'd-inline-block me-2 mb-2 size-chip';
            div.dataset.id = uniqueId;
            div.innerHTML = `
    <input type="hidden" name="selected_sizes[]" value="${uniqueId}">
    <input type="hidden" name="new_sizes[${uniqueId}][name]" value="${escapeHtml(name)}">
    <div class="border rounded py-1 px-2 d-flex align-items-center">
      <span class="me-2">${escapeHtml(name)}</span>
      <button type="button" class="btn btn-sm btn-outline-danger ms-1" onclick="removeSelectedSize(this)">✕</button>
    </div>`;
            document.getElementById('selectedSizesList').appendChild(div);
            document.getElementById('newSizeName').value = '';
            document.getElementById('newSizeRow').classList.add('visually-hidden');
            refreshVariantsControls();
        }

        function removeSelectedSize(btn) {
            btn.closest('.size-chip').remove();
            refreshVariantsControls();
        }

        /* Variants creation, SKU & per-variant images (variants table now #variantsTableLeft) */
        function refreshVariantsControls() {
            const show = document.getElementById('hasColors').checked || document.getElementById('hasSizes').checked;
            document.getElementById('variantsControlsLeft').classList.toggle('visually-hidden', !show);
            const variantsExist = document.querySelectorAll('#variantsTableLeft tbody tr').length > 0;
            document.getElementById('variantsSectionLeft').classList.toggle('visually-hidden', !variantsExist);
            document.getElementById('singleStockSection').classList.toggle('visually-hidden', variantsExist);
        }

      function generateVariants() {
    const colorChips = Array.from(document.querySelectorAll('#selectedColorsList .color-chip'));
    const sizeChips = Array.from(document.querySelectorAll('#selectedSizesList .size-chip'));

    const colors = colorChips.map(c => {
        const id = c.dataset.id;
        const nameInput = c.querySelector(`input[name^="new_colors"]`);
        let name;
        if (id && !id.startsWith('new_')) {
            const found = existingColors.find(x => String(x.id) === String(id));
            name = found ? found.color_name || found.name : c.textContent.trim();
        } else {
            name = nameInput ? nameInput.value : c.textContent.trim();
        }
        return { id: id, name: name };
    });

    const sizes = sizeChips.map(s => {
        const id = s.dataset.id;
        const nameInput = s.querySelector(`input[name^="new_sizes"]`);
        let name;
        if (id && !id.startsWith('new_')) {
            const found = existingSizes.find(x => String(x.id) === String(id));
            name = found ? found.size_name || found.name : s.textContent.trim();
        } else {
            name = nameInput ? nameInput.value : s.textContent.trim();
        }
        return { id: id, name: name };
    });

    const hasColors = document.getElementById('hasColors').checked;
    const hasSizes = document.getElementById('hasSizes').checked;
    if (hasColors && colors.length === 0) return alert('Please add at least one color.');
    if (hasSizes && sizes.length === 0) return alert('Please add at least one size.');

    const combos = [];
    if (hasColors && hasSizes) {
        colors.forEach(c => sizes.forEach(s => combos.push({ color: c, size: s })));
    } else if (hasColors) {
        colors.forEach(c => combos.push({ color: c, size: null }));
    } else {
        sizes.forEach(s => combos.push({ color: null, size: s }));
    }

    const tbody = document.querySelector('#variantsTableLeft tbody');
    tbody.innerHTML = '';
    const metaContainer = document.getElementById('variantInputsContainer');
    metaContainer.innerHTML = '';

    const baseSkuRaw = (document.getElementById('baseSku').value || '').trim();
    const baseSku = baseSkuRaw || slugify(document.getElementById('productName').value || 'SKU');

    combos.forEach((combo, idx) => {
        const colorName = combo.color ? combo.color.name : '';
        const sizeName = combo.size ? combo.size.name : '';

        // ---------------------------------------------
        // ✅ CHECK EXISTING VARIANT
        // ---------------------------------------------
                let existingVariant = existingVariants.find(v => {
            const vColorId = v.color_id ?? null;
            const vSizeId  = v.size_id ?? null;

            const curColorId = combo.color ? combo.color.id : null;
            const curSizeId  = combo.size ? combo.size.id : null;

            return String(vColorId) === String(curColorId) &&
                String(vSizeId) === String(curSizeId);
        });


        const variantId = existingVariant ? existingVariant.id : '';
        const storedImages = existingVariant ? existingVariant.images : [];

        const price = existingVariant ? existingVariant.price : '';
        const stock = existingVariant ? existingVariant.stock : '';
        const sku = existingVariant
            ? existingVariant.sku
            : generateVariantSku(baseSku, colorName, sizeName, idx + 1);

        const tr = document.createElement('tr');
        tr.dataset.variantIndex = idx;

        tr.innerHTML = `

            <input type="hidden" name="variants[${idx}][id]" value="${variantId}">

            <td>${escapeHtml(colorName)}</td>
            <td>${escapeHtml(sizeName)}</td>

            <td>
                <input required type="number" step="0.01" name="variants[${idx}][price]"
                    class="form-control form-control-sm" value="${price}">
            </td>

            <td>
                <input required type="number" name="variants[${idx}][stock]"
                    class="form-control form-control-sm" value="${stock}">
            </td>

            <td>
                <input type="text" name="variants[${idx}][sku]"
                    class="form-control form-control-sm" value="${escapeHtml(sku)}">
            </td>

           <td>
            <input type="file" name="variant_images[${idx}][]" accept="image/*"
                multiple class="form-control form-control-sm">

            ${
                storedImages.length
                ? `<div class="mt-1 small text-muted">
                        ${storedImages.map(img => `• ${img.image}`).join('<br>')}
                </div>`
                : `<div class="mt-1 small text-muted">No images</div>`
            }
        </td>


            <td class="text-end">
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeVariantRow(this)">✕</button>
            </td>
        `;

        tbody.appendChild(tr);

        const metaDiv = document.createElement('div');
        metaDiv.innerHTML = `
            <input type="hidden" name="variants[${idx}][color_key]" value="${escapeHtml(combo.color ? combo.color.id : '')}">
            <input type="hidden" name="variants[${idx}][color_name]" value="${escapeHtml(colorName)}">
            <input type="hidden" name="variants[${idx}][size_key]" value="${escapeHtml(combo.size ? combo.size.id : '')}">
            <input type="hidden" name="variants[${idx}][size_name]" value="${escapeHtml(sizeName)}">
        `;
        metaContainer.appendChild(metaDiv);
    });

    refreshVariantsControls();
}


        function removeVariantRow(btn) {
            const tr = btn.closest('tr');
            if (!tr) return;

            // If this row represents an existing variant, store its ID in deleted array
            const variantIndex = tr.dataset.variantIndex;
            if (variantIndex !== undefined && existingVariants[variantIndex]?.id) {
                const deletedContainer = document.getElementById('deletedVariantsContainer');
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'deleted_variants[]';
                input.value = existingVariants[variantIndex].id; // store DB ID
                deletedContainer.appendChild(input);
            }

            // Remove row
            tr.remove();

            // Reindex remaining variants
            reindexVariantsLeft();
            refreshVariantsControls();
        }


        function clearVariants() {
            document.querySelector('#variantsTableLeft tbody').innerHTML = '';
            document.getElementById('variantInputsContainer').innerHTML = '';
            refreshVariantsControls();
        }

        /* Reindex so variant_images[...] mapping remains aligned after deletions */
        function reindexVariantsLeft() {
            const tbody = document.querySelector('#variantsTableLeft tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));
            const metaContainer = document.getElementById('variantInputsContainer');
            metaContainer.innerHTML = '';

            rows.forEach((row, index) => {
                row.querySelectorAll('input, select, textarea').forEach(inp => {
                    if (!inp.name) return;
                    inp.name = inp.name.replace(/variants\[\d+\]/, `variants[${index}]`);
                });
                const fileInput = row.querySelector('input[type="file"]');
                if (fileInput) fileInput.name = `variant_images[${index}][]`;

                const colorCell = row.cells[0].textContent.trim();
                const sizeCell = row.cells[1].textContent.trim();
                const metaDiv = document.createElement('div');
                metaDiv.innerHTML = `
      <input type="hidden" name="variants[${index}][color_key]" value="">
      <input type="hidden" name="variants[${index}][color_name]" value="${escapeHtml(colorCell)}">
      <input type="hidden" name="variants[${index}][size_key]" value="">
      <input type="hidden" name="variants[${index}][size_name]" value="${escapeHtml(sizeCell)}">
    `;
                metaContainer.appendChild(metaDiv);
            });
        }

        /* SKU helpers */
        function generateVariantSku(baseSku, colorName, sizeName, idx) {
            if (!document.getElementById('skuAutoEnable').checked) return `${baseSku}-${idx}`;
            const colShort = colorName ? sanitizeForSku(colorName).slice(0, 3).toUpperCase() : 'NA';
            const sizePart = sizeName ? sanitizeForSku(sizeName).toUpperCase() : 'OS';
            return `${baseSku}-${colShort}-${sizePart}`;
        }

        function sanitizeForSku(s) {
            return (s || '').toString().replace(/\s+/g, '-').replace(/[^A-Za-z0-9\-]/g, '');
        }

        function slugify(s) {
            return (s || '').toString().toUpperCase().replace(/[^A-Z0-9]+/g, '-').replace(/(^-|-$)/g, '') || 'SKU';
        }

        /* utility */
        function escapeHtml(text) {
            return (text + '').replace(/[&<>"']/g, function(m) {
                return {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#39;'
                } [m];
            });
        }


        const existingVariants = {!! json_encode($variants ?? []) !!};
    </script>
<script>
$(document).ready(function() {
    $('#category-select').select2({
        placeholder: "Select categories",
        allowClear: true,
        width: '100%',
        // Enable search box
        minimumResultsForSearch: 0
    });
});
</script>

<script>
document.getElementById('mainImageInput').addEventListener('change', function (event) {
    const file = event.target.files[0];
    const previewContainer = document.getElementById('previewContainer');
    const previewImage = document.getElementById('previewImage');

    if (file) {
        previewContainer.style.display = "block";
        previewImage.src = URL.createObjectURL(file);
    } else {
        previewContainer.style.display = "none";
        previewImage.src = "";
    }
});
</script>


<script>
 // image gallery preview
document.getElementById('galleryInput').addEventListener('change', function(event) {
    const previewContainer = document.getElementById('galleryPreview');
    previewContainer.innerHTML = "";

    const files = event.target.files;

    if (!files.length) return;

    [...files].forEach(file => {
        const reader = new FileReader();

        reader.onload = function(e) {
            const imgHtml = `
                <div style="width:90px;height:90px;overflow:hidden;border:1px solid #ddd;border-radius:6px;padding:3px;">
                    <img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover;">
                </div>
            `;
            previewContainer.insertAdjacentHTML('beforeend', imgHtml);
        };

        reader.readAsDataURL(file);
    });
});
</script>
@if(isset($obj) && $obj->id)
<script>
document.addEventListener("DOMContentLoaded", function () {

    let sortable = new Sortable(document.getElementById("sortableGallery"), {
        animation: 150,
    });

    document.getElementById("saveGalleryOrder").addEventListener("click", function () {

        let order = [];

        document.querySelectorAll("#sortableGallery .gallery-item").forEach((item, index) => {
            order.push({
                id: item.dataset.id,
                position: index + 1
            });
        });

        fetch("{{ route('admin.products.update-image-order', $obj->id) }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ order })
        })
        .then(response => response.json())

        .catch(error => console.error("Error:", error));
    });

});
</script>
@endif


<style>
.drag-background {
    opacity: 0.4;
}
</style>

@endsection
