<?php

namespace App\Http\Controllers;

use App\Services\S3Service;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class S3Controller extends Controller
{
    private S3Service $s3;

    public function __construct(S3Service $s3)
    {
        $this->s3 = $s3;
    }

    public function index()
    {
        $files = [];
        $error = null;

        try {
            $files = $this->s3->listFiles();
        } catch (\Exception $e) {
            $error = 'Unable to connect to S3. Please check your AWS credentials and bucket name. Error: ' . $e->getMessage();
        }

        return view('index', [
            'files' => $files,
            'storageClasses' => S3Service::storageClasses(),
            'error' => $error,
        ]);
    }

    public function upload(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => 'required|file|max:51200',
            'storage_class' => 'required|in:STANDARD,STANDARD_IA,ONEZONE_IA,GLACIER_IR,GLACIER,DEEP_ARCHIVE,INTELLIGENT_TIERING',
        ]);

        try {
            $key = $this->s3->uploadFile(
                $request->file('file'),
                $request->input('storage_class', 'STANDARD')
            );
            return redirect('/')->with('success', 'File uploaded successfully to S3 as ' . $request->input('storage_class'));
        } catch (\Exception $e) {
            return redirect('/')->with('error', 'Upload failed: ' . $e->getMessage());
        }
    }

    public function delete(Request $request, string $key): RedirectResponse
    {
        $key = urldecode($key);
        try {
            $this->s3->deleteFile($key);
            return redirect('/')->with('success', 'File deleted successfully.');
        } catch (\Exception $e) {
            return redirect('/')->with('error', 'Delete failed: ' . $e->getMessage());
        }
    }

    public function changeClass(Request $request, string $key): RedirectResponse
    {
        $key = urldecode($key);
        $request->validate([
            'storage_class' => 'required|in:STANDARD,STANDARD_IA,ONEZONE_IA,GLACIER_IR,GLACIER,DEEP_ARCHIVE,INTELLIGENT_TIERING',
        ]);

        try {
            $this->s3->changeStorageClass($key, $request->input('storage_class'));
            return redirect('/')->with('success', 'Storage class changed successfully.');
        } catch (\Exception $e) {
            return redirect('/')->with('error', 'Change class failed: ' . $e->getMessage());
        }
    }

    public function presignedUrl(string $key)
    {
        $key = urldecode($key);
        try {
            $url = $this->s3->getPresignedUrl($key, 5);
            return redirect($url);
        } catch (\Exception $e) {
            return redirect('/')->with('error', 'Failed to generate URL: ' . $e->getMessage());
        }
    }
}
