<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>S3 Storage Class Learning</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
    <h1 class="mb-4">S3 Storage Class Learning</h1>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if ($error)
        <div class="alert alert-warning">{{ $error }}</div>
    @endif

    <div class="row g-4">
        {{-- Upload Form --}}
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">Upload File to S3</div>
                <div class="card-body">
                    <form action="/upload" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="file" class="form-label">Select file</label>
                            <input type="file" name="file" id="file" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="storage_class" class="form-label">Storage Class</label>
                            <select name="storage_class" id="storage_class" class="form-select" required>
                                @foreach ($storageClasses as $class => $info)
                                    <option value="{{ $class }}">{{ $info['name'] }} ({{ $class }})</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Upload to S3</button>
                    </form>
                </div>
            </div>

            {{-- File List --}}
            <div class="card">
                <div class="card-header">Files in S3 Bucket</div>
                <div class="card-body p-0">
                    @if (count($files) === 0)
                        <div class="p-3 text-muted">No files uploaded yet.</div>
                    @else
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Original Name</th>
                                    <th>Storage Class</th>
                                    <th>Size</th>
                                    <th>Last Modified</th>
                                    <th style="width: 300px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($files as $file)
                                    <tr>
                                        <td>{{ $file['original_name'] }}</td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $file['storage_class'] }}</span>
                                        </td>
                                        <td>{{ number_format($file['size']) }} bytes</td>
                                        <td>{{ $file['last_modified']->format('Y-m-d H:i:s') }}</td>
                                        <td>
                                            <div class="d-flex gap-1 flex-wrap">
                                                <a href="/file/{{ rawurlencode($file['key']) }}/url" class="btn btn-sm btn-outline-primary" target="_blank">Presigned URL</a>

                                                <form action="/file/{{ rawurlencode($file['key']) }}/change-class" method="POST" class="d-inline">
                                                    @csrf
                                                    <div class="input-group input-group-sm">
                                                        <select name="storage_class" class="form-select form-select-sm" style="width: auto;">
                                                            @foreach ($storageClasses as $class => $info)
                                                                <option value="{{ $class }}" {{ $file['storage_class'] === $class ? 'selected' : '' }}>{{ $info['name'] }}</option>
                                                            @endforeach
                                                        </select>
                                                        <button class="btn btn-outline-warning btn-sm" type="submit">Change</button>
                                                    </div>
                                                </form>

                                                <form action="/file/{{ rawurlencode($file['key']) }}/delete" method="POST" class="d-inline" onsubmit="return confirm('Delete this file?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>

        {{-- Storage Class Reference --}}
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">Storage Classes Reference</div>
                <div class="card-body p-0">
                    <div class="accordion accordion-flush" id="classAccordion">
                        @foreach ($storageClasses as $class => $info)
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $loop->index }}">
                                        {{ $info['name'] }}
                                    </button>
                                </h2>
                                <div id="collapse{{ $loop->index }}" class="accordion-collapse collapse" data-bs-parent="#classAccordion">
                                    <div class="accordion-body small">
                                        <p class="mb-1"><strong>Code:</strong> <code>{{ $class }}</code></p>
                                        <p class="mb-1"><strong>Retrieval:</strong> {{ $info['retrieval'] }}</p>
                                        <p class="mb-1"><strong>Min Storage:</strong> {{ $info['min_storage'] }}</p>
                                        <p class="mb-1"><strong>Best For:</strong> {{ $info['best_for'] }}</p>
                                        <p class="mb-0 text-muted">{{ $info['description'] }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
