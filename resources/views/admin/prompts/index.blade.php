@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">Manage AI Prompts</h1>
            <a href="{{ route('admin.prompts.create') }}" class="btn btn-primary mb-3">Add New Prompt</a>
            
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="bg-dark text-white">
                        <tr>
                            <th>Category</th>
                            <th>Question</th>
                            <th>Active</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($prompts as $prompt)
                            <tr>
                                <td>{{ $prompt->incident_category }}</td>
                                <td>{{ $prompt->question }}</td>
                                <td>
                                    <span class="badge {{ $prompt->is_active ? 'bg-success' : 'bg-danger' }}">
                                        {{ $prompt->is_active ? 'Yes' : 'No' }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.prompts.edit', $prompt) }}" class="btn btn-sm btn-warning">Edit</a>
                                    <form action="{{ route('admin.prompts.destroy', $prompt) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this prompt?');">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">No prompts found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
