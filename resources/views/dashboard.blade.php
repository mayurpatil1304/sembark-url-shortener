
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    {{ $user->role }} Dashboard
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Welcome back, {{ $user->name }}.
                </p>
            </div>

            @if(in_array($user->role, ['Admin', 'Member'], true))
                <a href="{{ route('short-urls.create') }}"
                   class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                    + Create short URL
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-8 px-4 sm:px-6 lg:px-8">

            {{-- Flash messages --}}
            @if(session('success'))
                <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Company information --}}
            @if($company)
                <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="text-sm font-medium text-gray-500">Your company</p>
                    <div class="mt-1 flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                        <h3 class="text-xl font-semibold text-gray-900">
                            {{ $company->name }}
                        </h3>
                        <p class="text-sm text-gray-500">
                            Signed in as {{ $user->role }}
                        </p>
                    </div>
                </section>
            @elseif($user->isSuperAdmin())
                <section class="rounded-xl border border-indigo-100 bg-indigo-50 p-5">
                    <p class="text-sm font-medium text-indigo-700">Platform overview</p>
                    <h3 class="mt-1 text-lg font-semibold text-indigo-950">
                        All companies and their URL activity
                    </h3>
                </section>
            @endif

            {{-- Statistics --}}
            <section>
                <h3 class="mb-4 text-lg font-semibold text-gray-900">
                    Overview
                </h3>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                        <p class="text-sm text-gray-500">
                            {{ $user->isSuperAdmin() ? 'Total companies' : 'Company' }}
                        </p>
                        <p class="mt-2 text-3xl font-bold text-gray-900">
                            {{ number_format($stats['companies']) }}
                        </p>
                    </div>

                    <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                        <p class="text-sm text-gray-500">Team members</p>
                        <p class="mt-2 text-3xl font-bold text-gray-900">
                            {{ number_format($stats['team_members']) }}
                        </p>
                    </div>

                    <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                        <p class="text-sm text-gray-500">
                            {{ $user->isMember() ? 'My short URLs' : 'Total short URLs' }}
                        </p>
                        <p class="mt-2 text-3xl font-bold text-indigo-600">
                            {{ number_format($stats['total_urls']) }}
                        </p>
                    </div>

                    <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                        <p class="text-sm text-gray-500">
                            {{ $user->isMember() ? 'Clicks on my URLs' : 'Total clicks' }}
                        </p>
                        <p class="mt-2 text-3xl font-bold text-emerald-600">
                            {{ number_format($stats['total_clicks']) }}
                        </p>
                    </div>
                </div>
            </section>

            {{-- Company list: SuperAdmin only --}}          

            @if($user->isSuperAdmin())
                <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900">
                        Create company and invite Admin
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Create a company and send an invitation to its first Admin.
                    </p>

                    <form method="POST"
                        action="{{ route('admin.invitations.store') }}"
                        class="mt-5 space-y-4">
                        @csrf

                        <div>
                            <label for="company_name"
                                class="block text-sm font-medium text-gray-700">
                                Company name
                            </label>
                            <input id="company_name"
                                name="company_name"
                                value="{{ old('company_name') }}"
                                required
                                class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('company_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="admin_name"
                                class="block text-sm font-medium text-gray-700">
                                Admin name
                            </label>
                            <input id="admin_name"
                                name="name"
                                value="{{ old('name') }}"
                                required
                                class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="admin_email"
                                class="block text-sm font-medium text-gray-700">
                                Admin email
                            </label>
                            <input id="admin_email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit"
                                class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                            Create company and send invitation
                        </button>
                    </form>
                </section>
            @endif

            {{-- Team --}}

            @if($user->isAdmin())
                <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900">
                        Invite a team member
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Invite someone to join {{ $company?->name ?? 'your company' }}.
                    </p>

                    <form method="POST"
                        action="{{ route('company.invitations.store') }}"
                        class="mt-5 space-y-4">
                        @csrf

                        <div>
                            <label for="invite_name"
                                class="block text-sm font-medium text-gray-700">
                                Name
                            </label>
                            <input id="invite_name"
                                name="name"
                                value="{{ old('name') }}"
                                required
                                class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="invite_email"
                                class="block text-sm font-medium text-gray-700">
                                Email
                            </label>
                            <input id="invite_email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="invite_role"
                                class="block text-sm font-medium text-gray-700">
                                Role
                            </label>
                            <select id="invite_role"
                                    name="role"
                                    required
                                    class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="Member">Member</option>
                                <option value="Admin">Admin</option>
                            </select>
                            @error('role')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit"
                                class="border-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold hover:bg-indigo-700  m-3">
                            Send invitation
                        </button>
                    </form>
                </section>
            @endif
            <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 px-5 py-4">
                    <h3 class="text-lg font-semibold text-gray-900">
                        {{ $user->isSuperAdmin() ? 'Platform team' : 'Your team' }}
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ $user->isSuperAdmin()
                            ? 'Users assigned to companies.'
                            : 'People in your company.' }}
                    </p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-gray-500">Name</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-gray-500">Email</th>
                                @if($user->isSuperAdmin())
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-gray-500">Company</th>
                                @endif
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-gray-500">Role</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($team as $member)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-5 py-4 text-sm font-medium text-gray-900">
                                        {{ $member->name }}
                                    </td>
                                    <td class="px-5 py-4 text-sm text-gray-600">
                                        {{ $member->email }}
                                    </td>
                                    @if($user->isSuperAdmin())
                                        <td class="px-5 py-4 text-sm text-gray-600">
                                            {{ $member->company?->name ?? '—' }}
                                        </td>
                                    @endif
                                    <td class="px-5 py-4">
                                        <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-700">
                                            {{ $member->role }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $user->isSuperAdmin() ? 4 : 3 }}"
                                        class="px-5 py-8 text-center text-sm text-gray-500">
                                        No team members found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            {{-- Short URL list --}}
            <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="flex flex-col gap-2 border-b border-gray-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">
                            {{ $user->isSuperAdmin() ? 'All short URLs' : ($user->isMember() ? 'My short URLs' : 'Company short URLs') }}
                        </h3>
                        <p class="mt-1 text-sm text-gray-500">
                            {{ $user->isSuperAdmin()
                                ? 'URLs across all companies.'
                                : ($user->isMember()
                                    ? 'URLs created by your account.'
                                    : 'URLs created by your company team.') }}
                        </p>
                    </div>

                    @if(in_array($user->role, ['Admin', 'Member'], true))
                        <a href="{{ route('short-urls.create') }}"
                           class="text-sm font-semibold text-indigo-600 hover:text-indigo-800">
                            Create a short URL
                        </a>
                    @endif
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-gray-500">Short URL</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-gray-500">Destination</th>
                                @if($user->isSuperAdmin() || $user->isAdmin())
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-gray-500">Company</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-gray-500">Created by</th>
                                @endif
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-gray-500">Clicks</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-gray-500">Created</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($urls as $url)
                                <tr class="align-top hover:bg-gray-50">
                                    <td class="whitespace-nowrap px-5 py-4 text-sm">
                                        <a href="{{ url('/'.$url->code) }}"
                                           target="_blank"
                                           rel="noopener noreferrer"
                                           class="font-semibold text-indigo-600 hover:text-indigo-800">
                                            {{ url('/'.$url->code) }}
                                        </a>
                                        <button type="button"
                                            onclick="navigator.clipboard.writeText(@js(url('/'.$url->code))).then(() => { this.innerText = 'Copied!'; }).catch(() => alert('Copy failed. Please copy the link manually.'))"
                                            class="ml-1 text-xs text-gray-500 hover:text-gray-800">
                                            Copy
                                        </button>
                                    </td>
                                    <td class="max-w-xs px-5 py-4 text-sm text-gray-600">
                                        <a href="{{ $url->original_url }}"
                                           target="_blank"
                                           rel="noopener noreferrer"
                                           title="{{ $url->original_url }}"
                                           class="break-all hover:text-indigo-600">
                                            {{ \Illuminate\Support\Str::limit($url->original_url, 65) }}
                                        </a>
                                    </td>
                                    @if($user->isSuperAdmin() || $user->isAdmin())
                                        <td class="px-5 py-4 text-sm text-gray-600">
                                            {{ $url->company?->name ?? '—' }}
                                        </td>
                                        <td class="px-5 py-4 text-sm text-gray-600">
                                            {{ $url->user?->name ?? '—' }}
                                        </td>
                                    @endif
                                    <td class="px-5 py-4 text-sm font-semibold text-gray-900">
                                        {{ number_format($url->clicks) }}
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-500">
                                        {{ $url->created_at?->format('d M Y, h:i A') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ ($user->isSuperAdmin() || $user->isAdmin()) ? 6 : 4 }}"
                                        class="px-5 py-10 text-center">
                                        <p class="font-medium text-gray-700">No short URLs yet</p>
                                        <p class="mt-1 text-sm text-gray-500">
                                            Create a short URL to see your activity here.
                                        </p>
                                        @if(in_array($user->role, ['Admin', 'Member'], true))
                                            <a href="{{ route('short-urls.create') }}"
                                               class="mt-3 inline-block text-sm font-semibold text-indigo-600 hover:text-indigo-800">
                                                Create your first URL
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($urls->hasPages())
                    <div class="border-t border-gray-200 px-5 py-4">
                        {{ $urls->links() }}
                    </div>
                @endif
            </section>

        </div>
    </div>
</x-app-layout>
