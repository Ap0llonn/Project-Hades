<?php

namespace App\Features\Dashboard\Settings\Sessions\Read;

use App\Models\ExtensionAuthToken;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

final class ListActiveSessionsHandler
{
    /**
     * @return list<array<string, mixed>>
     */
    public function handle(ListActiveSessionsQuery $query): array
    {
        $sessions = [];

        if (Schema::hasTable('sessions')) {
            $webSessions = DB::table('sessions')
                ->select(['id', 'ip_address', 'user_agent', 'last_activity'])
                ->where('user_id', $query->userId)
                ->orderByDesc('last_activity')
                ->get();

            foreach ($webSessions as $row) {
                $userAgent = (string) ($row->user_agent ?? '');
                $lastActivity = (int) ($row->last_activity ?? 0);
                $isCurrent = (string) ($row->id ?? '') === $query->currentSessionId;

                $sessions[] = [
                    'id' => (string) ($row->id ?? ''),
                    'channel' => 'web',
                    'device_name' => $this->buildDeviceName($userAgent, 'Browser Session'),
                    'browser' => $this->detectBrowser($userAgent),
                    'platform' => $this->detectPlatform($userAgent),
                    'ip_address' => (string) ($row->ip_address ?? ''),
                    'last_active_at' => $lastActivity > 0 ? now()->setTimestamp($lastActivity)->toIso8601String() : null,
                    'is_current' => $isCurrent,
                    'can_revoke' => !$isCurrent,
                ];
            }
        }

        if (Schema::hasTable('extension_auth_tokens')) {
            $extensionTokens = ExtensionAuthToken::query()
                ->where('user_id', $query->userId)
                ->whereNull('revoked_at')
                ->orderByDesc('last_used_at')
                ->orderByDesc('created_at')
                ->get();

            foreach ($extensionTokens as $token) {
                $userAgent = (string) ($token->user_agent ?? '');
                $lastActivity = $token->last_used_at ?? $token->updated_at ?? $token->created_at;

                $sessions[] = [
                    'id' => (string) $token->id,
                    'channel' => 'extension',
                    'device_name' => $this->buildDeviceName($userAgent, 'Extension Session'),
                    'browser' => $this->detectBrowser($userAgent),
                    'platform' => $this->detectPlatform($userAgent),
                    'ip_address' => (string) ($token->ip_address ?? ''),
                    'last_active_at' => optional($lastActivity)?->toIso8601String(),
                    'is_current' => false,
                    'can_revoke' => true,
                ];
            }
        }

        usort($sessions, function (array $left, array $right): int {
            $leftLastActive = strtotime((string) ($left['last_active_at'] ?? '')) ?: 0;
            $rightLastActive = strtotime((string) ($right['last_active_at'] ?? '')) ?: 0;

            return $rightLastActive <=> $leftLastActive;
        });

        return $sessions;
    }

    private function buildDeviceName(string $userAgent, string $fallback): string
    {
        $platform = $this->detectPlatform($userAgent);
        $browser = $this->detectBrowser($userAgent);

        if ($platform === 'Unknown' && $browser === 'Unknown') {
            return $fallback;
        }

        if ($platform === 'Unknown') {
            return $browser;
        }

        if ($browser === 'Unknown') {
            return $platform;
        }

        return sprintf('%s - %s', $platform, $browser);
    }

    private function detectPlatform(string $userAgent): string
    {
        $agent = strtolower($userAgent);

        return match (true) {
            str_contains($agent, 'windows') => 'Windows',
            str_contains($agent, 'mac os') || str_contains($agent, 'macintosh') => 'macOS',
            str_contains($agent, 'iphone') => 'iPhone',
            str_contains($agent, 'ipad') => 'iPad',
            str_contains($agent, 'android') => 'Android',
            str_contains($agent, 'linux') => 'Linux',
            default => 'Unknown',
        };
    }

    private function detectBrowser(string $userAgent): string
    {
        $agent = strtolower($userAgent);

        return match (true) {
            str_contains($agent, 'edg/') => 'Edge',
            str_contains($agent, 'chrome/') && !str_contains($agent, 'edg/') => 'Chrome',
            str_contains($agent, 'safari/') && !str_contains($agent, 'chrome/') => 'Safari',
            str_contains($agent, 'firefox/') => 'Firefox',
            str_contains($agent, 'opr/') || str_contains($agent, 'opera/') => 'Opera',
            default => 'Unknown',
        };
    }
}
