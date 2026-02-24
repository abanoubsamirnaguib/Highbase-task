@extends('layouts.app')

@section('title', 'Attributes — ' . $category->name)

@section('content')
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">Categories</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.categories.show', $category) }}">{{ $category->name }}</a></li>
        <li class="breadcrumb-item active">Attributes</li>
    </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">
        <i class="bi bi-list-check"></i>
        Attributes for <span class="text-primary">{{ $category->name }}</span>
    </h4>
    <a href="{{ route('admin.categories.attributes.create', $category) }}" class="btn btn-success">
        <i class="bi bi-plus-lg"></i> Add Attribute
    </a>
</div>

@if($category->attributes->isEmpty())
    <div class="text-center py-5 text-muted">
        <i class="bi bi-list-check display-4 d-block mb-3"></i>
        No attributes yet. Add attributes to enforce required fields when creating products.
    </div>
@else
    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Required?</th>
                        <th>Options</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($category->attributes as $attr)
                    <tr>
                        <td class="fw-semibold">{{ $attr->name }}</td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $attr->type }}</span>
                        </td>
                        <td>
                            @if($attr->is_required)
                                <span class="badge bg-danger"><i class="bi bi-check-lg"></i> Yes</span>
                            @else
                                <span class="badge bg-secondary">No</span>
                            @endif
                        </td>
                        <td>
                            @if($attr->options)
                                <span class="small text-muted">{{ implode(', ', $attr->options) }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.categories.attributes.edit', [$category, $attr]) }}"
                               class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.categories.attributes.destroy', [$category, $attr]) }}"
                                  method="POST" class="d-inline"
                                  onsubmit="return confirm('Delete this attribute? Existing product values will be removed.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
@endsection
