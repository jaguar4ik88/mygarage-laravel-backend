<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vehicle;
use App\Models\VehicleManual;

class VehicleManualSeeder extends Seeder
{
    public function run(): void
    {
        // Create default manual sections (not tied to any specific vehicle)
        $this->createDefaultManualSections();
    }

    private function createDefaultManualSections(): void
    {
        $sections = [
            [
                'section_id' => 'weekly_checks',
                'title' => 'Еженедельные проверки',
                'content' => [
                    'Проверьте уровень масла в двигателе',
                    'Проверьте уровень охлаждающей жидкости',
                    'Проверьте давление в шинах',
                    'Проверьте работу всех фар и габаритов',
                    'Проверьте уровень тормозной жидкости',
                    'Проверьте состояние аккумулятора',
                ],
                'icon' => 'calendar-week',
                'sort_order' => 1,
            ],
            [
                'section_id' => 'maintenance_schedule',
                'title' => 'Регламент ТО',
                'content' => [
                    'Замена масла: каждые 10,000 км или 12 месяцев',
                    'Замена воздушного фильтра: каждые 20,000 км',
                    'Замена топливного фильтра: каждые 30,000 км',
                    'Замена салонного фильтра: каждые 15,000 км',
                    'Проверка тормозных колодок: каждые 15,000 км',
                    'Замена свечей зажигания: каждые 40,000 км',
                ],
                'icon' => 'calendar-alt',
                'sort_order' => 2,
            ],
            [
                'section_id' => 'fluids',
                'title' => 'Жидкости',
                'content' => [
                    'Моторное масло: 5W-30 синтетическое',
                    'Охлаждающая жидкость: G12 или G12+',
                    'Тормозная жидкость: DOT 4',
                    'Жидкость ГУР: ATF Dexron III или аналог',
                    'Трансмиссионная жидкость: ATF Dexron III',
                ],
                'icon' => 'tint',
                'sort_order' => 3,
            ],
            [
                'section_id' => 'tire_pressure',
                'title' => 'Давление шин',
                'content' => [
                    'Передние шины: 2.2-2.4 бар',
                    'Задние шины: 2.0-2.2 бар',
                    'Запасное колесо: 2.5 бар',
                    'Проверяйте давление на холодных шинах',
                    'Регулируйте давление в зависимости от нагрузки',
                ],
                'icon' => 'tire',
                'sort_order' => 4,
            ],
            [
                'section_id' => 'lights',
                'title' => 'Освещение',
                'content' => [
                    'Проверьте работу ближнего света',
                    'Проверьте работу дальнего света',
                    'Проверьте работу габаритных огней',
                    'Проверьте работу стоп-сигналов',
                    'Проверьте работу указателей поворота',
                    'Проверьте работу фонарей заднего хода',
                ],
                'icon' => 'lightbulb',
                'sort_order' => 5,
            ],
            [
                'section_id' => 'emergency',
                'title' => 'Экстренные ситуации',
                'content' => [
                    'Аварийный набор: аптечка, огнетушитель, знак аварийной остановки',
                    'Телефон экстренных служб: 112',
                    'Телефон страховой компании',
                    'Номер эвакуатора',
                    'Инструкция по замене колеса',
                    'Проверка аварийного тормоза',
                ],
                'icon' => 'exclamation-triangle',
                'sort_order' => 6,
            ],
        ];

        foreach ($sections as $sectionData) {
            VehicleManual::create([
                'vehicle_id' => null, // Default manual, not tied to specific vehicle
                'section_id' => $sectionData['section_id'],
                'title' => $sectionData['title'],
                'content' => $sectionData['content'],
                'icon' => $sectionData['icon'],
                'sort_order' => $sectionData['sort_order'],
                'is_active' => true,
            ]);
        }
    }
}