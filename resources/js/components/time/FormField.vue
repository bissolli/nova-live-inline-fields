<template>
     <default-field :field="field" :errors="errors">
        <template slot="field">
            <div class="flex items-center">
                <time-picker
                    class="w-full form-control form-input form-input-bordered"
                    :class="{ 'border-danger': hasError }"
                    :id="field.attribute"
                    :field="field"
                    :placeholder="placeholder"
                    :value="value"
                    :dusk="field.attribute"
                    :disabled="isReadonly"
                    :twelveHourTime="twelveHourTime"
                    :minuteIncrement="minuteIncrement"
                    @change="handleChange"
                />
            </div>

            <div class="help-text error-text mt-2 text-danger" v-if="hasError">
                {{ firstError}}
            </div>
        </template>
    </default-field>
</template>

<script>
import TimePicker from './TimePicker'
import { FormField, HandlesValidationErrors } from 'laravel-nova'

export default {
    mixins: [FormField, HandlesValidationErrors],

    components: { TimePicker },

    props: ['resourceName', 'resourceId', 'field'],

    computed: {
        placeholder() {
            return moment(new Date()).format('HH:ss')
        },

        twelveHourTime() {
            return this.field.twelveHourTime || false;
        },

        minuteIncrement() {
            return this.field.minuteIncrement || 5;
        }
    },

    methods: {
        /*
         * Set the initial, internal value for the field.
         */
        setInitialValue() {
            this.value = this.field.value || ''
        },

        /**
         * Fill the given FormData object with the field's internal value.
         */
        fill(formData) {
            formData.append(this.field.attribute, this.value || '')
        },

        /**
         * Update the field's internal value.
         */
        handleChange(value) {
            this.value = value
        },
    },
}
</script>
