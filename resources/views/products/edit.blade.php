@extends('layouts.app')

@section('title', 'Edit Product')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Products</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.products.show', $product) }}">{{ $product->name }}</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>

        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-pencil-square"></i> Edit Product</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.products.update', $product) }}" method="POST" id="productForm">
                    @csrf @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                        <select name="category_id" id="categorySelect"
                                class="form-select @error('category_id') is-invalid @enderror" required>
                            <option value="">— Select a category —</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->breadcrumb }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Product Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $product->name) }}"
                               class="form-control @error('name') is-invalid @enderror" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" rows="3"
                                  class="form-control @error('description') is-invalid @enderror">{{ old('description', $product->description) }}</textarea>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Price ($) <span class="text-danger">*</span></label>
                            <input type="number" name="price"
                                   value="{{ old('price', $product->price) }}"
                                   step="0.01" min="0"
                                   class="form-control @error('price') is-invalid @enderror" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Stock <span class="text-danger">*</span></label>
                            <input type="number" name="stock"
                                   value="{{ old('stock', $product->stock) }}"
                                   min="0"
                                   class="form-control @error('stock') is-invalid @enderror" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                @foreach(['active','inactive','draft'] as $s)
                                    <option value="{{ $s }}"
                                        {{ old('status', $product->status) === $s ? 'selected' : '' }}>
                                        {{ ucfirst($s) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div id="attributesSection" class="mb-3" style="display:none;">
                        <hr>
                        <h6 class="mb-3 text-primary">
                            <i class="bi bi-list-check"></i> Category Attributes
                        </h6>
                        <div id="attributeFields"></div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Save Changes
                        </button>
                        <a href="{{ route('admin.products.show', $product) }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const apiBase = '{{ url("/api/categories") }}';
    const oldAttributes = @json(old('attributes', []));
    const existingValues = @json($product->attributeValues->pluck('value', 'category_attribute_id'));
    const attributeErrors = @json($errors->messages());

    const categorySelect = document.getElementById('categorySelect');
    const attributesSection = document.getElementById('attributesSection');
    const attributeFields = document.getElementById('attributeFields');

    function buildAttributeField(attr) {
        const oldVal = oldAttributes[attr.id] ?? existingValues[attr.id] ?? '';
        const errorKey = `attributes.${attr.id}`;
        const errorMsg = attributeErrors[errorKey] ? attributeErrors[errorKey][0] : '';
        const isInvalid = errorMsg ? 'is-invalid' : '';

        let inputHtml = '';

        if (attr.type === 'select' && attr.options && attr.options.length) {
            inputHtml = `<select name="attributes[${attr.id}]"
                                  class="form-select ${isInvalid}"
                                  ${attr.is_required ? 'required' : ''}>
                            <option value="">— Select —</option>
                            ${attr.options.map(o =>
                                `<option value="${o}" ${oldVal === o ? 'selected' : ''}>${o}</option>`
                            ).join('')}
                        </select>`;
        } else if (attr.type === 'boolean') {
            inputHtml = `<select name="attributes[${attr.id}]"
                                  class="form-select ${isInvalid}"
                                  ${attr.is_required ? 'required' : ''}>
                            <option value="">— Select —</option>
                            <option value="yes" ${oldVal === 'yes' ? 'selected' : ''}>Yes</option>
                            <option value="no"  ${oldVal === 'no'  ? 'selected' : ''}>No</option>
                        </select>`;
        } else if (attr.type === 'number') {
            inputHtml = `<input type="number" name="attributes[${attr.id}]"
                                value="${oldVal}"
                                class="form-control ${isInvalid}"
                                ${attr.is_required ? 'required' : ''}>`;
        } else {
            inputHtml = `<input type="text" name="attributes[${attr.id}]"
                                value="${oldVal}"
                                class="form-control ${isInvalid}"
                                ${attr.is_required ? 'required' : ''}>`;
        }

        return `<div class="attr-input-group">
            <label class="form-label fw-semibold mb-1">
                ${attr.name}
                ${attr.is_required
                    ? '<span class="badge bg-danger badge-required ms-1">Required</span>'
                    : '<span class="badge bg-secondary badge-required ms-1">Optional</span>'}
            </label>
            ${inputHtml}
            ${errorMsg ? `<div class="invalid-feedback d-block">${errorMsg}</div>` : ''}
        </div>`;
    }

    function loadAttributes(categoryId) {
        if (!categoryId) {
            attributesSection.style.display = 'none';
            attributeFields.innerHTML = '';
            return;
        }

        fetch(`${apiBase}/${categoryId}/attributes`)
            .then(r => r.json())
            .then(data => {
                if (!data.attributes || data.attributes.length === 0) {
                    attributesSection.style.display = 'none';
                    attributeFields.innerHTML = '';
                    return;
                }
                attributeFields.innerHTML = data.attributes.map(buildAttributeField).join('');
                attributesSection.style.display = 'block';
            })
            .catch(() => { attributesSection.style.display = 'none'; });
    }

    categorySelect.addEventListener('change', e => loadAttributes(e.target.value));

    if (categorySelect.value) {
        loadAttributes(categorySelect.value);
    }
</script>
@endpush
