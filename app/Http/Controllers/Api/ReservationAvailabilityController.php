<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use OpenApi\Attributes as OA;

#[OA\Info(
    title: 'Public Endpoints API',
    version: '1.0.0',
    description: 'API documentation for public reservation endpoints.'
)]
class ReservationAvailabilityController extends Controller
{
    #[OA\Get(
        path: '/api/reservation-availabilities',
        summary: 'Get reservation availability',
        tags: ['Reservations'],
        parameters: [
            new OA\Parameter(
                name: 'court_id',
                in: 'query',
                required: true,
                description: 'The ID of the court',
                schema: new OA\Schema(type: 'integer')
            ),
            new OA\Parameter(
                name: 'date',
                in: 'query',
                required: true,
                description: 'Date in YYYY-MM-DD format',
                schema: new OA\Schema(type: 'string', format: 'date')
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Successful operation'),
            new OA\Response(response: 400, description: 'Bad Request'),
        ]
    )]
    public function index(Request $request)
    {
        $request->validate([
            'court_id' => 'required|integer',
            'date' => 'required|date_format:Y-m-d',
        ]);

        $courtId = $request->input('court_id');
        $date = $request->input('date');

        $response = Http::get("https://glasspadelapp.com/api/padel-courts/{$courtId}/availability", [
            'date' => $date,
        ]);

        if ($response->failed()) {
            return response()->json([
                'error' => 'Failed to fetch availability',
                'details' => $response->body(),
            ], $response->status());
        }

        return response()->json($response->json());
    }
}
