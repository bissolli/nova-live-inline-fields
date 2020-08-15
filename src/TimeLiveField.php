<?php

namespace Bissolli\NovaLiveInlineFields;

use Carbon\Carbon;
use DateTime;
use Exception;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Http\Requests\NovaRequest;

class TimeLiveField extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'time-live-field';

    /**
     * Create a new field.
     *
     * @param string      $name
     * @param string|null $attribute
     * @param mixed|null  $resolveCallback
     *
     * @return void
     */
    public function __construct($name, $attribute = null, $resolveCallback = null)
    {
        parent::__construct($name, $attribute, $resolveCallback ?? function ($value) {
            return $value === null ?
                '' :
                Carbon::createFromFormat('H:i:s', $value)->format($this->format());
        });
    }

    public function format()
    {
        return ($this->meta['twelveHourTime'] ?? false) ? 'h:i A' : 'H:i';
    }

    public function minuteIncrement($step)
    {
        return $this->withMeta(['minuteIncrement' => $step]);
    }

    public function withTwelveHourTime()
    {
        return $this->withMeta(['twelveHourTime' => true]);
    }

    /**
     * Hydrate the given attribute on the model based on the incoming request.
     *
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
     * @param string                                  $requestAttribute
     * @param object                                  $model
     * @param string                                  $attribute
     *
     * @return void
     */
    protected function fillAttributeFromRequest(
        NovaRequest $request,
        $requestAttribute,
        $model,
        $attribute
    ) {
        if ($request->exists($requestAttribute)) {
            $sentData = $request[$requestAttribute];
            if ($this->nullable && $sentData === null) {
                $model->{$attribute} = null;
            } else {
                $validatedFormat = $this->validatedTimeFormat($sentData);
                if (!$validatedFormat) {
                    throw new Exception('The field must contain a valid time.');
                }
                $newDate = Carbon::createFromFormat($validatedFormat, $sentData)->format('H:i:s');
                $model->{$attribute} = $newDate;
            }
        }
    }

    /**
     * Validate format of given time and return correct format.
     *
     * @param string $timeString
     *
     * @return mixed string|bool
     */
    protected function validatedTimeFormat($timeString)
    {
        $allowedFormats = [
            'H:i',
            'H:i:s',
            $this->format(),
        ];

        foreach ($allowedFormats as $format) {
            if (DateTime::createFromFormat($format, $timeString) !== false) {
                return $format;
            }
        }

        return false;
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
