<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use App\Notifications\EventReminderNotification;


class SendEventReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-event-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends event reminders to all attendees that event starts soon';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $events = \App\Models\Event::with('attendees.user')
            ->whereBetween('start_time', [now(), now()->addDay()])
            ->get();

        $eventCount = $events->count();
        $eventLabel = Str::plural('event', $eventCount);

        $this->info("Found {$eventCount} {$eventLabel} to send reminders for");

        $events->each(
            fn ($event) => $event->attendees->each(
                fn ($attendee) => $attendee->user->notify(
                    new EventReminderNotification(
                        $event
                        )
                )
            )
        );

        $this->info('Reminders sent');
    }
}