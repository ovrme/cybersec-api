<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\PhishingScanLog;
use App\Services\PhishingAnalyzerService;
use App\Services\UrlAnalyzerService;
use Illuminate\Http\Request;

class WebScannerController extends Controller
{
    public function index()
    {
        return view('scanner.index', ['result' => null, 'type' => null]);
    }

    public function scanEmail(Request $request, PhishingAnalyzerService $analyzer)
    {
        $request->validate(['content' => 'required|string|max:20000']);

        $result = $analyzer->analyzeEmail($request->content);

        PhishingScanLog::create([
            'user_id'    => $request->user()->id,
            'input_type' => 'email',
            'input_data' => $request->content,
            'risk_level' => $result['risk_level'],
            'risk_score' => $result['risk_score'],
            'flags'      => $result['flags'],
        ]);

        return view('scanner.index', [
            'result' => $result,
            'type'   => 'email',
            'input'  => $request->content,
        ]);
    }

    public function scanUrl(Request $request, UrlAnalyzerService $analyzer)
    {
        $request->validate(['url' => 'required|string|max:2000']);

        $result = $analyzer->analyzeUrl($request->url);

        PhishingScanLog::create([
            'user_id'    => $request->user()->id,
            'input_type' => 'url',
            'input_data' => $request->url,
            'risk_level' => $result['risk_level'],
            'risk_score' => $result['risk_score'],
            'flags'      => $result['flags'],
        ]);

        return view('scanner.index', [
            'result' => $result,
            'type'   => 'url',
            'input'  => $request->url,
        ]);
    }
}
