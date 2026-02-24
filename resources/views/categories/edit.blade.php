@extends('layouts.app')

@section('title', 'Edit Category')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">Categories</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.categories.show', $category) }}">{{ $category->name }}</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>

        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-pencil-square"></i> Edit Category</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.categories.update', $category) }}" method="POST">
                    @csrf @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $category->name) }}"
                               class="form-control @error('name') is-invalid @enderror" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Slug</label>
                        <input type="text" name="slug" value="{{ old('slug', $category->slug) }}"
                               class="form-control @error('slug') is-invalid @enderror">
                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Parent Category</label>
                        <select name="parent_id" class="form-select @error('parent_id') is-invalid @enderror">
                            <option value="">— None (Root Category) —</option>
                            @foreach($parents as $parent)
                                <option value="{{ $parent->id }}"
                                    {{ old('parent_id', $category->parent_id) == $parent->id ? 'selected' : '' }}>
                                    {{ $parent->breadcrumb }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" rows="3"
                                  class="form-control">{{ old('description', $category->description) }}</textarea>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Save Changes
                        </button>
                        <a href="{{ route('admin.categories.show', $category) }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
