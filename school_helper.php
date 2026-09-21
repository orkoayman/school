<?php

use App\Models\SchoolSettingModel;

if (! function_exists('school_info')) {
    /**
     * Returns the school's current name/logo/contact info.
     * Cached statically so it only hits the database once per request,
     * even though it's called from both the layout and the login page.
     */
    function school_info(): array
    {
        static $cached = null;

        if ($cached === null) {
            $model  = model(SchoolSettingModel::class);
            $cached = $model->getSettings();
        }

        return $cached;
    }
}

if (! function_exists('school_logo_url')) {
    /**
     * Returns a browser-usable URL for the school logo, or null if none is set.
     * Logos are stored under public/uploads/logo/ and served directly.
     */
    function school_logo_url(): ?string
    {
        $info = school_info();

        if (empty($info['logo_path'])) {
            return null;
        }

        return base_url('uploads/logo/' . $info['logo_path']);
    }
}
