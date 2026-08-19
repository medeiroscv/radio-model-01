<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // Homepage builder
            ['homepage', 'hero_enabled', '1', 'Destaque principal'],
            ['homepage', 'player_enabled', '1', 'Player da rádio'],
            ['homepage', 'promotions_enabled', '1', 'Promoções'],
            ['homepage', 'news_enabled', '1', 'Notícias / Entretenimento'],
            ['homepage', 'release_enabled', '1', 'Lançamento musical'],
            ['homepage', 'chart_enabled', '1', 'Mais tocadas'],
            ['homepage', 'videos_enabled', '1', 'Vídeos'],
            ['homepage', 'onair_enabled', '1', 'No ar / Programação'],
            ['homepage', 'app_enabled', '1', 'Aplicativo'],
            ['homepage', 'newsletter_enabled', '1', 'Newsletter'],

            // App section
            ['app', 'app_title', 'Baixe nosso aplicativo', 'Título da seção do aplicativo'],
            ['app', 'app_text', 'Ouça a rádio onde estiver.', 'Texto da seção do aplicativo'],
            ['app', 'app_image', null, 'Imagem do celular'],
            ['app', 'app_qr_code', null, 'QR Code'],
            ['app', 'app_google_play', null, 'Link Google Play'],
            ['app', 'app_app_store', null, 'Link App Store'],
            ['app', 'app_appgallery', null, 'Link Huawei AppGallery'],
            ['app', 'app_custom_button_label', null, 'Texto do botão personalizado'],
            ['app', 'app_custom_button_url', null, 'URL do botão personalizado'],

            // Newsletter section
            ['newsletter', 'newsletter_title', 'Fique por dentro de tudo!', 'Título da newsletter'],
            ['newsletter', 'newsletter_text', 'Receba novidades, promoções e conteúdo exclusivo.', 'Texto da newsletter'],

            // On Air section
            ['onair', 'onair_title', 'Ao vivo para todo o Brasil!', 'Título da seção No Ar'],

            // Analytics
            ['analytics', 'google_analytics_id', null, 'Google Analytics ID'],
            ['analytics', 'gtm_id', null, 'Google Tag Manager ID'],
            ['analytics', 'meta_pixel_id', null, 'Meta Pixel ID'],

            // Legal / LGPD
            ['legal', 'privacy_policy_url', null, 'URL da política de privacidade'],
            ['legal', 'cookie_policy_url', null, 'URL da política de cookies'],
            ['legal', 'cookie_consent_enabled', '1', 'Banner de consentimento de cookies'],

            // Maintenance
            ['system', 'maintenance_message', 'Estamos em manutenção. Volte em breve!', 'Mensagem de manutenção'],

            // Cron check
            ['system', 'last_cron_run', null, 'Última execução do cron'],
        ];

        foreach ($settings as [$group, $key, $value, $label]) {
            Setting::firstOrCreate(
                ['group' => $group, 'key' => $key],
                ['value' => $value, 'label' => $label]
            );
        }
    }
}