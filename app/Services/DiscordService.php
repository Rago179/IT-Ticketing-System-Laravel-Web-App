<?php

namespace app\Services;

use Illuminate\Support\Facades\Http;

class DiscordService
{
    protected $webhookUrl;

    public function __construct()
    {
        // Load the URL from the config file
        $this->webhookUrl = config('services.discord.url');
    }

    public function sendHighPriorityAlert($ticketTitle, $ticketDescription, $userName)
    {
        // If no URL is set, do nothing (prevents crashes)
        if (!$this->webhookUrl) {
            return;
        }

        try {
            Http::post($this->webhookUrl, [
                'content' => "🚨 **CRITICAL TICKET CREATED** 🚨",
                'embeds' => [
                    [
                        'title' => $ticketTitle,
                        'description' => substr($ticketDescription, 0, 200), // Limit description length
                        'color' => 15548997, // Red
                        'fields' => [
                            [
                                'name' => 'Reported By', 
                                'value' => $userName, 
                                'inline' => true
                            ],
                            [
                                'name' => 'Priority', 
                                'value' => 'High (4)', 
                                'inline' => true
                            ]
                        ],
                        'footer' => [
                            'text' => 'IT Ticket System Alert'
                        ],
                        'timestamp' => now()->toIso8601String()
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            // Silently ignore errors so the user can still post their ticket
        }
    }
}