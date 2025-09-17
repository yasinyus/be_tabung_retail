<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DownloadLog;
use App\Jobs\GenerateQrCodesJob;
use App\Models\Tabung;

class TestDownloadController extends Controller
{
    public function testModel()
    {
        try {
            // Test create model instance
            $downloadLog = new DownloadLog();
            $downloadLog->user_id = 1;
            $downloadLog->type = 'qr_codes';
            $downloadLog->status = 'pending';
            $downloadLog->progress = 0;
            $downloadLog->message = 'Test message';
            $downloadLog->save();
            
            return response()->json([
                'success' => true,
                'message' => 'DownloadLog model works!',
                'data' => $downloadLog
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function testJob()
    {
        try {
            // Test dispatch job
            $tabungs = Tabung::limit(5)->pluck('id')->toArray();
            $downloadId = 1;
            $userId = 1;
            
            GenerateQrCodesJob::dispatch($downloadId, $tabungs, $userId);
            
            return response()->json([
                'success' => true,
                'message' => 'Job dispatched successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
}
