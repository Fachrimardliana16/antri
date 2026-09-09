<?php

namespace App\Services;

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class QrCodeService
{
    public static function generateSvg(string $data, int $size = 140): string
    {
        try {
            $renderer = new ImageRenderer(
                new RendererStyle($size, 1),
                new SvgImageBackEnd()
            );
            $writer = new Writer($renderer);
            return $writer->writeString($data);
        } catch (\Throwable $e) {
            // Fallback lightweight SVG placeholder if BaconQrCode encounters rendering issue
            $url = htmlspecialchars($data);
            return '<svg xmlns="http://www.w3.org/2000/svg" width="'.$size.'" height="'.$size.'" viewBox="0 0 100 100" fill="none" stroke="currentColor"><rect width="100" height="100" fill="#fff"/><text x="50" y="55" font-size="10" text-anchor="middle" fill="#000">QR CODE</text></svg>';
        }
    }
}
