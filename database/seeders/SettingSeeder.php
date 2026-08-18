<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'site_name', 'value' => 'داركم'],
            ['key' => 'site_slogan', 'value' => 'خطتك الذكية لبيت أحلامك'],
            ['key' => 'site_description', 'value' => 'منصتك الموثوقة لإدارة وتطوير مشاريعك الهندسية والمقاولات بكل احترافية.'],
            ['key' => 'contact_phone', 'value' => '0999123456'],
            ['key' => 'contact_email', 'value' => 'info@darakom.sy'],
            ['key' => 'contact_address', 'value' => 'دمشق، سوريا'],
            ['key' => 'guide_intro', 'value' => 'كل ما تحتاجه من معلومات، خطوات، ونصائح لبناء مشروعك بنجاح وبأعلى معايير الجودة والتوفير.'],
            ['key' => 'guide_financial_advice', 'value' => 'وضع ميزانية دقيقة مع هامش طوارئ، المقارنة بين عروض الأسعار، والالتزام بالتصميم المعتمد.'],
            ['key' => 'guide_general_instructions', 'value' => 'السلامة أولاً، استخراج التراخيص القانونية، اختيار المقاول المعتمد، والحرص على جودة المواد.'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], ['value' => $setting['value']]);
        }
    }
}