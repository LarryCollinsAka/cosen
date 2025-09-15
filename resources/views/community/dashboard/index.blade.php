@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">My Incident Reports</h1>
            <p class="lead">View the status of incidents you've reported.</p>

            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Severity</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($reports as $report)
                            <tr>
                                <td>{{ $report->id }}</td>
                                <td>{{ Str::limit($report->title, 50) }}</td>
                                <td>
                                    <span class="badge bg-secondary">{{ $report->category ?? 'Unclassified' }}</span>
                                </td>
                                <td>
                                    @php
                                        $severity_class = match($report->severity) {
                                            'High' => 'bg-danger',
                                            'Medium' => 'bg-warning text-dark',
                                            'Low' => 'bg-success',
                                            default => 'bg-secondary'
                                        };
                                    @endphp
                                    <span class="badge {{ $severity_class }}">{{ $report->severity ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-info text-dark">{{ $report->status }}</span>
                                </td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-primary">View Conversation</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">You have not submitted any reports yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection