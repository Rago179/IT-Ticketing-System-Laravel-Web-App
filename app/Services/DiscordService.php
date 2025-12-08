<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class DiscordService
{
    protected $webhookUrl;

    public function __construct()
    {
        $this->webhookUrl = config('services.discord.url');
    }

    public function sendHighPriorityAlert($ticketTitle, $ticketDescription, $userName)
    {
        if (!$this->webhookUrl) {
            return;
        }

        try {
            Http::post($this->webhookUrl, [
                'content' => "🚨 **CRITICAL TICKET CREATED** 🚨",
                'embeds' => [
                    [
                        'title' => $ticketTitle,
                        'description' => substr($ticketDescription, 0, 200), 
                        'color' => 15548997,
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
        }
    }
}