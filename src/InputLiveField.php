<?php

namespace Bissolli\NovaLiveInlineFields;

use Laravel\Nova\Fields\Field;

class InputLiveField extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'input-live-field';

    public function __construct($name, $attribute = null, callable $resolveCallback = null)
    {
        parent::__construct($name, $attribute, $resolveCallback);

        return $this->setType('text');
    }
    
    public function setType(string $type)
    {
        return $this->withMeta([
            'type' => $type
        ]);
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
