<?php

namespace Grafxflow\ClearCacheWidget;

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
            'Grafxflow\ClearCacheWidget\ReportWidgets\ClearCache' => [
                'label' => 'grafxflow.clearcachewidget::lang.plugin.label',
                'context' => 'dashboard',
            ],
        ];
    }
}
