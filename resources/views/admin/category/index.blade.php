@extends('layouts.admin')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header d-flex justify-content-between"><h5>Manage Category</h5><a href="{{ route('categories.create') }}" class="btn bg-primary-subtle text-primary">Add New category</a></div>
            <div class="card-body">
                {{ $dataTable->table(['class' => 'table search-table align-middle text-nowrap']) }}
            </div>
        </div>
    </div>
@endsection

@section('scripts')
{{-- Buttons extension --}}
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}
@endsection
