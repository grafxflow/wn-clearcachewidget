<?php

namespace Grafxflow\ClearCacheWidget;

use Grafxflow\ClearCacheWidget\ReportWidgets\ClearCache;
use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public function pluginDetails()
    {
        return [
            'name' => 'grafxflow.clearcachewidget::lang.plugin.name',
            'description' => 'grafxflow.clearcachewidget::lang.plugin.description',
            'author' => 'Paul Coull',
            'icon' => 'icon-trash',
            'homepage' => 'https://github.com/grafxflow/wn-clearcachewidget',
        ];
    }

    public function registerReportWidgets()
    {
        return [
            ClearCache::class => [
                'label' => 'grafxflow.clearcachewidget::lang.plugin.label',
                'context' => 'dashboard',
            ],
        ];
    }
}
