<?php

namespace Guava\IconPicker\Http\Controllers;

use Guava\IconPicker\Http\PickerToken;
use Guava\IconPicker\Icons\Facades\IconManager;
use Guava\IconPicker\Icons\IconSet;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IconIndexController
{
    public function __invoke(Request $request): Response
    {
        $token = PickerToken::parse($request->query('token'));

        abort_unless((bool) $token, 403);

        $sets = IconManager::getSets()
            ->filter(fn (IconSet $set) => $token->allowsSet($set->getId()))
            ->values()
        ;

        $icons = [];

        foreach ($sets as $index => $set) {
            foreach ($set->getIcons($token->scopeId) as $icon) {
                // Compact tuples keep the payload small for large sets.
                $icons[] = [$icon->id, $icon->label, $index, $icon->custom ? 1 : 0];
            }
        }

        $payload = [
            'sets' => $sets
                ->map(fn (IconSet $set) => ['id' => $set->getId(), 'label' => $set->label])
                ->all(),
            'icons' => $icons,
        ];

        $etag = '"' . md5(json_encode($payload)) . '"';
        $headers = [
            'ETag' => $etag,
            'Cache-Control' => 'private, no-cache',
        ];

        if (str_contains((string) $request->headers->get('If-None-Match'), $etag)) {
            return response('', 304, $headers);
        }

        return response()->json($payload, 200, $headers);
    }
}
