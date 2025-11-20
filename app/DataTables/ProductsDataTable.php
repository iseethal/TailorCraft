<?php

namespace App\DataTables;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class ProductsDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<Product> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->filterColumn('product_name', function ($query, $keyword) {
                $query->where('product_name', 'like', "%{$keyword}%");
                $query->orWhere('product_sku', 'like', "%{$keyword}%");
            })
            ->addColumn('actions', function (Product $product) {
                $editRoute = route('products.edit', encrypt($product->id));
                $deleteRoute = route('products.destroy', $product->id);
                $csrfField = csrf_field();
                $methodField = method_field('DELETE');

                // Using HEREDOC syntax for cleaner multi-line HTML
                $html = <<<HTML
                    <td class="d-flex gap-2">
                        <div class="d-flex gap-2">
                        <a href="{$editRoute}" data-status-id="{$product->id}" data-status-name="{$product->product_name}" data-status="{$product->status}" class=""><i class="hgi hgi-stroke hgi-pencil-edit-01"></i></a>

                        <form action="{$deleteRoute}" method="POST">
                            {$csrfField}
                            {$methodField}
                            <button type="submit" class="" onclick="return confirm('Are you sure you want to delete this product?')" style="background-color: transparent;border: none;"><i class="hgi hgi-stroke hgi-delete-03"></i></button>
                        </form>
                        </div>
                    </td>
                HTML;
                return $html;
            })
            ->addColumn('product_name', function (Product $product) {
                $url = route('products.show', $product->id);
                $name = ucwords($product->product_name);
                return "<a href='{$url}'>{$name}</a>";
            })
            ->addColumn('variants', function (Product $product) {
                return $product->variants->count();
            })
            ->addColumn('sku', function (Product $product) {
                return $product->product_sku;
            })
            ->addColumn('created_at', function (Product $product) {
                return $product->created_at->format('Y-m-d');
            })
            ->addColumn('price', function (Product $product) {
                return env('CURRENCY_SYMBOL', '₹') . $product->variants->first()->price;
            })
            ->addColumn('stock', function (Product $product) {
                return $product->variants->first()->stock;
            })
            ->addIndexColumn(['product_name'])
            ->setRowId('id')
            ->rawColumns(['actions', 'product_name']);
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<Product>
     */
    public function query(Product $model): QueryBuilder
    {
        return $model->newQuery()->with('variants');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('products-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->orderBy(1)
                    ->selectStyleSingle()
                    ->dom('Bfrtip')
                    ->buttons([
                        Button::make('excel', 'Excel')->exportOptions([
                            'columns' => [0, 1, 2, 3, 4, 5, 6],
                        ])->className('btn btn-light'),
                        Button::make('csv', 'CSV')->exportOptions([
                            'columns' => [0, 1, 2, 3, 4, 5, 6],
                        ])->className('btn btn-light'),
                        Button::make('pdf', 'PDF')->exportOptions([
                            'columns' => [0, 1, 2, 3, 4, 5, 6],
                        ])->className('btn btn-light'),
                        Button::make('print', 'Print')->exportOptions([
                            'columns' => [0, 1, 2, 3, 4, 5, 6],
                        ])->className('btn btn-light'),
                        //Button::make('reset'),
                        //Button::make('reload')
                    ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('id'),
            Column::make('product_name'),
            Column::make('sku'),
            Column::make('variants'),
            Column::make('price'),
            Column::make('stock'),
            Column::make('created_at'),
            Column::computed('actions')
                  ->exportable(false)
                  ->printable(false)
                  ->width(60)
                  ->addClass('text-center'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Products_' . date('YmdHis');
    }
}
