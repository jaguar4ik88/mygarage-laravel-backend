<?php

namespace App\Console\Commands;

use App\Models\Reminder;
use Illuminate\Console\Command;

class UpdateReminderStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminders:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Обновляет статус активности напоминаний на основе даты следующего обслуживания';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Начинаем обновление статуса напоминаний...');

        // Получаем все активные напоминания
        $activeReminders = Reminder::where('is_active', true)->get();
        
        $updatedCount = 0;
        
        foreach ($activeReminders as $reminder) {
            $oldStatus = $reminder->is_active;
            $reminder->updateActiveStatus();
            
            if ($oldStatus !== $reminder->is_active) {
                $updatedCount++;
                $this->line("Напоминание ID {$reminder->id} ({$reminder->title}) - статус изменен на неактивный");
            }
        }

        $this->info("Обновлено напоминаний: {$updatedCount}");
        $this->info('Обновление статуса напоминаний завершено.');
        
        return Command::SUCCESS;
    }
}
