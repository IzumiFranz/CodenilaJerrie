@extends('layouts.admin')

@section('title', 'Edit Specialization')

@php
    $pageTitle = 'Edit Specialization: ' . $specialization->name;
    $pageActions = '<a href="' . route('admin.specializations.index') . '" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Back to List</a>';
@endphp

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Specialization Information</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.specializations.update', $specialization) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="code">Code <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           name="code" 
                                           id="code" 
                                           class="form-control @error('code') is-invalid @enderror" 
                                           value="{{ old('code', $specialization->code) }}" 
                                           required>
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="name">Specialization Name <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           name="name" 
                                           id="name" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           value="{{ old('name', $specialization->name) }}" 
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description" 
                                      id="description" 
                                      rows="4" 
                                      class="form-control @error('description') is-invalid @enderror">{{ old('description', $specialization->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" 
                                       name="is_active" 
                                       class="custom-control-input" 
                                       id="is_active" 
                                       value="1" 
                                       {{ old('is_active', $specialization->is_active) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">
                                    Active Status
                                </label>
                            </div>
                        </div>

                        <hr>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="card border-left-primary shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Instructors</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            {{ $specialization->getQualifiedInstructorsCount() }}/{{ $specialization->instructors()->count() }}
                                        </div>
                                        <small class="text-muted">Active/Total</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-left-success shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Subjects</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            {{ $specialization->subjects()->count() }}
                                        </div>
                                        <small class="text-muted">Requiring this specialization</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($specialization->instructors()->count() > 0)
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                This specialization is assigned to {{ $specialization->instructors()->count() }} instructor(s).
                            </div>
                        @endif

                        <div class="form-group text-right">
                            <a href="{{ route('admin.specializations.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Specialization
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Auto-uppercase code
        $('#code').on('input', function() {
            $(this).val($(this).val().toUpperCase());
        });
    });
</script>
@endpush