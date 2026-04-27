<?php

namespace App\Http\Controllers;

use App\Services\S3Service;
use App\Services\DiscordService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class S3Controller extends Controller
{
    private S3Service $s3;
    private DiscordService $discord;

    public function __construct(S3Service $s3, DiscordService $discord)
    {
        $this->s3 = $s3;
        $this->discord = $discord;
    }

    public function index()
    {
        $files = [];
        $error = null;
        $s3Down = false;

        try {
            $files = $this->s3->listFiles();
        } catch (\Exception $e) {
            $s3Down = true;
            $error = $e->getMessage();
            \Log::error('S3 connection failed: ' . $error);
            $this->discord->sendErrorNotification('S3 Connection Failed - listFiles', $e);
        }

        if ($s3Down) {
            return response()->view('s3-down', [
                'storageClasses' => S3Service::storageClasses(),
                'errorMessage' => $error,
            ], 503);
        }

        return view('index', [
            'files' => $files,
            'storageClasses' => S3Service::storageClasses(),
            'error' => $error,
        ]);
    }

    public function health()
    {
        return response()->json(['status' => 'ok', 'timestamp' => now()->toIso8601String()], 200);
    }

    public function upload(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => 'required|file|max:51200',
            'storage_class' => 'required|in:STANDARD,STANDARD_IA,ONEZONE_IA,GLACIER_IR,GLACIER,DEEP_ARCHIVE,REDUCED_REDUNDANCY,INTELLIGENT_TIERING',
        ]);

        try {
            $key = $this->s3->uploadFile(
                $request->file('file'),
                $request->input('storage_class', 'STANDARD')
            );
            return redirect('/')->with('success', 'File uploaded successfully to S3 as ' . $request->input('storage_class'));
        } catch (\Exception $e) {
            $this->discord->sendErrorNotification('S3 Upload Failed - storage_class: ' . $request->input('storage_class'), $e);
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
            $this->discord->sendErrorNotification('S3 Delete Failed - key: ' . $key, $e);
            return redirect('/')->with('error', 'Delete failed: ' . $e->getMessage());
        }
    }

    public function changeClass(Request $request, string $key): RedirectResponse
    {
        $key = urldecode($key);
        $request->validate([
            'storage_class' => 'required|in:STANDARD,STANDARD_IA,ONEZONE_IA,GLACIER_IR,GLACIER,DEEP_ARCHIVE,REDUCED_REDUNDANCY,INTELLIGENT_TIERING',
        ]);

        try {
            $this->s3->changeStorageClass($key, $request->input('storage_class'));
            return redirect('/')->with('success', 'Storage class changed successfully.');
        } catch (\Exception $e) {
            $this->discord->sendErrorNotification('S3 Change Class Failed - key: ' . $key . ' -> ' . $request->input('storage_class'), $e);
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
            $this->discord->sendErrorNotification('S3 Presigned URL Failed - key: ' . $key, $e);
            return redirect('/')->with('error', 'Failed to generate URL: ' . $e->getMessage());
        }
    }
}
