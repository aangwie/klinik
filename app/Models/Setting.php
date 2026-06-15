<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function getValue($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function setValue($key, $value)
    {
        return self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    public static function getAppName()
    {
        return self::getValue('app_name', 'Klinik Sehat');
    }

    public static function getAppLogo()
    {
        return self::getValue('app_logo');
    }

    public static function getAppLogoBase64()
    {
        $logo = self::getAppLogo();
        if ($logo) {
            return 'data:image/png;base64,' . $logo;
        }
        return null;
    }
}