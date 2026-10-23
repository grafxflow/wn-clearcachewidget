<?php

namespace Grafxflow\ClearCacheWidget\ReportWidgets;

use Artisan;
use Backend\Classes\ReportWidgetBase;
use Illuminate\Support\Facades\Lang;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Winter\Storm\Support\Facades\Flash;

class ClearCache extends ReportWidgetBase
{
    public const CMS_CACHE_PATH = '/cms/cache';

    public const CMS_COMBINER_PATH = '/cms/combiner';

    public const CMS_TWIG_PATH = '/cms/twig';

    public const FRAMEWORK_CACHE_PATH = '/framework/cache';

    public const THUMBNAILS_PATH = '/app/uploads/public';

    public const THUMBNAILS_REGEX = '/^thumb_.*/';

    protected $defaultAlias = 'grafxflow_clear_cache';

    public function render()
    {
        $this->vars['size'] = $this->getSizes();
        $this->vars['radius'] = $this->property("radius");
        $widget = ($this->property("nochart")) ? 'widget2' : 'widget';
        return $this->makePartial($widget);
    }

    public function defineProperties()
    {
        return [
            'title' => [
                'title' => 'backend::lang.dashboard.widget_title_label',
                'default' => 'grafxflow.clearcachewidget::lang.plugin.label',
                'type' => 'string',
                'validationPattern' => '^.+$',
                'validationMessage' => 'backend::lang.dashboard.widget_title_error',
            ],
            'nochart' => [
                'title' => 'grafxflow.clearcachewidget::lang.plugin.nochart',
                'type' => 'checkbox',
            ],
            'radius' => [
                'title' => 'grafxflow.clearcachewidget::lang.plugin.radius',
                'type' => 'string',
                'validationPattern' => '^[0-9]+$',
                'validationMessage' => 'Only numbers!',
                'default' => '200',
            ],
            'delthumbs' => [
                'title' => 'grafxflow.clearcachewidget::lang.plugin.delthumbs',
                'type' => 'checkbox',
                'default' => false,
            ],
            'thumbspath' => [
                'title' => 'grafxflow.clearcachewidget::lang.plugin.delthumbspath',
                'type' => 'string',
                'placeholder' => self::THUMBNAILS_PATH,
            ],
            'thumb_regex' => [
                'title' => 'grafxflow.clearcachewidget::lang.plugin.thumbs_regex',
                'type' => 'string',
                'placeholder' => self::THUMBNAILS_REGEX,
            ],
        ];
    }

    public function onClear()
    {
        Artisan::call('cache:clear');
        if ($this->property("delthumbs")) {
            $this->delThumbs();
        }
        Flash::success(Lang::get('grafxflow.clearcachewidget::lang.plugin.success'));
        $widget = ($this->property("nochart")) ? 'widget2' : 'widget';
        return [
            'partial' => $this->makePartial(
                $widget,
                [
                    'size' => $this->getSizes(),
                    'radius' => $this->property("radius"),
                ],
            ),
        ];
    }

    private function getDirSize($directory)
    {
        if ( ! file_exists($directory) || count(scandir($directory)) <= 2) {
            return 0;
        }
        $size = 0;
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory)) as $file) {
            $size += $file->getSize();
        }
        return $size;
    }

    private function formatSize($size)
    {
        $mod = 1024;
        $units = explode(' ', 'B KB MB GB TB PB');
        for ($i = 0; $size > $mod; $i++) {
            $size /= $mod;
        }
        return round($size, 2) . ' ' . $units[$i];
    }

    private function getSizes()
    {
        $s['ccache_b'] = $this->getDirSize(storage_path() . self::CMS_CACHE_PATH);
        $s['ccache'] = $this->formatSize($s['ccache_b']);
        $s['ccombiner_b'] = $this->getDirSize(storage_path() . self::CMS_COMBINER_PATH);
        $s['ccombiner'] = $this->formatSize($s['ccombiner_b']);
        $s['ctwig_b'] = $this->getDirSize(storage_path() . self::CMS_TWIG_PATH);
        $s['ctwig'] = $this->formatSize($s['ctwig_b']);
        $s['fcache_b'] = $this->getDirSize(storage_path() . self::FRAMEWORK_CACHE_PATH);
        $s['fcache'] = $this->formatSize($s['fcache_b']);
        $s['all'] = $this->formatSize($s['ccache_b'] + $s['ccombiner_b'] + $s['ctwig_b'] + $s['fcache_b']);
        return $s;
    }

    private function delThumbs(): void
    {
        $thumbs = [];
        $path = storage_path();
        $path .= $this->property('thumbspath') ?: self::THUMBNAILS_PATH;
        $iterator = new RecursiveDirectoryIterator($path);
        $regex = $this->property('thumb_regex') ?: self::THUMBNAILS_REGEX;
        foreach (new RecursiveIteratorIterator($iterator) as $file) {
            if (preg_match($regex, $file->getFilename())) {
                $thumbs[] = $file->getRealPath();
            }
        }
        foreach ($thumbs as $img) {
            unlink($img);
        }
    }
}
