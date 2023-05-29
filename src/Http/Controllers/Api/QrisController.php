<?php

namespace Mdigi\QrisBankJateng\Http\Controllers\Api;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Mdigi\QrisBankJateng\Dtos\QrisLink;
use Mdigi\QrisBankJateng\Http\Controllers\Controller;
use Mdigi\QrisBankJateng\Http\Middleware\QrisAuth;
use Mdigi\QrisBankJateng\Services\QrisService;

class QrisController extends Controller
{
    public function __construct(private QrisService $service)
    {
        $this->middleware(QrisAuth::class);
    }

    public function __invoke($nop)
    {
        $validator = Validator::validate([
            'nop' => $nop,
        ], [
            'nop' => ['required', 'numeric', 'digits:18'],
        ]);
        $qrisLink = $this->service->getLink($validator['nop']);

        if ($qrisLink->errorCode != QrisLink::RESPONSE_SUCCESS) {
            Log::info('QRIS API: Cannot get QRIS.', [
                'reason' => $qrisLink
            ]);
            abort(404);
        }
        return response()->json([
            'url' => $qrisLink->link,
        ]);
    }
}
