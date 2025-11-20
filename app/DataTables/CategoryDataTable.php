<?php

namespace App\DataTables;

use App\Models\Category;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class CategoryDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<Category> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->filterColumn('name', function ($query, $keyword) {
                $query->where('name', 'like', "%{$keyword}%");
            })
            ->addColumn('products_count', function (Category $category) {
                return $category->products()->count();
            })
            ->addColumn('actions', function (Category $category) {
                $editRoute = route('categories.edit', encrypt($category->id));
                $deleteRoute = route('categories.destroy', $category->id);
                $csrfField = csrf_field();
                $methodField = method_field('DELETE');

                $html = <<<HTML
                    <td class="d-flex gap-2">
                        <a href="{$editRoute}" class="btn btn-sm btn-primary">Edit</a>
                        <form action="{$deleteRoute}" method="POST">
                            {$csrfField}
                            {$methodField}
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this category?')">Delete</button>
                        </form>
                    </td>
                HTML;

                return $html;
            })
            ->addIndexColumn()
            ->setRowId('id')
            ->rawColumns(['actions']);
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<Category>
     */
    public function query(Category $model): QueryBuilder
    {
        return $model->newQuery()->withCount('products');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('categories-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->orderBy(1)
                    ->selectStyleSingle()
                    ->dom('Bfrtip')
                    ->buttons([
                        Button::make('excel')->className('btn btn-light'),
                        Button::make('csv')->className('btn btn-light'),
                        Button::make('pdf')->className('btn btn-light'),
                        Button::make('print')->className('btn btn-light'),
                    ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('id'),
            Column::make('name')->title('Category Name'),
            Column::make('products_count')->title('Products'),
            Column::make('created_at')->title('Created At'),
            Column::computed('actions')
                  ->exportable(false)
                  ->printable(false)
                  ->width(100)
                  ->addClass('text-center'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Categories_' . date('YmdHis');
    }
}
