<?php

namespace Database\Seeders;

use App\Models\Inquiry;
use Illuminate\Database\Seeder;

class InquirySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $inquiries = [
            [
                'name' => 'John Smith',
                'email' => 'john.smith@example.com',
                'phone' => '+1234567890',
                'category' => 'Trading',
                'subject' => 'How to place a market order?',
                'message' => 'I am new to trading and would like to understand how to place a market order for stocks. Can you provide step-by-step guidance?',
                'status' => 'pending',
                'priority' => 'medium',
            ],
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah.j@example.com',
                'phone' => '+1987654321',
                'category' => 'Market Data',
                'subject' => 'Real-time data subscription',
                'message' => 'I need information about subscribing to real-time market data feeds. What are the available packages and pricing?',
                'status' => 'in_progress',
                'priority' => 'high',
            ],
            [
                'name' => 'Michael Chen',
                'email' => 'mchen@example.com',
                'phone' => null,
                'category' => 'Technical Issues',
                'subject' => 'Unable to login to trading platform',
                'message' => 'I have been trying to login to the trading platform for the past hour but keep getting an error message. Please help urgently.',
                'status' => 'resolved',
                'priority' => 'urgent',
                'resolved_at' => now()->subDays(1),
                'resolution_notes' => 'Password reset link sent. User successfully logged in.',
            ],
            [
                'name' => 'Emily Rodriguez',
                'email' => 'emily.r@example.com',
                'phone' => '+1555123456',
                'category' => 'General Questions',
                'subject' => 'Account verification process',
                'message' => 'What documents are required for account verification? How long does the verification process typically take?',
                'status' => 'pending',
                'priority' => 'low',
            ],
            [
                'name' => 'David Wilson',
                'email' => 'dwilson@example.com',
                'phone' => '+1555987654',
                'category' => 'Trading',
                'subject' => 'Stop-loss order not executed',
                'message' => 'My stop-loss order for XYZ stock was not executed even though the price reached the trigger level. Can you investigate this issue?',
                'status' => 'in_progress',
                'priority' => 'urgent',
            ],
            [
                'name' => 'Lisa Anderson',
                'email' => 'lisa.anderson@example.com',
                'phone' => '+1555246810',
                'category' => 'Market Data',
                'subject' => 'Historical data download',
                'message' => 'How can I download historical price data for the last 5 years? Is there a bulk download option available?',
                'status' => 'resolved',
                'priority' => 'medium',
                'resolved_at' => now()->subHours(3),
                'resolution_notes' => 'Provided instructions for bulk data export via API. User confirmed successful download.',
            ],
            [
                'name' => 'Robert Taylor',
                'email' => 'rtaylor@example.com',
                'phone' => null,
                'category' => 'Technical Issues',
                'subject' => 'Mobile app crashing on Android',
                'message' => 'The mobile trading app keeps crashing whenever I try to view my portfolio on my Android device (Samsung Galaxy S21).',
                'status' => 'pending',
                'priority' => 'high',
            ],
            [
                'name' => 'Jennifer Martinez',
                'email' => 'jmartinez@example.com',
                'phone' => '+1555369258',
                'category' => 'General Questions',
                'subject' => 'Trading hours and holidays',
                'message' => 'What are the regular trading hours? Is the exchange open on public holidays?',
                'status' => 'closed',
                'priority' => 'low',
                'resolved_at' => now()->subDays(2),
                'resolution_notes' => 'Provided trading hours information and holiday schedule. User satisfied.',
            ],
            [
                'name' => 'Christopher Lee',
                'email' => 'clee@example.com',
                'phone' => '+1555147258',
                'category' => 'Trading',
                'subject' => 'Margin trading requirements',
                'message' => 'I would like to start margin trading. What are the requirements and what is the maximum leverage available?',
                'status' => 'pending',
                'priority' => 'medium',
            ],
            [
                'name' => 'Amanda White',
                'email' => 'awhite@example.com',
                'phone' => '+1555789456',
                'category' => 'Market Data',
                'subject' => 'API rate limits',
                'message' => 'I am developing a trading bot and need to know what the API rate limits are for market data requests.',
                'status' => 'in_progress',
                'priority' => 'medium',
            ],
        ];

        foreach ($inquiries as $inquiry) {
            Inquiry::create($inquiry);
        }

        $this->command->info('Successfully seeded ' . count($inquiries) . ' inquiries.');
    }
}
