<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>S3 is Dead 💀</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #1a1a2e;
            color: #e0e0e0;
            font-family: 'Courier New', monospace;
            min-height: 100vh;
        }
        .cemetery-title {
            font-size: 2.5rem;
            text-align: center;
            margin-top: 2rem;
            color: #c0392b;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }
        .subtitle {
            text-align: center;
            color: #7f8c8d;
            font-size: 1.1rem;
            margin-bottom: 2rem;
        }
        .tombstone {
            background: #2d2d44;
            border: 2px solid #4a4a6a;
            border-radius: 12px 12px 0 0;
            padding: 1.2rem;
            margin-bottom: 1rem;
            position: relative;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .tombstone:hover {
            transform: translateY(-4px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.4);
        }
        .tombstone.dead {
            border-color: #c0392b;
            background: #2a1a1a;
            animation: flicker 3s infinite;
        }
        .tombstone.dead .class-name {
            text-decoration: line-through;
            color: #c0392b;
        }
        .tombstone.dead .rip-badge {
            display: inline-block;
        }
        .rip-badge {
            display: none;
            background: #c0392b;
            color: white;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: bold;
            margin-left: 8px;
            animation: pulse 2s infinite;
        }
        .class-name {
            font-size: 1.1rem;
            font-weight: bold;
            color: #ecf0f1;
        }
        .class-code {
            color: #95a5a6;
            font-size: 0.85rem;
        }
        .class-detail {
            font-size: 0.85rem;
            color: #bdc3c7;
            margin-top: 0.3rem;
        }
        .cross {
            font-size: 2rem;
            text-align: center;
            color: #7f8c8d;
            margin-bottom: 0.5rem;
        }
        .tombstone.dead .cross {
            color: #c0392b;
        }
        .error-box {
            background: #2d2d44;
            border: 1px solid #4a4a6a;
            border-radius: 8px;
            padding: 1rem;
            margin-top: 2rem;
            font-size: 0.85rem;
            color: #e74c3c;
            word-break: break-all;
        }
        .fog {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 80px;
            background: linear-gradient(transparent, rgba(26,26,46,0.9));
            pointer-events: none;
            z-index: 10;
        }
        .retry-btn {
            background: #c0392b;
            color: white;
            border: none;
            padding: 10px 30px;
            border-radius: 6px;
            font-family: 'Courier New', monospace;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s;
        }
        .retry-btn:hover {
            background: #e74c3c;
        }
        @keyframes flicker {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.85; }
            52% { opacity: 1; }
            54% { opacity: 0.9; }
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
    </style>
</head>
<body>
<div class="container py-4">
    <div class="cemetery-title">
        &#x2670; S3 Storage Cemetery &#x2671;
    </div>
    <div class="subtitle">
        AWS S3 is currently unavailable or under maintenance.<br>
        One of our storage classes didn't make it...
    </div>

    <div class="row g-3">
        @php
            $classes = $storageClasses;
            $deadClass = 'REDUCED_REDUNDANCY';
        @endphp

        @foreach ($classes as $code => $info)
            <div class="col-md-6 col-lg-4 col-xl-3">
                <div class="tombstone {{ $code === $deadClass ? 'dead' : '' }}">
                    <div class="cross">{!! $code === $deadClass ? '&#x2620;' : '&#x271D;' !!}</div>
                    <div>
                        <span class="class-name">{{ $info['name'] }}</span>
                        <span class="rip-badge">RIP</span>
                    </div>
                    <div class="class-code">{{ $code }}</div>
                    <div class="class-detail">
                        @if ($code === $deadClass)
                            <span style="color:#e74c3c;">Deceased &mdash; deprecated by AWS, reduced redundancy was not enough</span>
                        @else
                            {{ $info['retrieval'] }} &bull; {{ $info['min_storage'] }}
                        @endif
                    </div>
                    @if ($code !== $deadClass)
                        <div class="class-detail text-muted" style="font-size:0.75rem;">{{ $info['best_for'] }}</div>
                    @else
                        <div class="class-detail" style="font-size:0.75rem; color:#c0392b;">Cause of death: deprecated — only 99.99% durability. AWS pulled the plug.</div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <div class="text-center mt-4">
        <button class="retry-btn" onclick="location.reload()">&#x26A1; Attempt Resurrection &#x26A1;</button>
    </div>

    @if ($errorMessage)
        <div class="error-box">
            <strong>Technical Details:</strong> {{ $errorMessage }}
        </div>
    @endif

    <div class="text-center mt-4" style="color: #4a4a6a; font-size: 0.8rem;">
        This is a learning app. S3 outages are temporary. Try again in a few minutes.<br>
        Status code: 503 Service Unavailable
    </div>
</div>

<div class="fog"></div>
</body>
</html>
