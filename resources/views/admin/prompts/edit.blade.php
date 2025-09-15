@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h3 class="mb-0">Edit Prompt</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.prompts.update', $prompt) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="incident_category" class="form-label">Incident Category</label>
                            <input type="text" class="form-control" id="incident_category" name="incident_category" value="{{ old('incident_category', $prompt->incident_category) }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="question" class="form-label">AI Question</label>
                            <textarea class="form-control" id="question" name="question" rows="4" required>{{ old('question', $prompt->question) }}</textarea>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', $prompt->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Is Active?</label>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Prompt</button>
                        <a href="{{ route('admin.prompts.index') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection