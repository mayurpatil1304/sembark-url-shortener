
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">
                    Platform Short URLs
                </h2>
                <p class="text-sm text-gray-500">
                    View short links across all companies.
                </p>
            </div>

            <a href="{{ route('dashboard') }}"
               class="text-sm font-semibold text-indigo-600 hover:text-indigo-800">
                Back to dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="rounded-xl border bg-white p-5 shadow-sm">
                    <p class="text-sm text-gray-500">Total short URLs</p>
                    <p class="mt-2 text-3xl font-bold text-indigo-600">
                        {{ number_format($urls->total()) }}
                    </p>
                    <p class="mt-1 text-xs text-gray-400">Total matching records</p>
                </div>

                <div class="rounded-xl border bg-white p-5 shadow-sm">
                    <p class="text-sm text-gray-500">Clicks on this page</p>
                    <p class="mt-2 text-3xl font-bold text-emerald-600">
                        {{ number_format($urls->sum('clicks')) }}
                    </p>
                </div>

                <div class="rounded-xl border bg-white p-5 shadow-sm">
                    <p class="text-sm text-gray-500">Page</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">
                        {{ $urls->currentPage() }}
                    </p>
                    <p class="mt-1 text-xs text-gray-400">
                        {{ $urls->count() }} records on this page
                    </p>
                </div>
            </div>

            <div class="overflow-hidden rounded-xl border bg-white shadow-sm">
                <div class="border-b px-5 py-4">
                    <h3 class="text-lg font-semibold text-gray-900">
                        All short URLs
                    </h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-gray-500">Short URL</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-gray-500">Destination</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-gray-500">Company</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-gray-500">Created by</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-gray-500">Clicks</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-gray-500">Created</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($urls as $url)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-5 py-4 text-sm">
                                        <a href="{{ url('/'.$url->code) }}"
                                           target="_blank"
                                           rel="noopener noreferrer"
                                           class="font-semibold text-indigo-600 hover:underline">
                                            {{ url('/'.$url->code) }}
                                        </a>
                                    </td>
                                    <td class="max-w-xs break-all px-5 py-4 text-sm text-gray-600">
                                        {{ \Illuminate\Support\Str::limit($url->original_url, 70) }}
                                    </td>
                                    <td class="px-5 py-4 text-sm text-gray-600">
                                        {{ $url->company?->name ?? '—' }}
                                    </td>
                                    <td class="px-5 py-4 text-sm text-gray-600">
                                        {{ $url->user?->name ?? '—' }}
                                    </td>
                                    <td class="px-5 py-4 text-sm font-semibold text-gray-900">
                                        {{ number_format($url->clicks) }}
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-500">
                                        {{ $url->created_at?->format('d M Y') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-10 text-center text-sm text-gray-500">
                                        No short URLs have been created yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($urls->hasPages())
                    <div class="border-t px-5 py-4">
                        {{ $urls->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
