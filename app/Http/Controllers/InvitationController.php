<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class InvitationController extends Controller
{
    public function storeForNewCompany(Request $request)
    {
        abort_unless($request->user()->isSuperAdmin(), 403);

        $data = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
        ]);

        [$invitation, $token] = DB::transaction(function () use ($data, $request) {
            $company = Company::create([
                'name' => $data['company_name'],
            ]);

            return $this->createInvitation(
                $company,
                $request->user(),
                $data['email'],
                User::ROLE_ADMIN
            );
        });

        $this->sendInvitationEmail($invitation, $token);

        return back()->with('success', 'Company created and Admin invitation sent.');
    }

    public function storeForCompany(Request $request)
    {
        $inviter = $request->user();

        abort_unless($inviter->isAdmin(), 403);
        abort_if(!$inviter->company_id, 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => [
                'required',
                Rule::in([User::ROLE_ADMIN, User::ROLE_MEMBER]),
            ],
        ]);

        $company = $inviter->company;

        abort_if(!$company, 403);

        [$invitation, $token] = DB::transaction(function () use (
            $company, $inviter, $data
        ) {
            return $this->createInvitation(
                $company,
                $inviter,
                $data['email'],
                $data['role']
            );
        });

        $this->sendInvitationEmail($invitation, $token);

        return back()->with('success', 'Invitation sent successfully.');
    }

    private function createInvitation(
        Company $company,
        User $inviter,
        string $email,
        string $role
    ): array {
        $pendingExists = Invitation::where('company_id', $company->id)
            ->where('email', $email)
            ->whereNull('accepted_at')
            ->where('expires_at', '>', now())
            ->exists();

        if ($pendingExists) {
            throw ValidationException::withMessages([
                'email' => 'An active invitation already exists for this company and email.',
            ]);
        }

        $token = Str::random(64);

        $invitation = Invitation::create([
            'company_id' => $company->id,
            'invited_by' => $inviter->id,
            'email' => $email,
            'role' => $role,
            'token_hash' => hash('sha256', $token),
            'expires_at' => now()->addDays(2),
        ]);

        return [$invitation, $token];
    }

    private function sendInvitationEmail(
        Invitation $invitation,
        string $token
    ): void {
        $url = route('invitations.accept.form', ['token' => $token]);

        Mail::raw(
            "You have been invited to join {$invitation->company->name}.\n\n"
            . "Accept your invitation using this link:\n{$url}\n\n"
            . "This link expires in 48 hours. If you were not expecting "
            . "this invitation, you can ignore this email.",
            function ($message) use ($invitation) {
                $message->to($invitation->email)
                    ->subject('Your Sembark invitation');
            }
        );
    }

    public function showAcceptForm(string $token)
    {
        $invitation = $this->findValidInvitation($token);

        return view('invitations.accept', compact('invitation', 'token'));
    }

    public function accept(Request $request, string $token)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = DB::transaction(function () use ($data, $token) {
            $invitation = Invitation::where(
                'token_hash',
                hash('sha256', $token)
            )->lockForUpdate()->first();

            if (
                !$invitation ||
                $invitation->accepted_at !== null ||
                $invitation->expires_at->isPast()
            ) {
                throw ValidationException::withMessages([
                    'token' => 'This invitation is invalid, expired, or already accepted.',
                ]);
            }

            if (User::where('email', $invitation->email)->exists()) {
                throw ValidationException::withMessages([
                    'email' => 'An account already exists for this email.',
                ]);
            }

            $user = User::create([
                'name' => $data['name'],
                'email' => $invitation->email,
                'password' => Hash::make($data['password']),
                'company_id' => $invitation->company_id,
                'role' => $invitation->role,
                'email_verified_at' => now(),
            ]);

            $invitation->update(['accepted_at' => now()]);

            return $user;
        });

        auth()->login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')
            ->with('success', 'Your account has been created successfully.');
    }

    private function findValidInvitation(string $token): Invitation
    {
        $invitation = Invitation::with('company')
            ->where('token_hash', hash('sha256', $token))
            ->first();

        abort_if(
            !$invitation ||
            $invitation->accepted_at !== null ||
            $invitation->expires_at->isPast(),
            410,
            'This invitation is invalid, expired, or already accepted.'
        );

        return $invitation;
    }
}

