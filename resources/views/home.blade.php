<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>URL Shortener</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
        }
        .header {
            background: linear-gradient(to right, #4f46e5, #7c3aed);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
        }
        .btn-primary {
            background-color: #4f46e5;
            border-color: #4f46e5;
        }
        .btn-primary:hover {
            background-color: #4338ca;
            border-color: #4338ca;
        }
        .result-box {
            background-color: #f0f4f8;
            border-radius: 6px;
            padding: 1rem;
            margin-top: 1rem;
            word-break: break-all;
        }
        footer {
            margin-top: 3rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
        }
        .copy-btn-copied {
            background-color: #28a745;
            color: white;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header text-center">
        <div class="container">
            <h1>URL Shortener</h1>
            <p class="lead">Shorten long URLs into easy-to-share links</p>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <!-- Tabs -->
                <ul class="nav nav-tabs mb-4" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="shorten-tab" data-bs-toggle="tab" data-bs-target="#shorten" type="button" role="tab" aria-selected="true">Shorten URL</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="expand-tab" data-bs-toggle="tab" data-bs-target="#expand" type="button" role="tab" aria-selected="false">Expand URL</button>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content" id="myTabContent">
                    <!-- Shorten URL Tab -->
                    <div class="tab-pane fade show active" id="shorten" role="tabpanel" aria-labelledby="shorten-tab">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-3">Enter a long URL to shorten</h5>

                                <!-- Alert for errors -->
                                <div class="alert alert-danger d-none" id="encode-error"></div>

                                <!-- Alert for success -->
                                <div class="alert alert-success d-none" id="encode-success">URL shortened successfully!</div>

                                <form id="shorten-form">
                                    <div class="mb-3">
                                        <input type="url" class="form-control" id="long-url" placeholder="https://example.com/very/long/url" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Shorten URL</button>
                                </form>

                                <!-- Results Box -->
                                <div class="result-box d-none" id="shorten-result">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <a href="#" id="shortened-url" class="text-break text-decoration-none text-dark" target="_blank"></a>
                                        <button class="btn btn-sm btn-outline-primary ms-2" id="copy-short">Copy</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Expand URL Tab -->
                    <div class="tab-pane fade" id="expand" role="tabpanel" aria-labelledby="expand-tab">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-3">Enter a shortened URL to expand</h5>

                                <!-- Alert for errors -->
                                <div class="alert alert-danger d-none" id="decode-error"></div>

                                <!-- Alert for success -->
                                <div class="alert alert-success d-none" id="decode-success">URL expanded successfully!</div>

                                <form id="expand-form">
                                    <div class="mb-3">
                                        <input type="url" class="form-control" id="short-url" placeholder="http://short.est/AbC123" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Expand URL</button>
                                </form>

                                <!-- Results Box -->
                                <div class="result-box d-none" id="expand-result">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <a href="#" id="original-url" class="text-break text-decoration-none text-dark" target="_blank"></a>
                                        <button class="btn btn-sm btn-outline-primary ms-2" id="copy-long">Copy</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="text-center">
        <div class="container">
            <p>&copy; 2025 URL Shortener</p>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // DOM Elements - Shorten
            const shortenForm = document.getElementById('shorten-form');
            const longUrlInput = document.getElementById('long-url');
            const shortenResult = document.getElementById('shorten-result');
            const shortenedUrl = document.getElementById('shortened-url');
            const copyShortBtn = document.getElementById('copy-short');
            const encodeError = document.getElementById('encode-error');
            const encodeSuccess = document.getElementById('encode-success');

            // DOM Elements - Expand
            const expandForm = document.getElementById('expand-form');
            const shortUrlInput = document.getElementById('short-url');
            const expandResult = document.getElementById('expand-result');
            const originalUrl = document.getElementById('original-url');
            const copyLongBtn = document.getElementById('copy-long');
            const decodeError = document.getElementById('decode-error');
            const decodeSuccess = document.getElementById('decode-success');

            // Shorten URL
            shortenForm.addEventListener('submit', async function(e) {
                e.preventDefault();

                // Reset UI
                encodeError.classList.add('d-none');
                encodeSuccess.classList.add('d-none');
                shortenResult.classList.add('d-none');

                const url = longUrlInput.value.trim();
                if (!url) return;

                try {
                    const response = await fetch('/encode', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({ url: url })
                    });

                    const responseData = await response.json();

                    // Handle different response structures
                    const data = responseData.data || responseData;

                    if (!response.ok || (responseData.status && responseData.status !== 'Success')) {
                        encodeError.textContent = data.error || responseData.message || 'Failed to shorten URL';
                        encodeError.classList.remove('d-none');
                        return;
                    }

                    const shortUrl = data.short_url;

                    encodeSuccess.classList.remove('d-none');
                    shortenedUrl.textContent = shortUrl;
                    shortenedUrl.href = shortUrl;
                    shortenResult.classList.remove('d-none');

                    // Reset copy button
                    copyShortBtn.textContent = 'Copy';
                    copyShortBtn.classList.remove('copy-btn-copied');

                } catch (error) {
                    encodeError.textContent = 'An error occurred. Please try again.';
                    encodeError.classList.remove('d-none');
                    console.error(error);
                }
            });

            // Expand URL
            expandForm.addEventListener('submit', async function(e) {
                e.preventDefault();

                // Reset UI
                decodeError.classList.add('d-none');
                decodeSuccess.classList.add('d-none');
                expandResult.classList.add('d-none');

                const url = shortUrlInput.value.trim();
                if (!url) return;

                try {
                    const response = await fetch('/decode', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({ url: url })
                    });

                    const responseData = await response.json();

                    // Handle different response structures
                    const data = responseData.data || responseData;

                    if (!response.ok || (responseData.status && responseData.status !== 'Success')) {
                        decodeError.textContent = data.error || responseData.message || 'Failed to expand URL';
                        decodeError.classList.remove('d-none');
                        return;
                    }

                    const originalUrlStr = data.original_url;

                    decodeSuccess.classList.remove('d-none');
                    originalUrl.textContent = originalUrlStr;
                    originalUrl.href = originalUrlStr;
                    expandResult.classList.remove('d-none');

                    // Reset copy button
                    copyLongBtn.textContent = 'Copy';
                    copyLongBtn.classList.remove('copy-btn-copied');

                } catch (error) {
                    decodeError.textContent = 'An error occurred. Please try again.';
                    decodeError.classList.remove('d-none');
                    console.error(error);
                }
            });

            // Copy functionality
            copyShortBtn.addEventListener('click', function() {
                copyToClipboard(shortenedUrl.textContent, this);
            });

            copyLongBtn.addEventListener('click', function() {
                copyToClipboard(originalUrl.textContent, this);
            });

            // Helper function to copy text
            function copyToClipboard(text, button) {
                navigator.clipboard.writeText(text).then(() => {
                    button.textContent = 'Copied!';
                    button.classList.add('copy-btn-copied');
                    setTimeout(() => {
                        button.textContent = 'Copy';
                        button.classList.remove('copy-btn-copied');
                    }, 2000);
                }).catch(err => {
                    // Fallback for older browsers
                    const textarea = document.createElement('textarea');
                    textarea.value = text;
                    document.body.appendChild(textarea);
                    textarea.select();
                    document.execCommand('copy');
                    document.body.removeChild(textarea);

                    button.textContent = 'Copied!';
                    button.classList.add('copy-btn-copied');
                    setTimeout(() => {
                        button.textContent = 'Copy';
                        button.classList.remove('copy-btn-copied');
                    }, 2000);
                });
            }
        });
    </script>
</body>
</html>