<?php

namespace Database\Factories;

use App\Models\Inquiry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Inquiry>
 */
class InquiryFactory extends Factory
{
    protected $model = Inquiry::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->optional()->phoneNumber(),
            'category' => fake()->randomElement(Inquiry::CATEGORIES),
            'subject' => fake()->sentence(),
            'message' => fake()->paragraph(3),
            'status' => fake()->randomElement(Inquiry::STATUSES),
            'priority' => fake()->randomElement(Inquiry::PRIORITIES),
            'resolved_at' => null,
            'resolution_notes' => null,
        ];
    }

    /**
     * Indicate that the inquiry is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    /**
     * Indicate that the inquiry is in progress.
     */
    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'in_progress',
        ]);
    }

    /**
     * Indicate that the inquiry is resolved.
     */
    public function resolved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'resolved',
            'resolved_at' => fake()->dateTimeBetween('-30 days', 'now'),
            'resolution_notes' => fake()->paragraph(),
        ]);
    }

    /**
     * Indicate that the inquiry is closed.
     */
    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'closed',
            'resolved_at' => fake()->dateTimeBetween('-60 days', '-30 days'),
            'resolution_notes' => fake()->paragraph(),
        ]);
    }

    /**
     * Indicate that the inquiry has high priority.
     */
    public function highPriority(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => 'high',
        ]);
    }

    /**
     * Indicate that the inquiry has urgent priority.
     */
    public function urgent(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => 'urgent',
        ]);
    }

    /**
     * Indicate that the inquiry is about trading.
     */
    public function trading(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => 'Trading',
        ]);
    }

    /**
     * Indicate that the inquiry is about market data.
     */
    public function marketData(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => 'Market Data',
        ]);
    }

    /**
     * Indicate that the inquiry is about technical issues.
     */
    public function technicalIssues(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => 'Technical Issues',
        ]);
    }

    /**
     * Indicate that the inquiry is a general question.
     */
    public function generalQuestions(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => 'General Questions',
        ]);
    }
}
