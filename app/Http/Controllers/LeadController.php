<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Lead;

use Illuminate\Support\Facades\Redis;

class LeadController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    
    public function registerLead(Request $request, $token)
    {
        //Validate user permited
        if (auth()->user()->role == 'agent') {
            $meta =  [
                'success' => false,
                'errors' => [
                    'User unauthorized'
                ]
            ];

            return response()->json(['meta' => $meta], 401);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'source' => 'required',
            'owner' => 'required'
        ]);

        if ($validator->fails()) {
            $meta =  [
                'success' => false,
                'errors' => [
                    $validator->errors()
                ]
            ];

            return response()->json(['meta' => $meta], 400);
        }

        $lead = Lead::create(array_merge(
            $validator->validate(),
            ['created_by' => auth()->user()->id]
        ));

        $meta =  [
            'success' => true,
            'errors' => []
        ];

        return response()->json([
            'meta' => $meta,
            'data' => $lead
        ])->withHeaders([
            'token' => $token
        ]);
    }

    public function getLead(Request $request, $lead_id, $token)
    {
        $lead = Lead::find($lead_id);
        if ($lead) {
            if (auth()->user()->role == 'agent' && $lead->owner != auth()->user()->id) {
                $meta =  [
                    'success' => false,
                    'errors' => [
                        'User unauthorized'
                    ]
                ];
    
                return response()->json(['meta' => $meta], 401);
            } else {
                // Set a new key with the blog id
                Redis::set('lead_' . $lead_id, $lead);
                $lead_redis = Redis::get('lead_'.$lead_id);
    
                $meta =  [
                    'success' => true,
                    'errors' => []
                ];
    
                return response()->json([
                    'meta' => $meta,
                    'data' => json_decode($lead_redis)
                ])->withHeaders([
                    'token' => $token
                ]);
            }
        } else {
            $meta =  [
                'success' => false,
                'errors' => [
                    'No lead found'
                ]
            ];

            return response()->json(['meta' => $meta], 401);
        }
    }

    public function getLeads(Request $request, $token)
    {
        //Validate user permited
        if (auth()->user()->role == 'agent') {
            $meta =  [
                'success' => false,
                'errors' => [
                    'User unauthorized'
                ]
            ];

            return response()->json(['meta' => $meta], 401);
        }
        $leads = Lead::all();

        Redis::set('leads', $leads);

        $leads_redis = Redis::get('leads');
    
        $meta =  [
            'success' => true,
            'errors' => []
        ];

        return response()->json([
            'meta' => $meta,
            'data' => json_decode($leads_redis)
        ])->withHeaders([
            'token' => $token
        ]);
    }
}
