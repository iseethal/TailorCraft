@extends('layouts.admin')

@section('content')
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet" />

    <div class="container py-4">

        <form id="categoryForm" method="POST"
            action="{{ isset($obj->id) ? route('categories.update', $obj->id) : route('categories.store') }}"
            enctype="multipart/form-data">
            @csrf
            @if (isset($obj->id))
                @method('PUT')
            @endif

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

            <div class="row g-4">

                <!-- LEFT: Category Details -->
                <div class="col-lg-8">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Category Details</h5>
                            <small>Provide the main category info</small>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="name" class="form-label fw-semibold">Category Name <span
                                        class="text-danger">*</span></label>
                                <input id="name" name="name" type="text" class="form-control"
                                    placeholder="e.g., Cotton Kurta" value="{{ $obj->name ?? '' }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="parent_id" class="form-label fw-semibold">Parent Category</label>
                                <select id="parent_id" name="parent_id" class="form-select">
                                    <option value="">-- Root Category --</option>
                                    @foreach ($categories as $category)
                                        @if ($obj->id !== $category->id)
                                            <!-- Prevent selecting itself -->
                                            <option value="{{ $category->id }}"
                                                {{ old('parent_id', $obj->parent_id ?? '') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>

                                            @if ($category->children)
                                                @foreach ($category->children as $sub)
                                                    @if ($obj->id !== $sub->id)
                                                        <option value="{{ $sub->id }}"
                                                            {{ old('parent_id', $obj->parent_id ?? '') == $sub->id ? 'selected' : '' }}>
                                                            -- {{ $sub->name }}
                                                        </option>

                                                        @if ($sub->children)
                                                            @foreach ($sub->children as $subsub)
                                                                @if ($obj->id !== $subsub->id)
                                                                    <option value="{{ $subsub->id }}"
                                                                        {{ old('parent_id', $obj->parent_id ?? '') == $subsub->id ? 'selected' : '' }}>
                                                                        ---- {{ $subsub->name }}
                                                                    </option>
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                    @endif
                                                @endforeach
                                            @endif
                                        @endif
                                    @endforeach
                                </select>
                            </div>


                            <div class="mb-3">
                                <label for="description" class="form-label fw-semibold">Description</label>
                                <textarea id="description" name="description" class="form-control" rows="4" placeholder="Optional description">{{ $obj->description ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RIGHT: Status & Actions -->
                <div class="col-lg-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-secondary text-white">
                            <h6 class="mb-0">Category Settings</h6>
                        </div>
                        <div class="card-body">

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Status</label>
                                <select name="status" class="form-select">
                                    <option value="1" {{ isset($obj) && $obj->status ? 'selected' : '' }}>Active
                                    </option>
                                    <option value="0" {{ isset($obj) && !$obj->status ? 'selected' : '' }}>Inactive
                                    </option>
                                </select>
                            </div>

                            <!-- Optional: Category image upload -->

                        </div>
                        <div class="card-footer text-end">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-save"></i> {{ isset($obj->id) ? 'Update Category' : 'Add Category' }}
                            </button>
                        </div>
                    </div>
                </div>

            </div> <!-- /.row -->
        </form>
    </div> <!-- /.container -->
@endsection

@section('scripts')
    <script>
        $('#categoryForm').validate({
            rules: {
                name: {
                    required: true,
                    maxlength: 255
                },
            },
            messages: {
                name: {
                    required: "Category name is required",
                    maxlength: "Category name cannot exceed 255 characters"
                },
            },
            errorClass: "text-danger",
            errorPlacement: function(error, element) {
                error.insertAfter(element);
            }
        });
    </script>
@endsection
