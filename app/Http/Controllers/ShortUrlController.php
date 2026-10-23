<?php

namespace App\Http\Controllers;

use App\Models\ShortUrl;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ShortUrlController extends Controller
{
    public function indexAll()
    {        
        abort_unless(auth()->user()?->isSuperAdmin(), 403);

        $urls = ShortUrl::with(['company', 'user'])
            ->latest()
            ->paginate(10);

        return view('short-urls.index', compact('urls'));
    }

    public function store(Request $request)
    {
        $user = $request->user();

        abort_unless(
            in_array($user->role, ['Admin', 'Member'], true),
            403,
            'You are not allowed to create short URLs.'
        );

        abort_if(
            $user->company_id === null,
            403,
            'You must belong to a company to create a short URL.'
        );

        $validated = $request->validate([
            'original_url' => [
                'required',
                'string',
                'url',
                'max:2048',
                'regex:/^https?:\/\//i',
            ],
        ]);

        $shortUrl = null;

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $code = Str::random(8);

            try {
                $shortUrl = ShortUrl::create([
                    'company_id' => $user->company_id,
                    'user_id' => $user->id,
                    'original_url' => $validated['original_url'],
                    'code' => $code,
                ]);

                break;
            } catch (QueryException $exception) {
                // Retry only if this was a duplicate-key error for
                // the code. Re-throw unrelated database errors.
                $sqlState = $exception->errorInfo[0] ?? null;
                $driverCode = (int) ($exception->errorInfo[1] ?? 0);

                $isDuplicateKey = $sqlState === '23000'
                    || $sqlState === '23505';

                if (
                    !$isDuplicateKey ||
                    !ShortUrl::where('code', $code)->exists()
                ) {
                    throw $exception;
                }
            }
        }

        if (!$shortUrl) {
            throw ValidationException::withMessages([
                'original_url' => 'Unable to generate a unique short code. Please try again.',
            ]);
        }

        return redirect()
            ->back()
            ->with('success', 'Short URL created successfully.')
            ->with('short_url', url('/' . $shortUrl->code));

        // // Generate a unique public code.
        // do {
        //     $code = Str::random(8);
        // } while (ShortUrl::where('code', $code)->exists());

        // $shortUrl = $user->shortUrls()->create([
        //     'company_id' => $user->company_id,
        //     'original_url' => $validated['original_url'],
        //     'code' => $code,
        // ]);

        // return redirect()
        //     ->back()
        //     ->with('success', 'Short URL created successfully.')
        //     ->with('short_url', url('/' . $shortUrl->code));
    }

    public function redirect(string $code)
    {
        $shortUrl = ShortUrl::where('code', $code)->firstOrFail();
        
        $shortUrl->increment('clicks');

        return redirect()->away($shortUrl->original_url);
    }
}
