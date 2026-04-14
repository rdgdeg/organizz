<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Symfony\Component\HttpFoundation\Response;

class EventQrController extends Controller
{
    public function __invoke(string $slug): Response
    {
        $event = Event::query()->where('slug', $slug)->firstOrFail();
        if (! $event->isOpen()) {
            abort(404);
        }

        $url = route('public.event', ['slug' => $event->slug]);

        $writer = new PngWriter;
        $result = $writer->write(new QrCode($url));

        return response($result->getString(), 200, [
            'Content-Type' => $result->getMimeType(),
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}
