@extends('layouts.app')

@section('title', 'Add Attribute')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">Categories</a></li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.categories.attributes.index', $category) }}">{{ $category->name }} Attributes</a>
                </li>
                <li class="breadcrumb-item active">Add</li>
            </ol>
        </nav>

        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="bi bi-plus-circle"></i>
                    Add Attribute to <span class="text-primary">{{ $category->name }}</span>
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.categories.attributes.store', $category) }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Attribute Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. Size, Color, Weight"
                               class="form-control @error('name') is-invalid @enderror" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Type <span class="text-danger">*</span></label>
                        <select name="type" id="typeSelect"
                                class="form-select @error('type') is-invalid @enderror" required>
                            <option value="text"    {{ old('type', 'text') === 'text'    ? 'selected' : '' }}>Text</option>
                            <option value="number"  {{ old('type') === 'number'  ? 'selected' : '' }}>Number</option>
                            <option value="select"  {{ old('type') === 'select'  ? 'selected' : '' }}>Select (Dropdown)</option>
                            <option value="boolean" {{ old('type') === 'boolean' ? 'selected' : '' }}>Boolean (Yes/No)</option>
                        </select>
                    </div>

                    <div class="mb-3" id="optionsGroup" style="display:none;">
                        <label class="form-label fw-semibold">Options
                            <span class="text-muted small">(comma-separated, e.g. Small, Medium, Large)</span>
                        </label>
                        <input type="text" name="options" value="{{ old('options') }}"
                               placeholder="Small, Medium, Large, XL"
                               class="form-control @error('options') is-invalid @enderror">
                        @error('options')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_required"
                                   id="isRequired" value="1"
                                   {{ old('is_required') ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="isRequired">
                                Required Attribute
                                <span class="text-muted small d-block fw-normal">
                                    If checked, products in this category must provide this value.
                                </span>
                            </label>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-lg"></i> Add Attribute
                        </button>
                        <a href="{{ route('admin.categories.attributes.index', $category) }}"
                           class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const typeSelect = document.getElementById('typeSelect');
    const optionsGroup = document.getElementById('optionsGroup');

    function toggleOptions() {
        optionsGroup.style.display = typeSelect.value === 'select' ? 'block' : 'none';
    }

    typeSelect.addEventListener('change', toggleOptions);
    toggleOptions();
</script>
@endpush
