@extends('layouts.app')

@section('title', 'Edit Attribute')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">Categories</a></li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.categories.attributes.index', $category) }}">{{ $category->name }} Attributes</a>
                </li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>

        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-pencil-square"></i> Edit Attribute</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.categories.attributes.update', [$category, $attribute]) }}" method="POST">
                    @csrf @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Attribute Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $attribute->name) }}"
                               class="form-control @error('name') is-invalid @enderror" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Type</label>
                        <select name="type" id="typeSelect" class="form-select">
                            @foreach(['text','number','select','boolean'] as $type)
                                <option value="{{ $type }}"
                                    {{ old('type', $attribute->type) === $type ? 'selected' : '' }}>
                                    {{ ucfirst($type) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3" id="optionsGroup">
                        <label class="form-label fw-semibold">Options
                            <span class="text-muted small">(comma-separated)</span>
                        </label>
                        <input type="text" name="options"
                               value="{{ old('options', $attribute->options ? implode(', ', $attribute->options) : '') }}"
                               class="form-control">
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_required"
                                   id="isRequired" value="1"
                                   {{ old('is_required', $attribute->is_required) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="isRequired">
                                Required Attribute
                            </label>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Save Changes
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
