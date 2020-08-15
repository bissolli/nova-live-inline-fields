<?php

namespace Bissolli\NovaLiveInlineFields;

use Laravel\Nova\Fields\Select;

class SelectLiveField extends Select
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'select-live-field';

    public function __construct($name, $attribute = null, callable $resolveCallback = null)
    {
        parent::__construct($name, $attribute, $resolveCallback);

        $this->withMeta([
            'displayUsingLabels' => false,
        ]);
    }

    public function displayUsingLabels()
    {
        $this->withMeta([
            'displayUsingLabels' => true,
        ]);

        return parent::displayUsingLabels();
    }

    protected function resolveAttribute($resource, $attribute)
    {
        $this->setResourceId(data_get($resource, $resource->getKeyName()));

        return parent::resolveAttribute($resource, $attribute);
    }

    protected function setResourceId($id)
    {
        return $this->withMeta([
            'id' => $id,
            'nova_path' => config('nova.path')
        ]);
    }
}
