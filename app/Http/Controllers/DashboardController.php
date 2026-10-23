<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\ShortUrl;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $company = null;
        $companies = collect();
        $team = collect();

        $stats = [
            'companies' => 0,
            'team_members' => 0,
            'total_urls' => 0,
            'total_clicks' => 0,
        ];

        $urlQuery = ShortUrl::query();

        switch ($user->role) {
            case User::ROLE_SUPER_ADMIN:
                $companies = Company::query()
                    ->withCount(['users', 'shortUrls'])
                    ->withSum('shortUrls', 'clicks')
                    ->orderBy('name')
                    ->get();

                $team = User::query()
                    ->with('company')
                    ->whereNotNull('company_id')
                    ->orderBy('name')
                    ->get();

                $stats['companies'] = Company::count();
                $stats['team_members'] = User::whereNotNull('company_id')->count();
                $stats['total_urls'] = ShortUrl::count();
                $stats['total_clicks'] = (int) ShortUrl::sum('clicks');

                $urlQuery->with(['company', 'user']);
                break;

            case User::ROLE_ADMIN:
                abort_if(
                    $user->company_id === null,
                    403,
                    'Your account is not assigned to a company.'
                );

                $company = Company::findOrFail($user->company_id);

                $team = User::query()
                    ->where('company_id', $company->id)
                    ->orderBy('name')
                    ->get();

                $companyUrls = ShortUrl::where('company_id', $company->id);

                $stats['companies'] = 1;
                $stats['team_members'] = $team->count();
                $stats['total_urls'] = (clone $companyUrls)->count();
                $stats['total_clicks'] = (int) (clone $companyUrls)->sum('clicks');

                $urlQuery->where('company_id', $company->id)
                    ->with(['company', 'user']);
                break;

            case User::ROLE_MEMBER:
                abort_if(
                    $user->company_id === null,
                    403,
                    'Your account is not assigned to a company.'
                );

                $company = Company::findOrFail($user->company_id);
                
                $team = User::query()
                    ->where('company_id', $company->id)
                    ->orderBy('name')
                    ->get();

                $myUrls = ShortUrl::where('company_id', $company->id)
                    ->where('user_id', $user->id);

                $stats['companies'] = 1;
                $stats['team_members'] = $team->count();
                $stats['total_urls'] = (clone $myUrls)->count();
                $stats['total_clicks'] = (int) (clone $myUrls)->sum('clicks');

                $urlQuery->where('company_id', $company->id)
                    ->where('user_id', $user->id)
                    ->with(['company', 'user']);
                break;
                
            case User::ROLE_MANAGER:
                abort_if(
                    $user->company_id === null,
                    403,
                    'Your account is not assigned to a company.'
                );

                $company = Company::findOrFail($user->company_id);

                $team = User::query()
                    ->where('company_id', $company->id)
                    ->orderBy('name')
                    ->get();

                $companyUrls = ShortUrl::where('company_id', $company->id);

                $stats['companies'] = 1;
                $stats['team_members'] = $team->count();
                $stats['total_urls'] = (clone $companyUrls)->count();
                $stats['total_clicks'] = (int) (clone $companyUrls)->sum('clicks');

                $urlQuery->where('company_id', $company->id)
                    ->with(['company', 'user']);
                break;

            case User::ROLE_SALES:
                abort_if(
                    $user->company_id === null,
                    403,
                    'Your account is not assigned to a company.'
                );

                $company = Company::findOrFail($user->company_id);

                $team = User::query()
                    ->where('company_id', $company->id)
                    ->orderBy('name')
                    ->get();

                // Sales can view company information and team,
                // but URL statistics and URL records remain unavailable.
                $stats['companies'] = 1;
                $stats['team_members'] = $team->count();

                $urlQuery->whereRaw('1 = 0');
                break;

            default:
                abort(403, 'Your role does not have dashboard access.');
        }

        $urls = $urlQuery->latest()->paginate(10);

        return view('dashboard', compact(
            'user',
            'company',
            'companies',
            'team',
            'stats',
            'urls'
        ));
        
    }
}
